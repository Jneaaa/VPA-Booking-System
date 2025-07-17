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
use App\Models\User;
use App\Models\UserUpload;
use App\Models\FormStatusCode;

class RequisitionFormController extends Controller
{
    // Common response structure
    protected function jsonResponse($success, $message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    // ----- Save user information in session ----- //
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
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $userInfo = $request->only([
            'user_type', 'first_name', 'last_name', 'email', 
            'contact_number', 'organization_name', 'school_id'
        ]);

        // Sanitize inputs
        $userInfo['email'] = filter_var($userInfo['email'], FILTER_SANITIZE_EMAIL);
        $userInfo['first_name'] = htmlspecialchars($userInfo['first_name'], ENT_QUOTES);
        $userInfo['last_name'] = htmlspecialchars($userInfo['last_name'], ENT_QUOTES);

        session(['user_info' => $userInfo]);

        return $this->jsonResponse(true, 'User information saved successfully.', ['user_info' => $userInfo]);
    }

    // ----- Add items to session ----- //
    public function addToForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required_without:equipment_id|exists:facilities,facility_id',
            'equipment_id' => 'required_without:facility_id|exists:equipment,equipment_id',
            'type' => 'required|in:facility,equipment',
            'quantity' => 'sometimes|integer|min:1'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        $selectedItems = session('selected_items', []);
        $id = $request->input($request->type . '_id');
        $type = $request->type;

        // Check for duplicate item
        if (collect($selectedItems)->contains(fn($item) => $item['id'] == $id && $item['type'] === $type)) {
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
            'added_at' => now(),
            'quantity' => $request->quantity ?? 1
        ];

        $selectedItems[] = $newItem;
        session(['selected_items' => $selectedItems]);

        return $this->jsonResponse(true, ucfirst($type) . ' added successfully.', [
            'selected_items' => $selectedItems,
            'total_items' => count($selectedItems)
        ]);
    }

    // ----- Calculate total fee of added items ----- //
    public function calculateFees()
    {
        $selectedItems = session('selected_items', []);
        $userType = session('user_info.user_type', 'Internal');

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

        $selectedItems = session('selected_items', []);
        $id = $request->input($request->type . '_id');
        $type = $request->type;

        $updatedItems = array_values(array_filter($selectedItems, 
            fn($item) => !($item['id'] == $id && $item['type'] === $type)
        ));

        session(['selected_items' => $updatedItems]);

        return $this->jsonResponse(true, ucfirst($type) . ' removed successfully.', [
            'selected_items' => $updatedItems,
            'total_items' => count($updatedItems)
        ]);
    }

    // ----- Check for booking schedule conflicts ----- //
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'facility_id' => 'sometimes|required|exists:facilities,facility_id',
            'equipment_id' => 'sometimes|required|exists:equipment,equipment_id'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        // Validate time slot first
        if ($request->start_date == $request->end_date && $request->start_time >= $request->end_time) {
            return $this->jsonResponse(false, 'End time must be after start time.', [], 422);
        }

        $conflictStatusIds = FormStatusCode::whereIn('status_name', ['Scheduled', 'Ongoing'])
            ->pluck('status_id')
            ->toArray();

        $query = RequisitionForm::where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(fn($q) => $q->where('start_date', '<=', $request->start_date)
                        ->where('end_date', '>=', $request->end_date));
            })
            ->where(function($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->whereIn('status_id', $conflictStatusIds);

        if ($request->has('facility_id')) {
            $query->whereHas('requestedFacilities', fn($q) => $q->where('facility_id', $request->facility_id));
        }

        if ($request->has('equipment_id')) {
            $query->whereHas('requestedEquipment', fn($q) => $q->where('equipment_id', $request->equipment_id));
        }

        $conflicts = $query->exists();

        return $this->jsonResponse(true, $conflicts ? 
            'Time slot conflicts with an existing booking.' : 'Time slot is available.', 
            ['available' => !$conflicts]
        );
    }

    // ----- Temporary user uploads before submission ----- //
    public function tempUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_type' => 'required|in:Letter,Room Setup'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validation failed.', ['errors' => $validator->errors()], 422);
        }

        try {
            $folder = $request->upload_type === 'Letter' ? 
                'user-uploads/user-letters' : 'user-uploads/user-setups';
            
            $upload = Cloudinary::upload(
                $request->file('file')->getRealPath(), 
                ['folder' => $folder, 'resource_type' => 'auto']
            );

            if (!$upload->getSecurePath()) {
                throw new \Exception('Cloudinary upload failed: No secure URL returned.');
            }

            $userUpload = UserUpload::create([
                'file_url' => $upload->getSecurePath(),
                'cloudinary_public_id' => $upload->getPublicId(),
                'upload_type' => $request->upload_type,
                'upload_token' => Str::random(40),
                'request_id' => null
            ]);

            $tempUploads = session('temp_uploads', []);
            $tempUploads[] = [
                'upload_id' => $userUpload->upload_id,
                'token' => $userUpload->upload_token,
                'type' => $userUpload->upload_type
            ];
            
            session(['temp_uploads' => $tempUploads]);

            return $this->jsonResponse(true, 'File uploaded successfully.', [
                'upload' => [
                    'id' => $userUpload->upload_id,
                    'url' => $userUpload->file_url,
                    'type' => $userUpload->upload_type,
                    'token' => $userUpload->upload_token
                ],
                'temp_uploads' => $tempUploads
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Upload failed: ' . $e->getMessage(), [], 500);
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
            // Validate session data exists
            $userInfo = session('user_info');
            $selectedItems = session('selected_items', []);
            $tempUploads = session('temp_uploads', []);

            if (!$userInfo) {
                throw new \Exception('User information not found. Please complete step 1.');
            }

            if (empty($selectedItems)) {
                throw new \Exception('Your booking cart is empty. Add items before submitting.');
            }

            // Check for conflicts again right before submission
            $conflictCheck = $this->checkAvailability($request);
            if (!$conflictCheck->getData()->available) {
                throw new \Exception('Time slot no longer available. Please choose another.');
            }

            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $userInfo['email']],
                $userInfo
            );

            // Create requisition form
            $requisitionForm = RequisitionForm::create([
                'user_id' => $user->user_id,
                'access_code' => Str::upper(Str::random(8)),
                'purpose_id' => $request->purpose_id,
                'num_participants' => $request->num_participants,
                'additional_requests' => $request->additional_requests,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status_id' => FormStatusCode::where('status_name', 'Pending Approval')->value('status_id'),
                'tentative_fee' => session('fee_summary.total_fee', 0),
            ]);

            // Process selected items with stock validation
            foreach ($selectedItems as $item) {
                if ($item['type'] === 'facility') {
                    RequestedFacility::create([
                        'request_id' => $requisitionForm->request_id,
                        'facility_id' => $item['id'],
                        'is_waived' => false,
                    ]);
                } elseif ($item['type'] === 'equipment') {
                    $equipment = Equipment::find($item['id']);
                    if (!$equipment || $equipment->available_quantity < $item['quantity']) {
                        throw new \Exception("Not enough {$equipment->name} in stock.");
                    }

                    RequestedEquipment::create([
                        'request_id' => $requisitionForm->request_id,
                        'equipment_id' => $item['id'],
                        'quantity' => $item['quantity'] ?? 1,
                        'is_waived' => false,
                    ]);

                    // Deduct stock
                    $equipment->decrement('available_quantity', $item['quantity']);
                }
            }

            // Process temporary uploads
            foreach ($tempUploads as $upload) {
                UserUpload::where('upload_token', $upload['token'])
                    ->update(['request_id' => $requisitionForm->request_id]);
            }

            // Clear session data
            session()->forget(['user_info', 'selected_items', 'fee_summary', 'temp_uploads']);
            
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