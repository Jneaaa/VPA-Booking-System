<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionForm;
use App\Models\RequestedEquipment;
use App\Models\RequestedFacility;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\FormStatus;
use App\Models\AvailabilityStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

/* CONTROLLER DOCUMENTATION (expand to view):

Lookup Tables:

    AvailabilityStatus Model (status_id in availability_statuses table):
    '1', 'Available', '#28a745', '1'
    '2', 'Unavailable', '#dc3545', '1'
    '3', 'Under Maintenance', '#ffc107', '1'
    '4', 'Reserved', '#007bff', '1'
    '5', 'Hidden', '#343a40', '1'

    FormStatus Model (status_id in form_statuses table):
    '1', 'Pending Approval', '#FFA500'
    '2', 'In Review', '#00BFFF'
    '3', 'Awaiting Payment', '#FF69B4'
    '4', 'Scheduled', '#9370DB'
    '5', 'Ongoing', '#1E90FF'
    '6', 'Returned', '#20B2AA'
    '7', 'Late Return', '#DC143C'
    '8', 'Completed', '#32CD32'
    '9', 'Rejected', '#B22222'
    '10', 'Cancelled', '#A9A9A9'

    RequisitionPurpose Model (purpose_id in requisition_purposes table):
    '8', 'Alumni - Class Reunion'
    '9', 'Alumni - Personal Events'
    '7', 'Alumni-Organized Events'
    '5', 'CPU Organization Led Activity'
    '2', 'Equipment Rental'
    '10', 'External Event'
    '1', 'Facility Rental'
    '6', 'Student-Organized Activity'
    '3', 'Subject Requirement - Class, Seminar, Conference'
    '4', 'University Program/Activity'

    Condition Model (condition_id in conditions table):
    '1', 'New', '#28a745'
    '2', 'Good', '#20c997'
    '3', 'Fair', '#ffc107'
    '4', 'Needs Maintenance', '#fd7e14'
    '5', 'Damaged', '#dc3545'
    '6', 'In Use', '#6f42c1'

    Note: Condition applies only to equipment. This is used to determine what equipment_item is available for booking. Only New, good, and fair conditions are available for booking.

User should fill in these fields (Upon form submission, the following data will be saved in DB: requisition_forms table):

    (this info is copied from app/Models/RequisitionForm.php)

    protected $fillable = [

        // User information
        'user_type', // 'Internal' or 'External'
        'first_name', // required
        'last_name', // required
        'email', // required
        'school_id', // null for external, required for internal users
        'organization_name',  // optional
        'contact_number', // optional

        // Requisition details
        'num_participants', // required, must be at least 1
        'purpose_id', // this is the purpose of the requisition, e.g., 'Facility Rental', 'Equipment Rental', etc.
        'additional_requests', // optional, additional requests or notes
        'endorser', // optional, name of the endorser (if applicable)
        'date_endorsed', // optional, date endorsed by the endorser (if applicable)

        // User uploads. Formal letter is required, facility layout is optional. Acceptable formats: png, jpg, jpeg, pdf.

        'formal_letter_url', // this is the formal letter uploaded by the user.
        'formal _letter_public_id', // this is the public ID of the formal letter uploaded to Cloudinary.
        'facility_layout_url', // this is the facility layout uploaded by the user.
        'facility_layout_public_id', // this is the public ID of the facility layout uploaded to
        'upload_token', // this is a unique token generated for the upload, used to reference the file later.

        // Booking schedule. this must NOT conflict with existing bookings. Ensure no overlapping dates/times in the controller logic. 

        'start_date',
        'end_date',
        'start_time',
        'end_time',

        // Requested items. This will be saved in the requested_facilities and requested_equipment tables:

        'requested_facilities', // this is an array of facility IDs requested by the user.
        'requested_equipment', // this is an array of equipment IDs requested by the user.

        // Requisition status tracking. Left null if not applicable, status_id will be set to 'Pending Approval' (1) upon submission.

        'status_id', // this is the status of the requisition form, e.g., 'Pending Approval', 'In Review', etc.
        'is_late', // this is a boolean field that indicates if the requisition form is late. Set to true if the form is submitted after the end date.
        'late_penalty_fee', // this is the late penalty fee manually applied by the admins if the form is late.
        'returned_at', date and time that the requested_equipment was returned.
        'is_finalized', // this is a boolean field that indicates if the requisition form is finalized. Set to true if the form is finalized by an admin.
        'finalized_at', // date and time that the requisition form was finalized.
        'finalized_by', // this is the ID of the admin who finalized the requisition form.
        'is_closed', // this is a boolean field that indicates if the requisition form is closed. Set to true if the form is closed by an admin.
        'closed_at', // date and time that the requisition form was closed.
        'closed_by', // this is the ID of the admin who closed the requisition form.
        'official_receipt_no', this is the official receipt number issued by the admin upon finalization of the requisition form.
        'official_receipt_url', // this is the official receipt URL uploaded by the admin upon finalization of the requisition form.
        'official_receipt_public_id', // this is the public ID of the official receipt uploaded to Cloudinary.
        'calendar_title', // this is the title of the calendar event created for the requisition form.
        'calendar_description', // this is the description of the calendar event created for the requisition form.

        'tentative_fee', // this is the total calculated fee of all selected items upon submission.
        'approved_fee' // this is the final approved fee after review. left null for now, until the admins have finalized the requisition form and set the approved fee.

    ];

    protected $casts = [
        'start_date' => 'string',
        'end_date' => 'string',
        'start_time' => 'string',
        'end_time' => 'string',
        'returned_at' => 'datetime',
        'finalized_at' => 'datetime',
        'closed_at' => 'datetime',
        'date_endorsed' => 'datetime',
        'is_late' => 'boolean',
        'is_finalized' => 'boolean',
        'is_closed' => 'boolean',
        'tentative_fee' => 'decimal:2',
        'approved_fee' => 'decimal:2',
    ];  

*/


class RequisitionFormController extends Controller
{
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
            'quantity' => 'required_if:type,equipment|integer|min:1'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $selectedItems = Session::get('selected_items', []);
        $id = $request->input($request->type . '_id');
        $type = $request->type;
        $quantity = $request->quantity ?? 1;

        // Check for duplicate item
        $existingIndex = collect($selectedItems)->search(function ($item) use ($id, $type) {
            return $item['id'] == $id && $item['type'] === $type;
        });

        if ($existingIndex !== false) {
            // Update quantity if equipment
            if ($type === 'equipment') {
                $selectedItems[$existingIndex]['quantity'] = $quantity;
                Session::put('selected_items', $selectedItems);
                return $this->jsonResponse(true, 'Equipment quantity updated.', [
                    'selected_items' => $selectedItems
                ]);
            }
            return $this->jsonResponse(false, 'This item is already in your requisition.', [], 422);
        }

        // Check maximum items limit
        if (count($selectedItems) >= 10) {
            return $this->jsonResponse(false, 'Maximum item limit (10) reached.', [], 422);
        }

        // Add new item
        $newItem = [
            'id' => $id,
            'type' => $type,
            'quantity' => $quantity,
            'added_at' => now()->toDateTimeString()
        ];

        $selectedItems[] = $newItem;
        Session::put('selected_items', $selectedItems);

        return $this->jsonResponse(true, ucfirst($type) . ' added successfully.', [
            'selected_items' => $selectedItems
        ]);
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

        $selectedItems = Session::get('selected_items', []);
        $id = $request->input($request->type . '_id');
        $type = $request->type;

        $updatedItems = array_values(array_filter(
            $selectedItems,
            fn($item) => !($item['id'] == $id && $item['type'] === $type)
        ));

        session(['selected_items' => $updatedItems]);

        return $this->jsonResponse(true, ucfirst($type) . ' removed successfully.', [
            'selected_items' => $updatedItems
        ]);
    }

    // ----- Calculate total fee of added items ----- //
    public function calculateFees()
    {
        $selectedItems = session('selected_items', []);
        $userType = session('request_info.user_type', 'Internal');

        $feeField = $userType === 'Internal' ? 'internal_fee' : 'external_fee';

        $facilityTotalFee = collect($selectedItems)
            ->where('type', 'facility')
            ->reduce(function ($total, $item) use ($feeField) {
                $facility = Facility::find($item['id']);
                return $total + ($facility->$feeField ?? 0) * ($item['quantity'] ?? 1);
            }, 0);

        $equipmentTotalFee = collect($selectedItems)
            ->where('type', 'equipment')
            ->reduce(function ($total, $item) use ($feeField) {
                $equipment = Equipment::find($item['id']);
                return $total + ($equipment->$feeField ?? 0) * ($item['quantity'] ?? 1);
            }, 0);

        $totalFee = $facilityTotalFee + $equipmentTotalFee;

        $feeSummary = compact('facilityTotalFee', 'equipmentTotalFee', 'totalFee');
        session(['fee_summary' => $feeSummary]);

        return $this->jsonResponse(true, 'Fees calculated successfully.', [
            'fee_summary' => $feeSummary,
            'user_type' => $userType
        ]);
    }


    // ----- Check for booking schedule conflicts ----- //
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'facility_id' => 'nullable|exists:facilities,facility_id',
            'equipment_id' => 'nullable|exists:equipment,equipment_id',
            'request_id' => 'sometimes|exists:requisition_forms,request_id'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $requestStart = Carbon::createFromFormat('Y-m-d H:i', $request->start_date . ' ' . $request->start_time);
        $requestEnd = Carbon::createFromFormat('Y-m-d H:i', $request->end_date . ' ' . $request->end_time);

        if ($requestStart >= $requestEnd) {
            return $this->jsonResponse(false, 'End time must be after start time.', [], 422);
        }

        $conflictStatusIds = FormStatus::whereIn('status_name', ['Awaiting Payment', 'Scheduled', 'Ongoing'])
            ->pluck('status_id')
            ->toArray();

        $conflicts = false;
        $conflictItems = [];

        // If facility_id or equipment_id is provided, check just that one
        if ($request->filled('facility_id') || $request->filled('equipment_id')) {
            $query = RequisitionForm::where(function ($query) use ($request, $requestStart, $requestEnd, $conflictStatusIds) {
                $query->where(function ($q) use ($requestStart, $requestEnd) {
                    $q->where(function ($dateQ) use ($requestStart, $requestEnd) {
                        $dateQ->where('start_date', '<=', $requestEnd->format('Y-m-d'))
                            ->where('end_date', '>=', $requestStart->format('Y-m-d'));
                    });
                })
                    ->where(function ($q) use ($requestStart, $requestEnd) {
                        $q->where(function ($inner) use ($requestStart, $requestEnd) {
                            // Allow partial overlaps and edge cases
                            $inner->where('start_time', '<', $requestEnd->format('H:i'))
                                ->where('end_time', '>', $requestStart->format('H:i'));
                        });
                    })
                    ->whereIn('status_id', $conflictStatusIds);
            });

            if ($request->has('request_id')) {
                $query->where('request_id', '!=', $request->request_id);
            }
            if ($request->filled('facility_id')) {
                $query->whereHas('requestedFacilities', function ($q) use ($request) {
                    $q->where('facility_id', $request->facility_id);
                });
            }
            if ($request->filled('equipment_id')) {
                $query->whereHas('requestedEquipment', function ($q) use ($request) {
                    $q->where('equipment_id', $request->equipment_id);
                });
            }

            $conflicts = $query->exists();
            if ($conflicts) {
                $conflictItems[] = [
                    'type' => $request->filled('facility_id') ? 'facility' : 'equipment',
                    'id' => $request->facility_id ?? $request->equipment_id
                ];
            }
        } else {
            // Check all items in the cart/session
            $selectedItems = session('selected_items', []);
            foreach ($selectedItems as $item) {
                $query = RequisitionForm::where(function ($query) use ($requestStart, $requestEnd, $conflictStatusIds) {
                    $query->where(function ($q) use ($requestStart, $requestEnd) {
                        $q->where(function ($dateQ) use ($requestStart, $requestEnd) {
                            $dateQ->where('start_date', '<=', $requestEnd->format('Y-m-d'))
                                ->where('end_date', '>=', $requestStart->format('Y-m-d'));
                        });
                    })
                        ->where(function ($q) use ($requestStart, $requestEnd) {
                            $q->where(function ($inner) use ($requestStart, $requestEnd) {
                                // Allow partial overlaps and edge cases
                                $inner->where('start_time', '<', $requestEnd->format('H:i'))
                                    ->where('end_time', '>', $requestStart->format('H:i'));
                            });
                        })
                        ->whereIn('status_id', $conflictStatusIds);
                });

                if ($request->has('request_id')) {
                    $query->where('request_id', '!=', $request->request_id);
                }
                if ($item['type'] === 'facility') {
                    $query->whereHas('requestedFacilities', function ($q) use ($item) {
                        $q->where('facility_id', $item['id']);
                    });
                }
                if ($item['type'] === 'equipment') {
                    $query->whereHas('requestedEquipment', function ($q) use ($item) {
                        $q->where('equipment_id', $item['id']);
                    });
                }

                if ($query->exists()) {
                    $conflicts = true;
                    $conflictItems[] = $item;
                }
            }
        }

        return $this->jsonResponse(
            true,
            $conflicts ? 'Time slot conflicts with an existing booking.' : 'Time slot is available.',
            [
                'available' => !$conflicts,
                'conflict_items' => $conflictItems
            ]
        );
    }

    // ----- Temporary user uploads before submission ----- //

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

            \Log::debug('Cloudinary Config:', [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ]);

            // Determine which field was uploaded
            $field = $request->hasFile('formal_letter_url') ? 'formal_letter_url' : 'facility_layout_url';
            $file = $request->file($field);

            // Define upload folder based on field
            $folder = $field === 'formal_letter_url'
                ? 'user-uploads/user-letters'
                : 'user-uploads/user-setups';

            // Upload to Cloudinary
            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'auto',
            ]);

            if (!$upload->getSecurePath()) {
                throw new \Exception('Cloudinary upload failed.');
            }

            // Generate a unique token for this upload
            $uploadToken = Str::random(40);

            // Store in session (to reference later when saving to DB)
            $tempUploads = session('temp_uploads', []);
            $tempUploads[$field] = [
                'file_url' => $upload->getSecurePath(),
                'public_id' => $upload->getPublicId(),
                'upload_token' => $uploadToken,
            ];
            session(['temp_uploads' => $tempUploads]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'data' => [
                    'field' => $field,
                    'file_url' => $upload->getSecurePath(),
                    'public_id' => $upload->getPublicId(),
                    'upload_token' => $uploadToken,
                ],
            ]);
        } catch (\Exception $e) {
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'purpose_id' => 'required|exists:requisition_purposes,purpose_id',
            'num_participants' => 'required|integer|min:1',
            'additional_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $requestInfo = session('request_info');
            $selectedItems = session('selected_items', []);
            $tempUploads = session('temp_uploads', []);

            if (!$requestInfo) {
                throw new \Exception('User information not found. Please fill in all required fields.');
            }

            if (empty($selectedItems)) {
                throw new \Exception('Your booking cart is empty. Add items before submitting.');
            }

            $conflictCheck = $this->checkAvailability($request);
            if (!$conflictCheck->getData()->available) {
                throw new \Exception('Time slot no longer available. Please choose another.');
            }

            // Match uploads from session
            $letterUpload = collect($tempUploads)->firstWhere('type', 'Letter');
            $setupUpload = collect($tempUploads)->firstWhere('type', 'Setup');

            if (!$letterUpload) {
                throw new \Exception('A formal letter must be uploaded.');
            }

            // Create requisition form
            $requisitionForm = RequisitionForm::create([
                'user_type' => $requestInfo['user_type'],
                'first_name' => $requestInfo['first_name'],
                'last_name' => $requestInfo['last_name'],
                'email' => $requestInfo['email'],
                'contact_number' => $requestInfo['contact_number'] ?? null,
                'organization_name' => $requestInfo['organization_name'] ?? null,
                'school_id' => $requestInfo['school_id'] ?? null,
                'access_code' => Str::upper(Str::random(10)),
                'purpose_id' => $request->purpose_id,
                'num_participants' => $request->num_participants,
                'additional_requests' => $request->additional_requests,
                'formal_letter_url' => $letterUpload['url'],
                'formal_letter_public_id' => $letterUpload['public_id'],
                'facility_layout_url' => $setupUpload['url'],
                'facility_layout_public_id' => $setupUpload['public_id'],
                'upload_token' => $letterUpload['token'], // assume same token used for both
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
                        'facility_id' => $item['id'],
                        'is_waived' => false,
                    ]);
                } elseif ($item['type'] === 'equipment') {
                    $equipmentItems = \App\Models\EquipmentItem::where('equipment_id', $item['id'])
                        ->whereIn('condition_id', [1, 2, 3])
                        ->whereNull('deleted_at')
                        ->limit($item['quantity'] ?? 1)
                        ->get();

                    if ($equipmentItems->count() < ($item['quantity'] ?? 1)) {
                        throw new \Exception("Not enough items available for equipment ID {$item['id']}.");
                    }

                    foreach ($equipmentItems as $equipmentItem) {
                        RequestedEquipment::create([
                            'request_id' => $requisitionForm->request_id,
                            'equipment_id' => $item['id'],
                            'item_id' => $equipmentItem->item_id,
                            'is_waived' => false,
                        ]);

                        DB::table('equipment_items')
                            ->where('item_id', $equipmentItem->item_id)
                            ->update(['deleted_at' => now()]);
                    }
                }
            }

            // Clear session
            session()->forget(['request_info', 'selected_items', 'fee_summary', 'temp_uploads']);

            DB::commit();

            return $this->jsonResponse(true, 'Requisition submitted successfully!', [
                'access_code' => $requisitionForm->access_code,
                'request_id' => $requisitionForm->request_id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(false, 'Submission failed: ' . $e->getMessage(), [], 500);
        }
    }
}