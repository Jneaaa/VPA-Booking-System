<?php

namespace App\Http\Backups\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\RequisitionForm;
use App\Models\RequestedEquipment;
use App\Models\RequestedFacility;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\User;
use App\Models\UserUpload;
use App\Models\FormStatusCode;


class RequisitionFormController extends Controller
{

    // ----- Store user information in session ----- //
    
public function saveUserInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:Internal,External',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'contact_number' => 'nullable|string|max:15',
            'organization_name' => 'nullable|string|max:100',
            'school_id' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Store in session
        session()->put('user_info', [
            'user_type' => $request->user_type,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'organization_name' => $request->organization_name,
            'school_id' => $request->school_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User information saved successfully',
            'user_info' => session()->get('user_info')
        ]);
    }

    // ----- Add items to session ----- //

    public function addToForm(Request $request)
    {
        $request->validate([
            'facility_id' => 'sometimes|exists:facilities,facility_id',
            'equipment_id' => 'sometimes|exists:equipment,equipment_id',
            'type' => 'required|in:facility,equipment'
        ]);
    
        $selectedItems = session()->get('selected_items', []);
        $id = $request->input($request->type . '_id');
    
        // Validate item ID exists
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a valid item.'
            ], 422);
        }
    
        // Check for duplicates
        $alreadyExists = collect($selectedItems)->contains(fn($item) =>
            $item['id'] == $id && $item['type'] === $request->type
        );
    
        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'message' => 'This item is already in your selection.'
            ], 422);
        }
    
        // Add new item to selection
        $selectedItems[] = [
            'id' => $id,
            'type' => $request->type
        ];
        
        session()->put('selected_items', $selectedItems);
    
        return response()->json([
            'success' => true,
            'message' => Str::ucfirst($request->type) . ' added to selection.',
            'count' => count($selectedItems),
            'selected_items' => $selectedItems
        ]);
    }

    // ----- Remove items from session ----- //

    public function removeFromForm(Request $request)
    {
        $request->validate([
            'facility_id' => 'required_without:equipment_id|exists:facilities,facility_id',
            'equipment_id' => 'required_without:facility_id|exists:equipment,equipment_id',
            'type' => 'required|in:facility,equipment'
        ]);

        $selectedItems = session()->get('selected_items', []);
        $id = $request->input($request->type . '_id');

        // Validate item exists in session
        $itemExists = collect($selectedItems)->contains(fn($item) =>
            $item['id'] == $id && $item['type'] === $request->type
        );

        if (!$itemExists) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in current selection.'
            ], 404);
        }

        // Remove item from session
        $updatedItems = array_values(array_filter($selectedItems, function($item) use ($id, $request) {
            return !($item['id'] == $id && $item['type'] === $request->type);
        }));

        session()->put('selected_items', $updatedItems);

        return response()->json([
            'success' => true,
            'message' => Str::ucfirst($request->type) . ' removed successfully.',
            'count' => count($updatedItems),
            'selected_items' => $updatedItems
        ]);
    }

    // ----- Check for booking schedule conflicts ----- //

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'facility_id' => 'sometimes|required|exists:facilities,facility_id',
            'equipment_id' => 'sometimes|required|exists:equipment,equipment_id'
        ]);

        // Get active status IDs (Scheduled=4, Ongoing=5)
        $conflictStatusIds = FormStatusCode::whereIn('status_name', ['Scheduled', 'Ongoing'])
            ->pluck('status_id')
            ->toArray();

        // Check for conflicting bookings
        $conflicts = RequisitionForm::where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                        ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                        $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                    });
            })
            ->whereIn('status_id', $conflictStatusIds)
            ->when($request->has('facility_id'), function($query) use ($request) {
                $query->whereHas('requestedFacilities', function($q) use ($request) {
                    $q->where('facility_id', $request->facility_id);
                });
            })
            ->when($request->has('equipment_id'), function($query) use ($request) {
                $query->whereHas('requestedEquipment', function($q) use ($request) {
                    $q->where('equipment_id', $request->equipment_id);
                });
            })
            ->exists();

        return response()->json([
            'success' => true,
            'available' => !$conflicts,
            'message' => $conflicts 
                ? 'This time slot conflicts with an existing booking. Please choose different dates/times.'
                : 'This time slot is available for booking.',
            'conflicts' => $conflicts
        ]);
    }

    // ----- Temporary user uploads before submission ----- //

    public function tempUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'upload_type' => 'required|in:Letter,Room Setup'
        ]);

        try {
            // Determine Cloudinary folder based on upload type
            $folder = $request->upload_type === 'Letter' 
                ? 'user-uploads/user-letters' 
                : 'user-uploads/user-setups';

            // Upload to Cloudinary
            $upload = Cloudinary::upload($request->file('file')->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'auto'
            ]);

            // Create temporary record
            $userUpload = UserUpload::create([
                'file_url' => $upload->getSecurePath(),
                'cloudinary_public_id' => $upload->getPublicId(),
                'upload_type' => $request->upload_type,
                'upload_token' => Str::random(40), // Unique token for session reference
                'request_id' => null // Will be set during final submission
            ]);

            // Store in session
            $uploadSession = session()->get('temp_uploads', []);
            $uploadSession[] = [
                'upload_id' => $userUpload->upload_id,
                'token' => $userUpload->upload_token,
                'type' => $userUpload->upload_type
            ];
            session()->put('temp_uploads', $uploadSession);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'upload' => [
                    'id' => $userUpload->upload_id,
                    'url' => $userUpload->file_url,
                    'type' => $userUpload->upload_type,
                    'token' => $userUpload->upload_token
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Submit the full requisition form
    public function submitRequisition(Request $request)
    {
        $validated = $request->validate([
            'user' => 'required|array',
            'user.user_type' => 'required|in:Internal,External',
            'user.first_name' => 'required|string|max:50',
            'user.last_name' => 'required|string|max:50',
            'user.email' => 'required|email|max:100',
            'user.organization' => 'nullable|string|max:100',
            'user.school_id' => 'nullable|string|max:20|required_if:user.user_type,Internal',
            
            'reservation' => 'required|array',
            'reservation.num_participants' => 'required|integer|min:1',
            'reservation.purpose_id' => 'required|exists:requisition_purposes,purpose_id',
            'reservation.other_purpose' => 'nullable|string|max:255',
            'reservation.additional_requests' => 'nullable|string|max:500',
            'reservation.start_date' => 'required|date|after_or_equal:today',
            'reservation.end_date' => 'required|date|after_or_equal:reservation.start_date',
            'reservation.start_time' => 'required|date_format:H:i',
            'reservation.end_time' => 'required|date_format:H:i|after:reservation.start_time',
            
            'requested_facilities' => 'sometimes|array',
            'requested_facilities.*.facility_id' => 'required|exists:facilities,facility_id',
            'requested_facilities.*.layout_upload_token' => 'nullable|string',
            
            'requested_equipment' => 'sometimes|array',
            'requested_equipment.*.equipment_id' => 'required|exists:equipment,equipment_id',
            'requested_equipment.*.quantity' => 'required|integer|min:1',
            
            'letter_upload_token' => 'required|string',
            'access_code' => 'required|string|size:6',
        ]);

        // Create or find user
        $user = User::firstOrCreate(
            ['email' => $validated['user']['email']],
            [
                'user_type' => $validated['user']['user_type'],
                'first_name' => $validated['user']['first_name'],
                'last_name' => $validated['user']['last_name'],
                'organization' => $validated['user']['organization'] ?? null,
                'school_id' => $validated['user']['school_id'] ?? null,
            ]
        );

        // Calculate tentative fees
        $tentativeFee = $this->calculateTentativeFee(
            $validated['requested_facilities'] ?? [],
            $validated['requested_equipment'] ?? [],
            $validated['user']['user_type']
        );

        // Create the requisition form
        $requisition = RequisitionForm::create([
            'user_id' => $user->user_id,
            'access_code' => $validated['access_code'],
            'num_participants' => $validated['reservation']['num_participants'],
            'purpose_id' => $validated['reservation']['purpose_id'],
            'other_purpose' => $validated['reservation']['other_purpose'] ?? null,
            'additional_requests' => $validated['reservation']['additional_requests'] ?? null,
            'status_id' => 1, // Pending Approval
            'start_date' => $validated['reservation']['start_date'],
            'end_date' => $validated['reservation']['end_date'],
            'start_time' => $validated['reservation']['start_time'],
            'end_time' => $validated['reservation']['end_time'],
            'tentative_fee' => $tentativeFee,
            'is_finalized' => true,
            'finalized_at' => now(),
        ]);

        // Attach requested facilities
        if (!empty($validated['requested_facilities'])) {
            foreach ($validated['requested_facilities'] as $facility) {
                $requestedFacility = RequestedFacility::create([
                    'request_id' => $requisition->request_id,
                    'facility_id' => $facility['facility_id'],
                ]);

                // Attach layout images if provided
                if (!empty($facility['layout_upload_token'])) {
                    $this->attachUploadsToRequisition(
                        $facility['layout_upload_token'],
                        $requisition->request_id,
                        'Room Setup'
                    );
                }
            }
        }

        // Attach requested equipment
        if (!empty($validated['requested_equipment'])) {
            foreach ($validated['requested_equipment'] as $equipment) {
                RequestedEquipment::create([
                    'request_id' => $requisition->request_id,
                    'equipment_id' => $equipment['equipment_id'],
                    'quantity' => $equipment['quantity'],
                ]);
            }
        }

        // Attach formal letter
        $this->attachUploadsToRequisition(
            $validated['letter_upload_token'],
            $requisition->request_id,
            'Letter'
        );

        // Send confirmation email
        $this->sendConfirmationEmail($user, $requisition);

        // Schedule reminder emails
        $this->scheduleReminderEmails($requisition);

        return response()->json([
            'message' => 'Requisition submitted successfully',
            'requisition_id' => $requisition->request_id,
            'access_code' => $requisition->access_code,
        ], 201);
    }

    private function calculateTentativeFee(array $facilities, array $equipment, string $userType): float
    {
        $totalFee = 0;

        // Calculate facility fees
        foreach ($facilities as $facilityData) {
            $facility = Facility::find($facilityData['facility_id']);
            $totalFee += ($userType === 'External') ? $facility->company_fee : $facility->internal_fee;
        }

        // Calculate equipment fees
        foreach ($equipment as $equipmentData) {
            $equipment = Equipment::find($equipmentData['equipment_id']);
            $quantity = $equipmentData['quantity'];
            $totalFee += ($userType === 'External') ? 
                ($equipment->company_fee * $quantity) : 
                ($equipment->internal_fee * $quantity);
        }

        return $totalFee;
    }

    private function attachUploadsToRequisition(string $uploadToken, int $requisitionId, string $type): void
    {
        UserUpload::where('upload_token', $uploadToken)
            ->where('upload_type', $type)
            ->update([
                'requisition_id' => $requisitionId,
                'upload_token' => null,
            ]);
    }





    private function sendConfirmationEmail(User $user, RequisitionForm $requisition): void
    {
        $emailData = [
            'user' => $user,
            'requisition' => $requisition,
            'facilities' => $requisition->requestedFacilities()->with('facility')->get(),
            'equipment' => $requisition->requestedEquipment()->with('equipment')->get(),
        ];

        Mail::send('emails.requisition_confirmation', $emailData, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your Requisition Has Been Submitted');
        });
    }

    private function scheduleReminderEmails(RequisitionForm $requisition): void
    {
        $startDate = Carbon::parse($requisition->start_date);
        $endDate = Carbon::parse($requisition->end_date);
        
        // Pre-event reminder (3 days before)
        $reminderDate = $startDate->copy()->subDays(3);
        if ($reminderDate->isFuture()) {
            SendRequisitionReminder::dispatch($requisition, 'pre_event')
                ->delay($reminderDate);
        }

        // Equipment reminders only if equipment is requested
        if ($requisition->requestedEquipment()->exists()) {
            // Pre-return reminder (3 days before due date)
            $preReturnDate = $endDate->copy()->subDays(3);
            if ($preReturnDate->isFuture()) {
                SendRequisitionReminder::dispatch($requisition, 'pre_return')
                    ->delay($preReturnDate);
            }

            // Due date reminder
            SendRequisitionReminder::dispatch($requisition, 'return_due')
                ->delay($endDate->startOfDay());
        }
    }

    // Get requisition details (for viewing)
    public function show($id, Request $request)
    {
        $requisition = RequisitionForm::with([
            'user',
            'purpose',
            'status',
            'requestedFacilities.facility',
            'requestedEquipment.equipment',
            'uploads'
        ])->findOrFail($id);

        // Verify access code if provided (for non-admin access)
        if ($request->has('access_code') && $requisition->access_code !== $request->access_code) {
            return response()->json(['message' => 'Invalid access code'], 403);
        }

        return response()->json($requisition);
    }
}