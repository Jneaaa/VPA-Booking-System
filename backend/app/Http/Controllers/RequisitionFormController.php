<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
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

        $selectedItems = session('selected_items', []);

        // Prevent overload: limit to 10 items
        if (count($selectedItems) >= 10) {
            return back()->withErrors(['message' => 'You have reached the maximum allowed items.']);
        }

        $id = $request->input($request->type . '_id');

        if (!$id) {
            return back()->withErrors(['message' => 'Please select a valid item.']);
        }

        if (collect($selectedItems)->contains(fn($item) => 
            $item['id'] == $id && $item['type'] === $request->type
        )) {
            return back()->withErrors(['message' => 'Item already in selection.']);
        }

        $selectedItems[] = [
            'id' => $id,
            'type' => $request->type,
            'added_at' => now()
        ];

        session(['selected_items' => $selectedItems]); // Ensure session is updated

        // Calculate the sum of added facilities and equipment
        $facilityCount = collect($selectedItems)->where('type', 'facility')->count();
        $equipmentCount = collect($selectedItems)->where('type', 'equipment')->count();
        $totalItems = $facilityCount + $equipmentCount;

        return back()->with([
            'success' => Str::ucfirst($request->type) . ' added to selection.',
            'selected_items' => session('selected_items'), // Return updated session data
            'total_items' => $totalItems // Return the sum of facilities and equipment
        ]);
    }

    // ----- Calculate total fee of added items ----- //

    public function calculateFees()
    {
        $selectedItems = session('selected_items', []);

        $facilityTotalFee = collect($selectedItems)->reduce(function ($total, $item) {
            if ($item['type'] === 'facility') {
                $facility = Facility::find($item['id']);
                return $total + ($facility ? $facility->internal_fee : 0);
            }
            return $total;
        }, 0);

        $equipmentTotalFee = collect($selectedItems)->reduce(function ($total, $item) {
            if ($item['type'] === 'equipment') {
                $equipment = Equipment::find($item['id']);
                return $total + ($equipment ? $equipment->internal_fee : 0);
            }
            return $total;
        }, 0);

        $totalFee = $facilityTotalFee + $equipmentTotalFee;

        // Save fees in session
        session()->put('fee_summary', [
            'facility_total_fee' => $facilityTotalFee,
            'equipment_total_fee' => $equipmentTotalFee,
            'total_fee' => $totalFee
        ]);

        return response()->json([
            'success' => true,
            'fee_summary' => session('fee_summary') // Return fee summary from session
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

        $itemExists = collect($selectedItems)->contains(fn($item) =>
            $item['id'] == $id && $item['type'] === $request->type
        );

        if (!$itemExists) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in current selection.'
            ], 404);
        }

        $updatedItems = array_values(array_filter($selectedItems, function($item) use ($id, $request) {
            return !($item['id'] == $id && $item['type'] === $request->type);
        }));

        session()->put('selected_items', $updatedItems); // Ensure session is updated

        return response()->json([
            'success' => true,
            'message' => Str::ucfirst($request->type) . ' removed successfully.',
            'count' => count(session('selected_items')), // Return updated session count
            'selected_items' => session('selected_items') // Return updated session data
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
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_type' => 'required|in:Letter,Room Setup'
        ]);

        try {
            $folder = $request->upload_type === 'Letter' 
                ? 'user-uploads/user-letters' 
                : 'user-uploads/user-setups';

            $upload = Cloudinary::upload($request->file('file')->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'auto'
            ]);

            $userUpload = UserUpload::create([
                'file_url' => $upload->getSecurePath(),
                'cloudinary_public_id' => $upload->getPublicId(),
                'upload_type' => $request->upload_type,
                'upload_token' => Str::random(40),
                'request_id' => null
            ]);

            $uploadSession = session()->get('temp_uploads', []);
            $uploadSession[] = [
                'upload_id' => $userUpload->upload_id,
                'token' => $userUpload->upload_token,
                'type' => $userUpload->upload_type
            ];
            session()->put('temp_uploads', $uploadSession); // Ensure session is updated

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'upload' => [
                    'id' => $userUpload->upload_id,
                    'url' => $userUpload->file_url,
                    'type' => $userUpload->upload_type,
                    'token' => $userUpload->upload_token
                ],
                'temp_uploads' => session('temp_uploads') // Return updated session data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}