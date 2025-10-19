<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionForm;
use App\Models\RequestedEquipment;
use App\Models\RequestedFacility;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\FormStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/* 
|-------------------------------------------------------------------------- 
| RequisitionController Documentation 
|-------------------------------------------------------------------------- 
| Handles the entire requisition workflow:
| 
| - Uses Laravel session cookies to temporarily store user form data.
| - Supports adding/removing facility and equipment items.
| - Checks booking conflicts via checkAvailability() before submission.
| - Uploads temporary files (e.g., formal letter, layout) to Cloudinary.
| - On submission:
|     * Validates form data and re-checks availability.
|     * Creates a new requisition record (status: Pending Approval).
|     * Saves related requested_facilities and requested_equipment entries.
|     * Clears the session after completion.
| - Returns a success response with request_id and access_code.
|
| Note: Only equipment in "New", "Good", or "Fair" condition is bookable.
*/

class RequisitionFormController extends Controller
{
    // Get calendar events for public view (Scheduled, Ongoing, Late events only)
public function getCalendarEvents(Request $request)
{
    try {
        // Get status IDs for Scheduled, Ongoing, and Late
        $statusIds = FormStatus::whereIn('status_name', ['Scheduled', 'Ongoing', 'Late'])
            ->pluck('status_id')
            ->toArray();

        \Log::info('Fetching calendar events with status IDs:', ['status_ids' => $statusIds]);

        // Get filter parameters
        $facilityId = $request->get('facility_id');
        $equipmentId = $request->get('equipment_id');

        \Log::info('Filter parameters:', [
            'facility_id' => $facilityId,
            'equipment_id' => $equipmentId
        ]);

        $query = RequisitionForm::with([
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'purpose',
                'status'
            ])
            ->whereIn('status_id', $statusIds);

        // Filter by facility_id if provided
        if ($facilityId) {
            $query->whereHas('requestedFacilities', function($q) use ($facilityId) {
                $q->where('facility_id', $facilityId);
            });
        }

        // Filter by equipment_id if provided
        if ($equipmentId) {
            $query->whereHas('requestedEquipment', function($q) use ($equipmentId) {
                $q->where('equipment_id', $equipmentId);
            });
        }

        $events = $query->get()
            ->map(function ($requisition) {
                // Event title
                $title = $requisition->calendar_title ?: "Booking #{$requisition->request_id}";

                // Status color
                $statusColor = $requisition->status->color_code;

                // Facilities
                $facilities = $requisition->requestedFacilities->map(function ($requestedFacility) {
                    return $requestedFacility->facility->facility_name ?? 'Unknown Facility';
                })->toArray();

                // Equipment
                $equipment = $requisition->requestedEquipment->map(function ($requestedEquipment) {
                    $name = $requestedEquipment->equipment->equipment_name ?? 'Unknown Equipment';
                    $quantity = $requestedEquipment->quantity > 1 ? " × {$requestedEquipment->quantity}" : '';
                    return $name . $quantity;
                })->toArray();

                return [
                    'id' => $requisition->request_id,
                    'title' => $title,
                    'start' => $requisition->start_date . 'T' . $requisition->start_time,
                    'end' => $requisition->end_date . 'T' . $requisition->end_time,
                    'color' => $statusColor,
                    'extendedProps' => [
                        'status' => $requisition->status->status_name,
                        'requester' => $requisition->first_name . ' ' . $requisition->last_name,
                        'purpose' => $requisition->purpose->purpose_name ?? 'N/A',
                        'num_participants' => $requisition->num_participants,
                        'facilities' => $facilities,
                        'equipment' => $equipment
                    ]
                ];
            });

        \Log::info('Calendar events loaded', [
            'total_events' => $events->count(),
            'status_ids' => $statusIds,
            'facility_id_filter' => $facilityId,
            'equipment_id_filter' => $equipmentId,
            'events_by_status' => $events->groupBy('extendedProps.status')->map->count()
        ]);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);

    } catch (\Exception $e) {
        \Log::error('Calendar events error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to load calendar events: ' . $e->getMessage(),
            'data' => []
        ], 500);
    }
}

    public function activeSchedules()
    {
        $activeStatuses = [2, 3, 4]; // awaiting payment, scheduled, ongoing

        $schedules = RequisitionForm::whereIn('status_id', $activeStatuses)
            ->get(['start_date', 'start_time', 'end_date', 'end_time', 'status_id'])
            ->map(function ($schedule) {
                $schedule->start_time = substr($schedule->start_time, 0, 5); // keep HH:MM
                $schedule->end_time = substr($schedule->end_time, 0, 5);
                return $schedule;
            });

        return response()->json([
            'schedules' => $schedules
        ]);
    }

    // Common response structure
    protected function jsonResponse($success, $message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    // ----- Save form details in session ----- //
    public function saveRequestInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User information
            'user_type' => 'required|in:Internal,External',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'contact_number' => 'nullable|string|max:15',
            'organization_name' => 'nullable|string|max:100',
            'school_id' => 'nullable|string|max:20',
            // Requisition details
            'additional_requests' => 'nullable|string|max:250',
            'num_participants' => 'required|integer|min:1',
            'purpose_id' => 'required|exists:requisition_purposes,purpose_id',
            'endorser' => 'nullable|string|max:50',
            'date_endorsed' => 'nullable|date_format:Y-m-d',
            // Booking schedule
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $requestInfo = $request->only([
            'user_type',
            'first_name',
            'last_name',
            'email',
            'school_id',
            'organization_name',
            'contact_number',
            'num_participants',
            'purpose_id',
            'additional_requests',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
        ]);

        // Sanitize inputs
        $requestInfo['email'] = filter_var($requestInfo['email'], FILTER_SANITIZE_EMAIL);
        $requestInfo['first_name'] = htmlspecialchars($requestInfo['first_name'], ENT_QUOTES);
        $requestInfo['last_name'] = htmlspecialchars($requestInfo['last_name'], ENT_QUOTES);

        session(['request_info' => $requestInfo]);

        return $this->jsonResponse(true, 'Form details saved successfully.', ['request_info' => $requestInfo]);
    }

    // ----- Add items to session ----- //
    public function addToForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required_without:equipment_id|exists:facilities,facility_id',
            'equipment_id' => 'required_without:facility_id|exists:equipment,equipment_id',
            'type' => 'required|in:facility,equipment',
            'quantity' => 'required_if:type,equipment|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        try {
            $selectedItems = Session::get('selected_items', []);
            $type = $request->type;
            $idField = $type . '_id';
            $id = $request->input($idField);
            $quantity = $request->quantity ?? 1;

            // Check for duplicate item
            $existingIndex = collect($selectedItems)->search(function ($item) use ($id, $type, $idField) {
                return isset($item[$idField]) && $item[$idField] == $id && $item['type'] === $type;
            });

            if ($existingIndex !== false) {
                if ($type === 'equipment') {
                    $selectedItems[$existingIndex]['quantity'] = $quantity;
                    // Recalculate total fee for equipment
                    $selectedItems[$existingIndex]['total_fee'] = $selectedItems[$existingIndex]['external_fee'] * $quantity;
                    Session::put('selected_items', $selectedItems);
                    return $this->jsonResponse(true, 'Equipment quantity updated.', [
                        'selected_items' => $selectedItems,
                        'cart_count' => count($selectedItems)
                    ]);
                }
                return $this->jsonResponse(false, 'This item is already in your requisition.', [], 422);
            }

            if (count($selectedItems) >= 10) {
                return $this->jsonResponse(false, 'Maximum item limit (10) reached.', [], 422);
            }

            // Get item details
            if ($type === 'facility') {
                $item = Facility::with(['images', 'category', 'status'])->find($id);
            } else {
                $item = Equipment::with(['images', 'category', 'status'])->find($id);
            }

            if (!$item) {
                return $this->jsonResponse(false, 'Item not found.', [], 404);
            }

            $newItem = [
                'type' => $type,
                $idField => $id,
                'quantity' => $quantity,
                'name' => $type === 'facility' ? $item->facility_name : $item->equipment_name,
                'description' => $item->description,
                'external_fee' => $item->external_fee,
                'total_fee' => $type === 'equipment' ? $item->external_fee * $quantity : $item->external_fee,
                'rate_type' => $item->rate_type,
                'images' => $item->images->toArray(),
                'added_at' => now()->toDateTimeString()
            ];

            $selectedItems[] = $newItem;
            Session::put('selected_items', $selectedItems);

            return $this->jsonResponse(true, ucfirst($type) . ' added successfully.', [
                'selected_items' => $selectedItems,
                'cart_count' => count($selectedItems)
            ]);

        } catch (\Exception $e) {
            Log::error('Add to form error: ' . $e->getMessage());
            return $this->jsonResponse(false, 'An error occurred.', [], 500);
        }
    }

    // Add this method to your controller for fee calculation
    public function calculateFeeBreakdown(Request $request)
    {
        try {
            $selectedItems = Session::get('selected_items', []);
            $requestInfo = Session::get('request_info', []);

            if (empty($selectedItems)) {
                return $this->jsonResponse(false, 'No items in cart.', [], 400);
            }

            if (empty($requestInfo)) {
                return $this->jsonResponse(false, 'Schedule information not found.', [], 400);
            }

            $totalFee = 0;
            $breakdown = [];

            foreach ($selectedItems as $item) {
                $itemFee = $item['external_fee'];

                // Calculate duration in hours for hourly rates
                if ($item['rate_type'] === 'Per Hour') {
                    $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $requestInfo['start_date'] . ' ' . $requestInfo['start_time']);
                    $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $requestInfo['end_date'] . ' ' . $requestInfo['end_time']);
                    $durationHours = $endDateTime->diffInHours($startDateTime);

                    if ($item['type'] === 'equipment') {
                        $itemFee = $item['external_fee'] * $item['quantity'] * $durationHours;
                    } else {
                        $itemFee = $item['external_fee'] * $durationHours;
                    }
                } else if ($item['type'] === 'equipment') {
                    // Per Event rate for equipment: multiply by quantity only
                    $itemFee = $item['external_fee'] * $item['quantity'];
                }

                $breakdown[] = [
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'quantity' => $item['quantity'] ?? 1,
                    'rate_type' => $item['rate_type'],
                    'fee_per_unit' => $item['external_fee'],
                    'total_fee' => $itemFee
                ];

                $totalFee += $itemFee;
            }

            // Store fee summary in session for later use
            Session::put('fee_summary', [
                'breakdown' => $breakdown,
                'total_fee' => $totalFee
            ]);

            return $this->jsonResponse(true, 'Fee breakdown calculated.', [
                'breakdown' => $breakdown,
                'total_fee' => $totalFee
            ]);

        } catch (\Exception $e) {
            Log::error('Fee calculation error: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error calculating fees.', [], 500);
        }
    }


    // ----- Remove items from session ----- //
    public function removeFromForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required_without:equipment_id|exists:facilities,facility_id',
            'equipment_id' => 'required_without:facility_id|exists:equipment,equipment_id',
            'type' => 'required|in:facility,equipment'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        try {
            $selectedItems = Session::get('selected_items', []);
            $type = $request->type;
            $idField = $type . '_id'; // Create the correct field name
            $id = $request->input($idField);

            $updatedItems = array_values(array_filter(
                $selectedItems,
                fn($item) => !(isset($item[$idField]) && $item[$idField] == $id && $item['type'] === $type)
            ));

            Session::put('selected_items', $updatedItems);

            return $this->jsonResponse(true, ucfirst($type) . ' removed successfully.', [
                'selected_items' => $updatedItems,
                'cart_count' => count($updatedItems)
            ]);

        } catch (\Exception $e) {
            Log::error('Cart removal error: ' . $e->getMessage());
            return $this->jsonResponse(false, 'An error occurred while removing item.', [], 500);
        }
    }

    // Updated getItems method
    public function getItems(Request $request)
    {
        $selectedItems = Session::get('selected_items', []);

        // Ensure consistent data structure
        $formattedItems = array_map(function ($item) {
            $base = [
                'type' => $item['type'],
                'name' => $item['name'],
                'description' => $item['description'],
                'external_fee' => $item['external_fee'],
                'rate_type' => $item['rate_type'],
                'images' => $item['images'],
            ];

            if ($item['type'] === 'facility') {
                $base['facility_id'] = $item['facility_id'];
            } else {
                $base['equipment_id'] = $item['equipment_id'];
                $base['quantity'] = $item['quantity'] ?? 1;
            }

            return $base;
        }, $selectedItems);

        return response()->json([
            'success' => true,
            'data' => [
                'selected_items' => $formattedItems
            ]
        ]);
    }
    // ----- Check for booking schedule conflicts ----- //
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:facility,equipment',
            'items.*.facility_id' => 'required_if:items.*.type,facility|exists:facilities,facility_id',
            'items.*.equipment_id' => 'required_if:items.*.type,equipment|exists:equipment,equipment_id',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $requestStart = Carbon::createFromFormat('Y-m-d H:i', $request->start_date . ' ' . $request->start_time);
        $requestEnd = Carbon::createFromFormat('Y-m-d H:i', $request->end_date . ' ' . $request->end_time);

        if ($request->start_date === $request->end_date && $requestStart >= $requestEnd) {
            return $this->jsonResponse(false, 'End time must be after start time for the same day.', [], 422);
        }


        $conflictStatusIds = FormStatus::whereIn('status_name', ['Awaiting Payment', 'Scheduled', 'Ongoing'])
            ->pluck('status_id')
            ->toArray();

        $conflicts = false;
        $conflictItems = [];

        foreach ($request->items as $item) {
            $query = RequisitionForm::whereIn('status_id', $conflictStatusIds)
                ->where(function ($q) use ($requestStart, $requestEnd) {
                    // Check for date overlap first (optimization)
                    $q->where(function ($dateQ) use ($requestStart, $requestEnd) {
                        $dateQ->where('start_date', '<=', $requestEnd->format('Y-m-d'))
                            ->where('end_date', '>=', $requestStart->format('Y-m-d'));
                    })
                        // Then check for time overlap (excluding edge cases where one ends when another starts)
                        ->where(function ($timeQ) use ($requestStart, $requestEnd) {
                        $timeQ->where(function ($inner) use ($requestStart, $requestEnd) {
                            $inner->where('start_time', '<', $requestEnd->format('H:i'))
                                ->where('end_time', '>', $requestStart->format('H:i'));
                        });
                    });
                });

            if ($item['type'] === 'facility') {
                $query->whereHas('requestedFacilities', function ($q) use ($item) {
                    $q->where('facility_id', $item['facility_id']);
                });
            } else {
                $query->whereHas('requestedEquipment', function ($q) use ($item) {
                    $q->where('equipment_id', $item['equipment_id']);
                });
            }

            if ($query->exists()) {
                $conflicts = true;
                $itemName = $item['type'] === 'facility'
                    ? Facility::find($item['facility_id'])->facility_name
                    : Equipment::find($item['equipment_id'])->equipment_name;

                $conflictItems[] = [
                    'type' => $item['type'],
                    'id' => $item[$item['type'] . '_id'],
                    'name' => $itemName
                ];
            }
        }

        return $this->jsonResponse(
            true,
            $conflicts ? 'Time slot conflicts with existing booking(s).' : 'Time slot is available.',
            [
                'available' => !$conflicts,
                'conflict_items' => $conflictItems
            ]
        );
    }

    public function tempUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formal_letter_url' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'facility_layout_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Log session before upload
            \Log::debug('Pre-upload session data', ['session' => session()->all()]);

            $field = $request->hasFile('formal_letter_url') ? 'formal_letter_url' : 'facility_layout_url';
            $file = $request->file($field);

            $folder = $field === 'formal_letter_url'
                ? 'user-uploads/user-letters'
                : 'user-uploads/user-setups';

            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'auto',
            ]);

            if (!$upload->getSecurePath()) {
                throw new \Exception('Cloudinary upload failed.');
            }

            $uploadToken = Str::random(40);

            // Store upload in session with clear structure
            $tempUploads = session('temp_uploads', []);
            $tempUploads[$field] = [
                'url' => $upload->getSecurePath(),
                'public_id' => $upload->getPublicId(),
                'token' => $uploadToken,
                'type' => $field === 'formal_letter_url' ? 'Letter' : 'Setup'
            ];
            session(['temp_uploads' => $tempUploads]);

            // Log session after upload
            \Log::debug('Post-upload session data', ['session' => session()->all()]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'data' => $tempUploads[$field],
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ----- Submit requisition form with overbooking protection ----- //
    public function submitForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:Internal,External',
            'school_id' => 'required_if:user_type,Internal|nullable|string|max:20',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'contact_number' => ['nullable', 'regex:/^\d{1,15}$/', 'max:15'],
            'organization_name' => 'nullable|string|max:100',
            'num_participants' => 'required|integer|min:1',
            'purpose_id' => 'required|exists:requisition_purposes,purpose_id',
            'additional_requests' => 'nullable|string|max:250',
            'endorser' => 'nullable|string|max:50',
            'date_endorsed' => 'nullable|date_format:Y-m-d',
            'formal_letter_url' => 'required|url',
            'formal_letter_public_id' => 'required|string|max:255',
            'facility_layout_url' => 'nullable|string|max:255',
            'facility_layout_public_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray() // Return full error details
            ], 422);
        }

        DB::beginTransaction();

        try {

            // Get selected items from session (still needed)
            $selectedItems = session('selected_items', []);
            if (empty($selectedItems)) {
                throw new \Exception('Your booking cart is empty. Add items before submitting.');
            }

            // Debug session data
            \Log::debug('Submission session data', [
                'request_info' => session('request_info'),
                'selected_items' => session('selected_items'),
                'temp_uploads' => session('temp_uploads'),
                'all_session' => session()->all()
            ]);

            $requestInfo = session('request_info');
            $selectedItems = session('selected_items', []);
            $tempUploads = session('temp_uploads', []);

            if (!$request->first_name || !$request->last_name || !$request->email) {
                throw new \Exception('User information not found. Please fill in all required fields.');
            }

            $conflictCheck = $this->checkAvailability(new Request([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'items' => array_map(function ($item) {
                    return [
                        'type' => $item['type'],
                        $item['type'] . '_id' => $item[$item['type'] . '_id']
                    ];
                }, $selectedItems)
            ]));

            $conflictData = $conflictCheck->getData();
            if (!$conflictData->success || !$conflictData->data->available) {
                throw new \Exception($conflictData->message ?? 'Time slot no longer available. Please choose another.');
            }

            // Get uploads - handle null case explicitly
            if (!$request->formal_letter_url) {
                throw new \Exception('A formal letter must be uploaded.');
            }

            $accessCode = Str::upper(Str::random(10));
            \Log::debug('Generated access code', ['code' => $accessCode]);
            \Log::debug('Submitting form data', [
                'facility_layout_url' => $request->facility_layout_url,
                'facility_layout_public_id' => $request->facility_layout_public_id,
                'all_data' => $request->all()
            ]);


            // Create requisition form
            $requisitionForm = RequisitionForm::create([
                'user_type' => $request->user_type, // Use submitted value
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'organization_name' => $request->organization_name,
                'school_id' => $request->school_id,
                'access_code' => $accessCode,
                'purpose_id' => $request->purpose_id,
                'num_participants' => $request->num_participants,
                'additional_requests' => $request->additional_requests,
                'formal_letter_url' => $request->formal_letter_url,
                'formal_letter_public_id' => $request->formal_letter_public_id,
                'facility_layout_url' => $request->facility_layout_url ?? null,
                'facility_layout_public_id' => $request->facility_layout_public_id ?? null,
                'upload_token' => Str::random(40), // assume same token used for both
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status_id' => FormStatus::where('status_name', 'Pending Approval')->value('status_id'),
                'tentative_fee' => session('fee_summary.total_fee', 0),
            ]);

            // Save selected items
            foreach ($selectedItems as $item) {
                if ($item['type'] === 'facility') {
                    RequestedFacility::create([
                        'request_id' => $requisitionForm->request_id,
                        'facility_id' => $item['facility_id'] ?? $item['id'], // Handle both formats
                        'is_waived' => false,
                    ]);
                } elseif ($item['type'] === 'equipment') {
                    $equipmentItems = \App\Models\EquipmentItem::where('equipment_id', $item['equipment_id'] ?? $item['id'])
                        ->whereIn('condition_id', [1, 2, 3])
                        ->limit($item['quantity'] ?? 1)
                        ->get();

                    if ($equipmentItems->count() < ($item['quantity'] ?? 1)) {
                        throw new \Exception("Not enough items available for equipment ID {$item['id']}.");
                    }

                    foreach ($equipmentItems as $equipmentItem) {
                        RequestedEquipment::create([
                            'request_id' => $requisitionForm->request_id,
                            'equipment_id' => $item['equipment_id'],
                            'item_id' => $equipmentItem->item_id,
                            'is_waived' => false,
                        ]);

                        DB::table('equipment_items')
                            ->where('item_id', $equipmentItem->item_id)
                        ;
                    }
                }
            }

            // Send confirmation email to requester
            $this->sendConfirmationEmail($requisitionForm);

            // Notify admins about new requisition
            \App\Services\NotificationService::notifyNewRequisition($requisitionForm);

            // Clear session
            session()->forget(['request_info', 'selected_items', 'fee_summary', 'temp_uploads']);

            DB::commit();

            return $this->jsonResponse(true, 'Requisition submitted successfully!', [
                'access_code' => $requisitionForm->access_code,
                'request_id' => $requisitionForm->request_id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Submission error: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Submission failed: ' . $e->getMessage(), [], 500);
        }
    }

protected function sendConfirmationEmail(RequisitionForm $requisitionForm)
{
    try {
        $subject = 'CPU Booking System - Requisition Form Received';

        $emailData = [
            'first_name' => $requisitionForm->first_name,
            'last_name' => $requisitionForm->last_name,
            'access_code' => $requisitionForm->access_code,
        ];

        // Use the blade template instead of raw text
        \Mail::send('emails.booking-confirmation', $emailData, function ($message) use ($requisitionForm, $subject) {
            $message->to($requisitionForm->email)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });

        \Log::info('Confirmation email sent to: ' . $requisitionForm->email);

    } catch (\Exception $e) {
        \Log::error('Failed to send confirmation email: ' . $e->getMessage());
        // Don't throw exception here - email failure shouldn't prevent form submission
    }
}

    public function clearSession()
    {
        session()->forget(['request_info', 'selected_items', 'fee_summary', 'temp_uploads']);
        return response()->json(['success' => true]);
    }

}