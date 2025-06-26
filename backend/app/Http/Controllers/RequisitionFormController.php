<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use App\Models\UserFile;
use Illuminate\Http\Request;
use App\Models\RequisitionForm;
use App\Models\RequestedEquipment;
use App\Models\RequestedFacility;
use App\Models\User;
use App\Models\UserUpload;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class RequisitionFormController extends Controller
{
    // Save form progress
public function saveProgress(Request $request)
{
    $sessionKey = 'requisition_form_' . ($request->user() ? $request->user()->id : session()->getId());
    
    $data = $request->validate([
        'step' => 'required|integer',
        'formData' => 'required|array'
    ]);
    
    $currentData = session()->get($sessionKey, []);
    $currentData[$data['step']] = $data['formData'];
    
    session()->put($sessionKey, $currentData);
    session()->save();
    
    return response()->json(['message' => 'Progress saved']);
}

// Load saved progress
public function loadProgress(Request $request)
{
    $sessionKey = 'requisition_form_' . ($request->user() ? $request->user()->id : session()->getId());
    
    return response()->json([
        'savedData' => session()->get($sessionKey, [])
    ]);
}

// Clear saved progress
public function clearProgress(Request $request)
{
    $sessionKey = 'requisition_form_' . ($request->user() ? $request->user()->id : session()->getId());
    
    session()->forget($sessionKey);
    
    return response()->json(['message' => 'Progress cleared']);
}
    // Temporary file upload handler
    public function tempUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'upload_type' => 'required|in:Letter,Room Setup',
        ]);

        $uploadedFile = Cloudinary::upload($request->file('file')->getRealPath(), [
            'folder' => 'requisition_uploads/temp',
            'resource_type' => 'auto'
        ]);

        $uploadToken = $request->input('upload_token') ?? Str::uuid()->toString();

        $userFile = UserUpload::create([
            'file_url' => $uploadedFile->getSecurePath(),
            'cloudinary_public_id' => $uploadedFile->getPublicId(),
            'upload_type' => $request->upload_type,
            'upload_token' => $uploadToken,
        ]);

        return response()->json([
            'upload_token' => $uploadToken,
            'user_file' => $userFile,
        ]);
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
            $totalFee += ($userType === 'External') ? $facility->company_fee : $facility->rental_fee;
        }

        // Calculate equipment fees
        foreach ($equipment as $equipmentData) {
            $equipment = Equipment::find($equipmentData['equipment_id']);
            $quantity = $equipmentData['quantity'];
            $totalFee += ($userType === 'External') ? 
                ($equipment->company_fee * $quantity) : 
                ($equipment->rental_fee * $quantity);
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