<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\RequisitionForm;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\RateType;
use App\Models\Amenity;

// Define your status IDs here, or load them from a config/enum
const REQUISITION_STATUS_PENDING = 1;
const REQUISITION_STATUS_APPROVED = 2;
const REQUISITION_STATUS_REJECTED = 3;
const FACILITY_EQUIPMENT_STATUS_ACTIVE = 1; // Example for active status of facilities/equipment

class RequisitionController extends Controller
{
    /**
     * Handles the submission of a new booking requisition.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitRequisition(Request $request)
    {
        // --- STEP 1: Input Validation ---
        try {
            $request->validate([
                'num_participants' => 'required|integer|min:1',
                'purpose_id' => 'required|exists:requisition_purposes,purpose_id',
                'other_purpose' => 'nullable|string|max:255',
                'additional_requests' => 'nullable|string',
                'rental_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s|after:start_time',
                'applicant_type' => 'required|in:individual,company',
                'facilities' => 'nullable|array',
                'facilities.*.facility_id' => 'required|exists:facilities,facility_id',
                'facilities.*.is_waived' => 'boolean',
                'equipment' => 'nullable|array',
                'equipment.*.equipment_id' => 'required|exists:equipment,equipment_id',
                'equipment.*.quantity' => 'required|integer|min:1',
                'equipment.*.is_waived' => 'boolean',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed for your request.',
                'errors' => $e->errors()
            ], 422);
        }

        $formData = $request->only([
            'num_participants', 'purpose_id', 'other_purpose',
            'additional_requests', 'rental_date', 'start_time', 'end_time',
            'applicant_type'
        ]);
        $requestedFacilities = $request->input('facilities', []);
        $requestedEquipment = $request->input('equipment', []);

        $totalEstimatedFee = 0;
        $applicantFeeType = ($formData['applicant_type'] == 'company') ? 'company_fee' : 'rental_fee';
        $bookingDurationHours = $this->calculateDurationInHours($formData['start_time'], $formData['end_time']);

        DB::beginTransaction();

        try {
            $requisitionForm = RequisitionForm::create([
                'access_code' => $this->generateUniqueAccessCode(),
                'num_participants' => $formData['num_participants'],
                'purpose_id' => $formData['purpose_id'],
                'other_purpose' => $formData['other_purpose'],
                'additional_requests' => $formData['additional_requests'],
                'rental_date' => $formData['rental_date'],
                'start_time' => $formData['start_time'],
                'end_time' => $formData['end_time'],
                'status_id' => REQUISITION_STATUS_PENDING, // Always pending on submission
                'user_id' => auth()->id(),
                'estimated_total_fee' => 0, 
            ]);

            // --- Process Facilities ---
            foreach ($requestedFacilities as $reqFacility) {
                $facility = Facility::find($reqFacility['facility_id']);

                if (!$facility || $facility->status_id !== FACILITY_EQUIPMENT_STATUS_ACTIVE) {
                    throw new \Exception("Requested facility '{$reqFacility['facility_id']}' is not active or does not exist.");
                }

                if ($this->isFacilityBooked($facility->facility_id, $formData['rental_date'], $formData['start_time'], $formData['end_time'])) {
                    throw new \Exception("Facility '{$facility->facility_name}' is already booked for the requested time.");
                }

                $facilityBaseFee = $reqFacility['is_waived'] ? 0 : $facility->{$applicantFeeType};
                $totalEstimatedFee += $facilityBaseFee;

                $requisitionForm->facilities()->attach($facility->facility_id, [
                    'is_waived' => $reqFacility['is_waived']
                ]);

                $facility->update(['last_booked_at' => now()]);

                foreach ($facility->equipmentInFacility as $autoIncludedEquipment) {
                    if ($autoIncludedEquipment->status_id === FACILITY_EQUIPMENT_STATUS_ACTIVE) {
                        if ($this->isEquipmentQuantityAvailable(
                            $autoIncludedEquipment->equipment_id,
                            1,
                            $formData['rental_date'],
                            $formData['start_time'],
                            $formData['end_time']
                        )) {
                            throw new \Exception("Automatically included equipment '{$autoIncludedEquipment->equipment_name}' from '{$facility->facility_name}' is not available in the required quantity for the requested time.");
                        }

                        $autoEqFee = $this->calculateEquipmentFee(
                            $autoIncludedEquipment,
                            1,
                            $applicantFeeType,
                            $bookingDurationHours
                        );
                        $totalEstimatedFee += $autoEqFee;

                        $requisitionForm->equipment()->attach($autoIncludedEquipment->equipment_id, [
                            'quantity' => 1,
                            'is_waived' => false
                        ]);
                        $autoIncludedEquipment->update(['last_booked_at' => now()]);
                    }
                }

                foreach ($facility->amenities as $amenityInFacility) {
                    $amenity = Amenity::find($amenityInFacility->amenity_id);
                    if ($amenity) {
                        $amenityPrice = $amenity->price;
                        $totalEstimatedFee += $amenityPrice * $amenityInFacility->quantity;
                    }
                }
            }

            // --- Process Requested Equipment ---
            foreach ($requestedEquipment as $reqEq) {
                $equipment = Equipment::find($reqEq['equipment_id']);

                if (!$equipment || $equipment->status_id !== FACILITY_EQUIPMENT_STATUS_ACTIVE) {
                    throw new \Exception("Requested equipment '{$reqEq['equipment_id']}' is not active or does not exist.");
                }

                if ($this->isEquipmentQuantityAvailable(
                    $equipment->equipment_id,
                    $reqEq['quantity'],
                    $formData['rental_date'],
                    $formData['start_time'],
                    $formData['end_time']
                )) {
                    throw new \Exception("Not enough quantity of '{$equipment->equipment_name}' available for the requested time.");
                }

                $equipmentFee = $reqEq['is_waived'] ? 0 : $this->calculateEquipmentFee(
                    $equipment,
                    $reqEq['quantity'],
                    $applicantFeeType,
                    $bookingDurationHours
                );
                $totalEstimatedFee += $equipmentFee;

                $requisitionForm->equipment()->attach($equipment->equipment_id, [
                    'quantity' => $reqEq['quantity'],
                    'is_waived' => $reqEq['is_waived']
                ]);

                $equipment->update(['last_booked_at' => now()]);
            }

            $requisitionForm->update(['estimated_total_fee' => $totalEstimatedFee]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking request submitted successfully for review.',
                'request_id' => $requisitionForm->request_id,
                'access_code' => $requisitionForm->access_code,
                'estimated_total_fee' => $totalEstimatedFee
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Updates an existing booking requisition.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id The request_id of the requisition form
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRequisition(Request $request, int $id)
    {
        // Find the requisition form
        $requisitionForm = RequisitionForm::find($id);

        if (!$requisitionForm) {
            return response()->json(['message' => 'Requisition form not found.'], 404);
        }

        // OPTIONAL: Prevent editing if the form is already approved or rejected
        if ($requisitionForm->status_id === REQUISITION_STATUS_APPROVED ||
            $requisitionForm->status_id === REQUISITION_STATUS_REJECTED) {
            return response()->json(['message' => 'Cannot edit an approved or rejected requisition.'], 403);
        }

        // --- Input Validation for Update ---
        try {
            $request->validate([
                'num_participants' => 'required|integer|min:1',
                'purpose_id' => 'required|exists:requisition_purposes,purpose_id',
                'other_purpose' => 'nullable|string|max:255',
                'additional_requests' => 'nullable|string',
                'rental_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s|after:start_time',
                'applicant_type' => 'required|in:individual,company',
                'facilities' => 'nullable|array',
                'facilities.*.facility_id' => 'required|exists:facilities,facility_id',
                'facilities.*.is_waived' => 'boolean',
                'equipment' => 'nullable|array',
                'equipment.*.equipment_id' => 'required|exists:equipment,equipment_id',
                'equipment.*.quantity' => 'required|integer|min:1',
                'equipment.*.is_waived' => 'boolean',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed for your update request.',
                'errors' => $e->errors()
            ], 422);
        }

        $formData = $request->only([
            'num_participants', 'purpose_id', 'other_purpose',
            'additional_requests', 'rental_date', 'start_time', 'end_time',
            'applicant_type'
        ]);
        $requestedFacilities = $request->input('facilities', []);
        $requestedEquipment = $request->input('equipment', []);

        $totalEstimatedFee = 0;
        $applicantFeeType = ($formData['applicant_type'] == 'company') ? 'company_fee' : 'rental_fee';
        $bookingDurationHours = $this->calculateDurationInHours($formData['start_time'], $formData['end_time']);

        DB::beginTransaction();

        try {
            // Update the main requisition form data
            $requisitionForm->update($formData);

            // Re-sync facilities: detach existing, then attach new ones
            // This is crucial for updating pivot table data
            $currentFacilities = [];
            foreach ($requestedFacilities as $reqFacility) {
                $facility = Facility::find($reqFacility['facility_id']);

                if (!$facility || $facility->status_id !== FACILITY_EQUIPMENT_STATUS_ACTIVE) {
                    throw new \Exception("Requested facility '{$reqFacility['facility_id']}' is not active or does not exist.");
                }
                // When updating, check availability against *other* bookings,
                // and potentially the current booking if its time/date changed
                if ($this->isFacilityBooked($facility->facility_id, $formData['rental_date'], $formData['start_time'], $formData['end_time'], $requisitionForm->request_id)) {
                    throw new \Exception("Facility '{$facility->facility_name}' is already booked for the requested time.");
                }

                $facilityBaseFee = $reqFacility['is_waived'] ? 0 : $facility->{$applicantFeeType};
                $totalEstimatedFee += $facilityBaseFee;

                $currentFacilities[$facility->facility_id] = ['is_waived' => $reqFacility['is_waived']];

                // Re-calculate auto-included equipment/amenities
                foreach ($facility->equipmentInFacility as $autoIncludedEquipment) {
                    if ($autoIncludedEquipment->status_id === FACILITY_EQUIPMENT_STATUS_ACTIVE) {
                        if ($this->isEquipmentQuantityAvailable(
                            $autoIncludedEquipment->equipment_id,
                            1,
                            $formData['rental_date'],
                            $formData['start_time'],
                            $formData['end_time'],
                            $requisitionForm->request_id // Pass current requisition ID to exclude it from conflict check
                        )) {
                             throw new \Exception("Automatically included equipment '{$autoIncludedEquipment->equipment_name}' from '{$facility->facility_name}' is not available in the required quantity for the requested time.");
                        }

                        $autoEqFee = $this->calculateEquipmentFee(
                            $autoIncludedEquipment,
                            1,
                            $applicantFeeType,
                            $bookingDurationHours
                        );
                        $totalEstimatedFee += $autoEqFee;
                    }
                }
                foreach ($facility->amenities as $amenityInFacility) {
                    $amenity = Amenity::find($amenityInFacility->amenity_id);
                    if ($amenity) {
                        $amenityPrice = $amenity->price;
                        $totalEstimatedFee += $amenityPrice * $amenityInFacility->quantity;
                    }
                }
            }
            // Sync facilities with the pivot data
            $requisitionForm->facilities()->sync($currentFacilities);


            // Re-sync equipment: detach existing, then attach new ones
            $currentEquipment = [];
            foreach ($requestedEquipment as $reqEq) {
                $equipment = Equipment::find($reqEq['equipment_id']);

                if (!$equipment || $equipment->status_id !== FACILITY_EQUIPMENT_STATUS_ACTIVE) {
                    throw new \Exception("Requested equipment '{$reqEq['equipment_id']}' is not active or does not exist.");
                }

                if ($this->isEquipmentQuantityAvailable(
                    $equipment->equipment_id,
                    $reqEq['quantity'],
                    $formData['rental_date'],
                    $formData['start_time'],
                    $formData['end_time'],
                    $requisitionForm->request_id // Pass current requisition ID to exclude it from conflict check
                )) {
                    throw new \Exception("Not enough quantity of '{$equipment->equipment_name}' available for the requested time.");
                }

                $equipmentFee = $reqEq['is_waived'] ? 0 : $this->calculateEquipmentFee(
                    $equipment,
                    $reqEq['quantity'],
                    $applicantFeeType,
                    $bookingDurationHours
                );
                $totalEstimatedFee += $equipmentFee;

                $currentEquipment[$equipment->equipment_id] = [
                    'quantity' => $reqEq['quantity'],
                    'is_waived' => $reqEq['is_waived']
                ];
            }
            // Sync equipment with the pivot data
            $requisitionForm->equipment()->sync($currentEquipment);


            // Update the total estimated fee
            $requisitionForm->update(['estimated_total_fee' => $totalEstimatedFee]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Requisition form updated successfully.',
                'request_id' => $requisitionForm->request_id,
                'estimated_total_fee' => $totalEstimatedFee
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating requisition: ' . $e->getMessage()
            ], 400);
        }
    }


    /**
     * Deletes a requisition form.
     *
     * @param  int  $id The request_id of the requisition form
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRequisition(int $id)
    {
        $requisitionForm = RequisitionForm::find($id);

        if (!$requisitionForm) {
            return response()->json(['message' => 'Requisition form not found.'], 404);
        }

        // OPTIONAL: Prevent deletion if the form is already approved
        // This is a business rule you might want to enforce
        if ($requisitionForm->status_id === REQUISITION_STATUS_APPROVED) {
            return response()->json(['message' => 'Cannot delete an approved requisition.'], 403);
        }

        DB::beginTransaction();
        try {
            // Detach related facilities and equipment first to avoid foreign key constraints
            $requisitionForm->facilities()->detach();
            $requisitionForm->equipment()->detach();

            $requisitionForm->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Requisition form deleted successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error deleting requisition: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Approves a pending requisition form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id The request_id of the requisition form
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveRequisition(Request $request, int $id)
    {
        $requisitionForm = RequisitionForm::find($id);

        if (!$requisitionForm) {
            return response()->json(['message' => 'Requisition form not found.'], 404);
        }

        // Check if the form is pending before approving
        if ($requisitionForm->status_id !== REQUISITION_STATUS_PENDING) {
            return response()->json(['message' => 'Only pending requisitions can be approved.'], 400);
        }

        DB::beginTransaction();
        try {
            // Re-run availability checks at the time of approval
            // This prevents double-bookings if availability changed while pending
            if ($requisitionForm->facilities) {
                foreach ($requisitionForm->facilities as $facility) {
                    if ($this->isFacilityBooked(
                        $facility->facility_id,
                        $requisitionForm->rental_date,
                        $requisitionForm->start_time,
                        $requisitionForm->end_time,
                        $requisitionForm->request_id // Exclude current requisition from check
                    )) {
                        DB::rollBack(); // Rollback if conflict found
                        return response()->json([
                            'status' => 'error',
                            'message' => "Facility '{$facility->facility_name}' is no longer available for the requested time. Cannot approve."
                        ], 409); // 409 Conflict
                    }
                }
            }

            if ($requisitionForm->equipment) {
                foreach ($requisitionForm->equipment as $equipment) {
                    if ($this->isEquipmentQuantityAvailable(
                        $equipment->equipment_id,
                        $equipment->pivot->quantity, // Get quantity from pivot table
                        $requisitionForm->rental_date,
                        $requisitionForm->start_time,
                        $requisitionForm->end_time,
                        $requisitionForm->request_id // Exclude current requisition from check
                    )) {
                        DB::rollBack(); // Rollback if conflict found
                        return response()->json([
                            'status' => 'error',
                            'message' => "Not enough quantity of '{$equipment->equipment_name}' available. Cannot approve."
                        ], 409); // 409 Conflict
                    }
                }
            }

            // Update the status and approval details
            $requisitionForm->update([
                'status_id' => REQUISITION_STATUS_APPROVED,
                'approved_by_admin_id' => auth()->guard('admin')->id(), // Assuming admin guard
                'approved_at' => now(),
                'rejection_reason' => null, // Clear any previous rejection reason if it was rejected before
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Requisition form approved successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error approving requisition: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rejects a pending requisition form and stores the reason.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id The request_id of the requisition form
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectRequisition(Request $request, int $id)
    {
        $requisitionForm = RequisitionForm::find($id);

        if (!$requisitionForm) {
            return response()->json(['message' => 'Requisition form not found.'], 404);
        }

        // Only allow rejection if it's pending or perhaps approved (if you allow revoking approval)
        // For simplicity, let's allow pending to be rejected.
        if ($requisitionForm->status_id !== REQUISITION_STATUS_PENDING) {
             return response()->json(['message' => 'Only pending requisitions can be rejected.'], 400);
        }


        // Validate rejection reason
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rejection reason is required and must be a string.',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $requisitionForm->update([
                'status_id' => REQUISITION_STATUS_REJECTED,
                'rejection_reason' => $request->input('rejection_reason'),
                'rejected_by_admin_id' => auth()->guard('admin')->id(), // Assuming admin guard
                'rejected_at' => now(),
                'approved_by_admin_id' => null, // Clear approval details if re-rejecting
                'approved_at' => null,
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Requisition form rejected successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error rejecting requisition: ' . $e->getMessage()], 500);
        }
    }


    // --- Helper Functions (Private methods within the Controller or dedicated service class) ---

    /**
     * Generates a unique access code for the requisition.
     */
    private function generateUniqueAccessCode(): string
    {
        do {
            $code = strtoupper(substr(uniqid(), -8)); // Simple 8-char unique ID
        } while (RequisitionForm::where('access_code', $code)->exists());
        return $code;
    }

    /**
     * Calculates the duration between start and end times in hours.
     * @param string $startTime 'HH:MM:SS'
     * @param string $endTime 'HH:MM:SS'
     * @return float Duration in hours
     */
    private function calculateDurationInHours(string $startTime, string $endTime): float
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        return $end->diffInMinutes($start) / 60;
    }

    /**
     * Checks if a specific facility is booked during the requested time slot.
     * Searches for *confirmed/approved* bookings that overlap.
     * @param int $facilityId
     * @param string $date 'YYYY-MM-DD'
     * @param string $startTime 'HH:MM:SS'
     * @param string $endTime 'HH:MM:SS'
     * @param int|null $excludeRequisitionId Optional ID of current requisition to exclude from conflict check
     * @return bool True if booked, false otherwise.
     */
    private function isFacilityBooked(int $facilityId, string $date, string $startTime, string $endTime, ?int $excludeRequisitionId = null): bool
    {
        $query = DB::table('requisition_facilities')
            ->join('requisition_form', 'requisition_facilities.request_id', '=', 'requisition_form.request_id')
            ->where('requisition_facilities.facility_id', $facilityId)
            ->where('requisition_form.rental_date', $date)
            // Check only against APPROVED forms for strict booking conflicts
            // If you want to block based on PENDING too, include REQUISITION_STATUS_PENDING
            ->whereIn('requisition_form.status_id', [REQUISITION_STATUS_APPROVED]);

        if ($excludeRequisitionId) {
            $query->where('requisition_form.request_id', '!=', $excludeRequisitionId);
        }

        $query->where(function ($q) use ($startTime, $endTime) {
            // Check for time overlap: [start_time, end_time) should not overlap with (existing_start_time, existing_end_time)
            $q->where('requisition_form.start_time', '<', $endTime)
              ->where('requisition_form.end_time', '>', $startTime);
        });

        return $query->exists();
    }

    /**
     * Checks if enough quantity of a specific equipment is available during the requested time slot.
     * Accounts for total quantity and quantities booked in *confirmed* overlapping requisitions.
     * @param int $equipmentId
     * @param int $requestedQuantity
     * @param string $date 'YYYY-MM-DD'
     * @param string $startTime 'HH:MM:SS'
     * @param string $endTime 'HH:MM:SS'
     * @param int|null $excludeRequisitionId Optional ID of current requisition to exclude from conflict check
     * @return bool True if NOT available (conflict), false if available.
     */
    private function isEquipmentQuantityAvailable(int $equipmentId, int $requestedQuantity, string $date, string $startTime, string $endTime, ?int $excludeRequisitionId = null): bool
    {
        $equipment = Equipment::find($equipmentId);
        if (!$equipment) {
            return true; // Consider it unavailable if equipment not found
        }

        $totalAvailable = $equipment->total_quantity;

        $query = DB::table('requested_equipment')
            ->join('requisition_form', 'requested_equipment.request_id', '=', 'requisition_form.request_id')
            ->where('requested_equipment.equipment_id', $equipmentId)
            ->where('requisition_form.rental_date', $date)
            // Check only against APPROVED forms for strict booking conflicts
            ->whereIn('requisition_form.status_id', [REQUISITION_STATUS_APPROVED]);

        if ($excludeRequisitionId) {
            $query->where('requisition_form.request_id', '!=', $excludeRequisitionId);
        }

        $bookedQuantity = $query->where(function ($q) use ($startTime, $endTime) {
            $q->where('requisition_form.start_time', '<', $endTime)
              ->where('requisition_form.end_time', '>', $startTime);
        })
        ->sum('requested_equipment.quantity');

        // Return true if requested quantity + already booked quantity exceeds total available
        return ($requestedQuantity + $bookedQuantity) > $totalAvailable;
    }

    /**
     * Calculates the fee for a single equipment item based on its rate type.
     * @param Equipment $equipment The Equipment model instance.
     * @param int $quantity The number of units requested.
     * @param string $applicantFeeType 'rental_fee' or 'company_fee'.
     * @param float $bookingDurationHours The duration of the booking in hours.
     * @return float Calculated fee.
     */
    private function calculateEquipmentFee(
        Equipment $equipment,
        int $quantity,
        string $applicantFeeType,
        float $bookingDurationHours
    ): float {
        $rateType = $equipment->rateType->rate_type ?? null;

        $baseFee = $equipment->{$applicantFeeType};

        if ($rateType === 'Per Hour') {
            return $baseFee * $bookingDurationHours * $quantity;
        } elseif ($rateType === 'Per Show/Event') {
            return $baseFee * $quantity;
        }
        return 0.0;
    }
}