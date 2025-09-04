<?php

/* 

Reservation Workflow Summary:

- requester fills up form to book items then submits 
- admin reviews and confirms the booking, now awaiting payment 
- requester goes to business office to pay then sends their proof of payment as a picture or pdf file in our web based system 
- admin confirms payment and finalizes their booking timeslot 
- ongoing event happens as planned 
- equipment is returned at the day of their schedule's end datetime, if any was booked 
- admin closes the forms and marks it as completed

*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequisitionApproval;
use App\Models\RequestedFacility;
use App\Models\RequestedEquipment;
use App\Models\RequisitionFee;
use App\Models\FormStatus;
use App\Models\CompletedTransaction;
use App\Models\Admin;
use App\Models\LookupTables\AdminRole;
use App\Models\RequisitionForm;
use App\Models\RequisitionComment;
use App\Models\LookupTables\AvailabilityStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminApprovalController extends Controller
{
    /* Controller Documentation:

        * This controller handles the admin approval process for requisition forms.
        * It includes methods for viewing pending approvals, approving or rejecting requests,
        * and managing the status of requisition forms.
        *
        * Each action is logged in the system_logs (log_id) table (future implementation).


        * Methods:

        - pendingRequests(): Get all records from the requisition_forms table (PK: request_id) for admin approval
            - Excluded status_name: Returned, Late Return, Completed, Rejected, and Cancelled. Pluck the status_id based on these status_names. Eloquent model relationship: FormStatus.
            - Add the total number of approvals and rejections made by admins for a requisition form in the json response.

        - completedRequests(): Get all records from the requisition_forms table (PK: request_id) with status_name: Completed, Rejected, and Cancelled. Pluck the status_id based on these status_names. Eloquent model relationship: FormStatus.

        - approveRequest(): an admin approves a requisition form
            - adds a new record in the requisition_approvals table by getting the admin_id and the request_id and committing it to this table:

                approved_by = admin_id 
                rejected_by = null
                remarks = remarks (nullable)
                request_id = request_id
                date_approved = isCurrentDateTime()

        - rejectRequest(): an admin rejects a requisition form
            - adds a new record in the requisition_approvals table by getting the admin_id and the request_id and committing it to this table:

                approved_by = null
                rejected_by = admin_id 
                remarks = remarks (nullable)
                request_id = request_id
                date_approved = isCurrentDateTime()

        Note: For approveRequest() and rejectRequest(), validate that the admin has the necessary permissions to approve the request. Only admins with this role_name: 'Head Admin', 'Vice President of Administration', or 'Approving Officer' can approve requests. Pluck the admin_id based on these role_names. Eloquent model relationship: AdminRole.

        - addFee() 

            - Add a fee to a requisition form by creating a new record in the requisition_fees table with the following fields:
                - request_id (FK: request_id from requisition_forms table)
                - added_by (FK: admin_id from admins table)
                - label (e.g., "Facility Fee", "Equipment Fee")
                - fee_amount (decimal value for the fee)
                - discount_amount (decimal value for any discount applied)
                - waived_facility (FK: requested_facility_id from requested_facilities table, nullable)
                - waived_equipment (FK: requested_equipment_id from requested_equipment table, nullable)
                - waived_form (boolean, default false)

        - addDiscount()
            - Add a discount to a requisition form by creating a new record in the requisition_fees table with the following fields:
                - request_id (FK: request_id from requisition_forms table)
                - added_by (FK: admin_id from admins table)
                - label (e.g., "Early Bird Discount", "Member Discount")
                - fee_amount (decimal value for the discount, usually negative)
                - discount_amount (decimal value for any additional discount applied)
                - waived_facility (FK: requested_facility_id from requested_facilities table, nullable)
                - waived_equipment (FK: requested_equipment_id from requested_equipment table, nullable)
                - waived_form (boolean, default false)

        - waiveItem()
            - Waive a specific facility or equipment fee by updating the 'is_waived' field in either requested_facilities or requested_equipment table to true.
            - This could involve updating the corresponding record in the requisition_fees table to indicate that the fee has been waived.
            - Ensure that the requestId corresponds to a valid requisition form and that the admin has the necessary permissions to waive fees.

        - waiveForm()
            - Waive all fees for a requisition form by updating the 'is_waived' field in the requisition_forms table to true.
            - This could involve updating the corresponding record in the requisition_fees table to indicate that all fees have been waived.
            - Ensure that the requestId corresponds to a valid requisition form and that the admin has the necessary permissions to waive all fees.

        - rejectRequest()
            - Add a new record in the requisition_approvals table with the following fields:
                - rejected_by (FK: admin_id from admins table)
                - request_id (FK: request_id from requisition_forms table)


            FormStatus (status_id in form_statuses table):
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

        */

    public function pendingRequests()
    {
        // Get status IDs to exclude
        $excludedStatuses = FormStatus::whereIn('status_name', [
            'Returned',
            'Late Return',
            'Completed',
            'Rejected',
            'Cancelled'
        ])->pluck('status_id');

        // Get pending forms with relationships - ADD requisitionFees relationship
        $forms = RequisitionForm::whereNotIn('status_id', $excludedStatuses)
            ->with([
                'formStatus',
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'requisitionApprovals',
                'requisitionFees.addedBy',
                'purpose',
                'finalizedBy.role', // Eager load roles relationship
                'closedBy'
            ])
            ->get()
            ->map(function ($form) {
                // Calculate tentative fee from facilities and equipment
                $facilityFees = $form->requestedFacilities->sum(function ($facility) {
                    return $facility->is_waived ? 0 : $facility->facility->external_fee;
                });

                $equipmentFees = $form->requestedEquipment->sum(function ($equipment) {
                    return $equipment->is_waived ? 0 : ($equipment->equipment->external_fee * $equipment->quantity);
                });

                $totalTentativeFee = $facilityFees + $equipmentFees;
                if ($form->is_late) {
                    $totalTentativeFee += $form->late_penalty_fee;
                }

                // Calculate approved fee including requisition fees
                $approvedFee = $this->calculateApprovedFee($form);

                // Add approval and rejection counts
                $approvalCount = $form->requisitionApprovals->whereNotNull('approved_by')->count();
                $rejectionCount = $form->requisitionApprovals->whereNotNull('rejected_by')->count();

                // Add finalization info - FIXED: Handle null roles safely
                $isFinalized = $form->is_finalized;
                $finalizedBy = $form->finalizedBy ? [
                    'id' => $form->finalizedBy->admin_id,
                    'name' => $form->finalizedBy->first_name . ' ' . $form->finalizedBy->last_name,
                    'role' => $form->finalizedBy->role->role_title ?? 'Unknown' // Changed to role->role_title
                ] : null;

                // Format requisition fees for response
                $requisitionFees = $form->requisitionFees->map(function ($fee) {
                    return [
                        'fee_id' => $fee->fee_id,
                        'label' => $fee->label,
                        'fee_amount' => (float) $fee->fee_amount,
                        'discount_amount' => (float) $fee->discount_amount,
                        'discount_type' => $fee->discount_type,
                        'type' => $fee->fee_amount > 0 ?
                            ($fee->fee_amount > 0 && $fee->discount_amount > 0 ? 'mixed' : 'fee') :
                            'discount',
                        'added_by' => $fee->addedBy ? [
                            'admin_id' => $fee->addedBy->admin_id,
                            'name' => $fee->addedBy->first_name . ' ' . $fee->addedBy->last_name
                        ] : null,
                        'created_at' => $fee->created_at,
                        'updated_at' => $fee->updated_at
                    ];
                });

                // Return the same structure as pendingRequests() with enhanced fees section
                return [
                    'request_id' => $form->request_id,
                    'user_details' => [
                        'user_type' => $form->user_type,
                        'first_name' => $form->first_name,
                        'last_name' => $form->last_name,
                        'email' => $form->email,
                        'school_id' => $form->school_id,
                        'organization_name' => $form->organization_name,
                        'contact_number' => $form->contact_number
                    ],
                    'form_details' => [
                        'num_participants' => $form->num_participants,
                        'purpose' => $form->purpose->purpose_name,
                        'additional_requests' => $form->additional_requests,
                        'status' => [
                            'name' => $form->formStatus->status_name,
                            'color' => $form->formStatus->color
                        ],
                        'calendar_info' => [
                            'title' => $form->calendar_title,
                            'description' => $form->calendar_description
                        ]
                    ],
                    'schedule' => [
                        'start_date' => $form->start_date,
                        'end_date' => $form->end_date,
                        'start_time' => $form->start_time,
                        'end_time' => $form->end_time
                    ],
                    'requested_items' => [
                        'facilities' => $form->requestedFacilities->map(function ($facility) {
                        return [
                            'requested_facility_id' => $facility->requested_facility_id,
                            'name' => $facility->facility->facility_name,
                            'fee' => $facility->facility->external_fee,
                            'is_waived' => $facility->is_waived
                        ];
                    }),
                        'equipment' => $form->requestedEquipment->map(function ($equipment) {
                        return [
                            'requested_equipment_id' => $equipment->requested_equipment_id,
                            'name' => $equipment->equipment->equipment_name,
                            'quantity' => $equipment->quantity,
                            'fee' => $equipment->equipment->external_fee,
                            'is_waived' => $equipment->is_waived
                        ];
                    })
                    ],
                    'fees' => [
                        'tentative_fee' => $totalTentativeFee,
                        'approved_fee' => $approvedFee,
                        'late_penalty_fee' => $form->late_penalty_fee,
                        'is_late' => $form->is_late,
                        'breakdown' => [
                            'base_fees' => $facilityFees + $equipmentFees,
                            'additional_fees' => $form->requisitionFees->sum('fee_amount'),
                            'discounts' => $form->requisitionFees->sum('discount_amount'),
                            'late_penalty' => $form->is_late ? $form->late_penalty_fee : 0
                        ],
                        'requisition_fees' => $requisitionFees
                    ],
                    'status_tracking' => [
                        'is_late' => $form->is_late,
                        'is_finalized' => $form->is_finalized,
                        'finalized_at' => $form->finalized_at,
                        'finalized_by' => $finalizedBy,
                        'is_closed' => $form->is_closed,
                        'closed_at' => $form->closed_at,
                        'closed_by' => $form->closedBy ? [
                            'id' => $form->closedBy->admin_id,
                            'name' => $form->closedBy->first_name . ' ' . $form->closedBy->last_name
                        ] : null,
                        'returned_at' => $form->returned_at
                    ],
                    'documents' => [
                        'endorser' => $form->endorser,
                        'date_endorsed' => $form->date_endorsed,
                        'formal_letter' => [
                            'url' => $form->formal_letter_url,
                            'public_id' => $form->formal_letter_public_id
                        ],
                        'facility_layout' => [
                            'url' => $form->facility_layout_url,
                            'public_id' => $form->facility_layout_public_id
                        ],
                        'official_receipt' => [
                            'number' => $form->official_receipt_no,
                            'url' => $form->official_receipt_url,
                            'public_id' => $form->official_receipt_public_id
                        ],
                        'proof_of_payment' => [
                            'url' => $form->proof_of_payment_url,
                            'public_id' => $form->proof_of_payment_public_id
                        ]
                    ],
                    'approval_info' => [
                        'approval_count' => $approvalCount,
                        'rejection_count' => $rejectionCount,
                        'is_finalized' => $isFinalized,
                        'finalized_by' => $finalizedBy,
                        'can_finalize' => $approvalCount >= 3 && !$isFinalized,
                        'latest_action' => $form->requisitionApprovals()->latest('date_updated')->first()
                    ],
                    'access_code' => $form->access_code
                ];
            });

        return response()->json($forms);
    }

    public function approveRequest(Request $request, $requestId)
    {
        try {
            \Log::debug('=== APPROVE REQUEST CALLED ===', [
                'request_id' => $requestId,
                'admin_id' => auth()->id(),
                'full_url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all()
            ]);

            $adminId = auth()->id();

            if (!$adminId) {
                \Log::warning('Admin not authenticated');
                return response()->json(['error' => 'Admin not authenticated'], 401);
            }

            // Create approval record - remarks are optional
            $approval = RequisitionApproval::create([
                'approved_by' => $adminId,
                'rejected_by' => null,
                'remarks' => $request->input('remarks', null),
                'request_id' => $requestId,
                'date_updated' => now()
            ]);

            \Log::debug('Approval record created successfully', ['approval_id' => $approval->id]);

            return response()->json([
                'message' => 'Request approved successfully',
                'approval_id' => $approval->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to approve request', [
                'request_id' => $requestId,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to approve request',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectRequest(Request $request, $requestId)
    {
        try {
            \Log::debug('Simple reject request attempt', [
                'request_id' => $requestId,
                'admin_id' => auth()->id(),
                'input_data' => $request->all()
            ]);

            $adminId = auth()->id();

            if (!$adminId) {
                return response()->json(['error' => 'Admin not authenticated'], 401);
            }

            // Create rejection record - remarks are optional
            $rejection = RequisitionApproval::create([
                'approved_by' => null,
                'rejected_by' => $adminId,
                'remarks' => $request->input('remarks', null), // Optional remarks
                'request_id' => $requestId,
                'date_updated' => now()
            ]);

            \Log::debug('Rejection record created', ['rejection_id' => $rejection->id]);

            return response()->json([
                'message' => 'Request rejected successfully',
                'rejection_id' => $rejection->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to reject request', [
                'request_id' => $requestId,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to reject request',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getSimplifiedForms()
    {
        // Get status IDs to exclude (same as index)
        $excludedStatuses = FormStatus::whereIn('status_name', [
            'Returned',
            'Late Return',
            'Completed',
            'Rejected',
            'Cancelled'
        ])->pluck('status_id');

        // Get pending forms with necessary relationships
        $forms = RequisitionForm::whereNotIn('status_id', $excludedStatuses)
            ->with([
                'purpose',
                'formStatus',
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'requisitionApprovals'
            ])
            ->get()
            ->map(function ($form) {
                // Calculate tentative fee
                $facilityFees = $form->requestedFacilities->sum(function ($facility) {
                    return $facility->is_waived ? 0 : $facility->facility->external_fee;
                });

                $equipmentFees = $form->requestedEquipment->sum(function ($equipment) {
                    return $equipment->is_waived ? 0 : ($equipment->equipment->external_fee * $equipment->quantity);
                });

                $totalTentativeFee = $facilityFees + $equipmentFees + ($form->is_late ? $form->late_penalty_fee : 0);

                // Format schedule
                $startDateTime = date('F j, Y g:i A', strtotime($form->start_date . ' ' . $form->start_time));
                $endDateTime = date('F j, Y g:i A', strtotime($form->end_date . ' ' . $form->end_time));

                // Format requested items
                $requestedItems = collect([
                    ...$form->requestedFacilities->map(fn($rf) => $rf->facility->facility_name),
                    ...$form->requestedEquipment->map(fn($re) => $re->equipment->equipment_name . ' (×' . $re->quantity . ')')
                ])->join(', ');

                return [
                    'request_id' => $form->request_id,
                    'purpose' => $form->purpose->purpose_name,
                    'schedule' => $startDateTime . ' to ' . $endDateTime,
                    'requester' => $form->first_name . ' ' . $form->last_name,
                    'status_id' => $form->status_id,
                    'requested_items' => $requestedItems,
                    'tentative_fee' => number_format($totalTentativeFee, 2),
                    'approvals' => $form->requisitionApprovals()->whereNotNull('approved_by')->count() . '/3 approved'
                ];
            });

        return response()->json($forms);
    }

    public function getRequisitionFees($requestId)
    {
        try {
            $fees = RequisitionFee::with('addedBy')
                ->where('request_id', $requestId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($fee) {
                    return [
                        'fee_id' => $fee->fee_id,
                        'label' => $fee->label,
                        'fee_amount' => $fee->fee_amount,
                        'discount_amount' => $fee->discount_amount,
                        'discount_type' => $fee->discount_type,
                        'type' => $fee->fee_amount > 0 ? ($fee->fee_amount > 0 && $fee->discount_amount > 0 ? 'mixed' : 'fee') : 'discount',
                        'added_by' => $fee->addedBy ? [
                            'admin_id' => $fee->addedBy->admin_id,
                            'name' => $fee->addedBy->first_name . ' ' . $fee->addedBy->last_name
                        ] : null,
                        'created_at' => $fee->created_at,
                        'updated_at' => $fee->updated_at
                    ];
                });

            return response()->json($fees);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch fees',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function addFee(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:50',
                'fee_amount' => 'required|numeric|min:0.01',
            ]);

            $admin = auth()->user();

            $fee = RequisitionFee::create([
                'request_id' => $requestId,
                'added_by' => $admin->admin_id,
                'label' => $validatedData['label'],
                'fee_amount' => $validatedData['fee_amount'],
                'discount_amount' => 0,
            ]);

            // Recalculate approved fee
            $form = RequisitionForm::with(['requestedFacilities', 'requestedEquipment', 'requisitionFees'])
                ->findOrFail($requestId);

            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            return response()->json([
                'message' => 'Fee added successfully',
                'fee' => $fee,
                'updated_approved_fee' => $approvedFee
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add fee',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function removeFee($requestId, $feeId)
    {
        try {
            $fee = RequisitionFee::where('request_id', $requestId)
                ->where('fee_id', $feeId)
                ->firstOrFail();

            $fee->delete();

            // Recalculate approved fee
            $form = RequisitionForm::with(['requestedFacilities', 'requestedEquipment', 'requisitionFees'])
                ->findOrFail($requestId);

            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            return response()->json([
                'message' => 'Fee removed successfully',
                'updated_approved_fee' => $approvedFee
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove fee',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function addDiscount(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:50',
                'discount_amount' => 'required|numeric|min:0.01',
                'discount_type' => 'required|in:Fixed,Percentage',
            ]);

            $admin = auth()->user();

            $discount = RequisitionFee::create([
                'request_id' => $requestId,
                'added_by' => $admin->admin_id,
                'label' => $validatedData['label'],
                'fee_amount' => 0,
                'discount_amount' => $validatedData['discount_amount'],
                'discount_type' => $validatedData['discount_type'],
            ]);

            // Recalculate approved fee
            $form = RequisitionForm::with(['requestedFacilities', 'requestedEquipment', 'requisitionFees'])
                ->findOrFail($requestId);

            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            return response()->json([
                'message' => 'Discount added successfully',
                'discount' => $discount,
                'discount_type' => $validatedData['discount_type'],
                'updated_approved_fee' => $approvedFee
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add discount',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function addLatePenalty(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                'penalty_amount' => 'required|numeric|min:0.01'
            ]);

            $form = RequisitionForm::findOrFail($requestId);

            // Check if the requisition is marked as late by the system
            if (!$form->is_late) {
                return response()->json([
                    'error' => 'Cannot add late penalty',
                    'details' => 'This requisition is not marked as late by the system'
                ], 422);
            }

            $form->late_penalty_fee = $validatedData['penalty_amount'];
            $form->save();

            // Recalculate approved fee
            $form->load(['requestedFacilities', 'requestedEquipment', 'requisitionFees']);
            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            return response()->json([
                'message' => 'Late penalty added successfully',
                'penalty_amount' => $form->late_penalty_fee,
                'updated_approved_fee' => $approvedFee
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add late penalty',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function removeLatePenalty(Request $request, $requestId)
    {
        try {
            $form = RequisitionForm::findOrFail($requestId);

            // Only reset the penalty fee, leave is_late status as determined by the system
            $form->late_penalty_fee = 0;
            $form->save();

            // Recalculate approved fee without penalty
            $form->load(['requestedFacilities', 'requestedEquipment', 'requisitionFees']);
            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            return response()->json([
                'message' => 'Late penalty removed successfully',
                'penalty_amount' => $form->late_penalty_fee,
                'updated_approved_fee' => $approvedFee,
                'is_late' => $form->is_late // Include current late status in response
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove late penalty',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function waiveItems(Request $request, $requestId)
    {
        try {
            \Log::debug('Waive items request received', [
                'request_id' => $requestId,
                'waive_all' => $request->waive_all,
                'waived_facilities' => $request->waived_facilities,
                'waived_equipment' => $request->waived_equipment
            ]);

            // Custom validation to check if items belong to this request
            $validator = Validator::make($request->all(), [
                'waive_all' => 'sometimes|boolean',
                'waived_facilities' => 'sometimes|array',
                'waived_facilities.*' => [
                    function ($attribute, $value, $fail) use ($requestId) {
                        $exists = RequestedFacility::where('requested_facility_id', $value)
                            ->where('request_id', $requestId)
                            ->exists();

                        \Log::debug('Facility validation check', [
                            'requested_facility_id' => $value,
                            'request_id' => $requestId,
                            'exists' => $exists
                        ]);

                        if (!$exists) {
                            $fail('The selected facility is invalid for this request.');
                        }
                    }
                ],
                'waived_equipment' => 'sometimes|array',
                'waived_equipment.*' => [
                    function ($attribute, $value, $fail) use ($requestId) {
                        $exists = RequestedEquipment::where('requested_equipment_id', $value)
                            ->where('request_id', $requestId)
                            ->exists();

                        \Log::debug('Equipment validation check', [
                            'requested_equipment_id' => $value,
                            'request_id' => $requestId,
                            'exists' => $exists
                        ]);

                        if (!$exists) {
                            $fail('The selected equipment is invalid for this request.');
                        }
                    }
                ]
            ]);

            if ($validator->fails()) {
                \Log::error('Waive items validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);

                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            DB::beginTransaction();

            if (isset($validatedData['waive_all']) && $validatedData['waive_all']) {
                // Waive all facilities and equipment
                RequestedFacility::where('request_id', $requestId)
                    ->update(['is_waived' => true]);

                RequestedEquipment::where('request_id', $requestId)
                    ->update(['is_waived' => true]);
            } else {
                // Only update waivers for specific items
                // Update facilities based on the provided list
                if (isset($validatedData['waived_facilities'])) {
                    // Waive the specified facilities
                    RequestedFacility::where('request_id', $requestId)
                        ->whereIn('requested_facility_id', $validatedData['waived_facilities'])
                        ->update(['is_waived' => true]);

                    // Unwaive facilities not in the list
                    RequestedFacility::where('request_id', $requestId)
                        ->whereNotIn('requested_facility_id', $validatedData['waived_facilities'])
                        ->update(['is_waived' => false]);
                } else {
                    // If no facilities specified, unwaive all facilities
                    RequestedFacility::where('request_id', $requestId)
                        ->update(['is_waived' => false]);
                }

                // Update equipment based on the provided list
                if (isset($validatedData['waived_equipment'])) {
                    // Waive the specified equipment
                    RequestedEquipment::where('request_id', $requestId)
                        ->whereIn('requested_equipment_id', $validatedData['waived_equipment'])
                        ->update(['is_waived' => true]);

                    // Unwaive equipment not in the list
                    RequestedEquipment::where('request_id', $requestId)
                        ->whereNotIn('requested_equipment_id', $validatedData['waived_equipment'])
                        ->update(['is_waived' => false]);
                } else {
                    // If no equipment specified, unwaive all equipment
                    RequestedEquipment::where('request_id', $requestId)
                        ->update(['is_waived' => false]);
                }
            }

            // Recalculate approved fee
            $form = RequisitionForm::with(['requestedFacilities', 'requestedEquipment', 'requisitionFees'])
                ->findOrFail($requestId);

            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            DB::commit();

            return response()->json([
                'message' => 'Items waived successfully',
                'updated_approved_fee' => $approvedFee,
                'tentative_fee' => $this->calculateTentativeFee($requestId)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to waive items', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to waive items',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function addComment(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                'comment' => 'required|string|max:500'
            ]);

            $admin = auth()->user();

            $comment = RequisitionComment::create([
                'request_id' => $requestId,
                'admin_id' => $admin->admin_id,
                'comment' => $validatedData['comment']
            ]);

            return response()->json([
                'message' => 'Comment added successfully',
                'comment' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add comment',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function getComments($requestId)
    {
        try {
            $comments = RequisitionComment::with('admin')
                ->where('request_id', $requestId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($comments);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch comments',
                'details' => $e->getMessage()
            ], 500);
        }
    }


public function finalizeForm(Request $request, $requestId)
{
    try {
        \Log::debug('Finalize form attempt', [
            'request_id' => $requestId,
            'admin_id' => auth()->id(),
            'input_data' => $request->all()
        ]);

        $validatedData = $request->validate([
            'calendar_title' => 'sometimes|string|max:50|nullable',
            'calendar_description' => 'sometimes|string|max:100|nullable',
            'remarks' => 'sometimes|string|max:500|nullable'
        ]);

        \Log::debug('Validation passed', ['validated_data' => $validatedData]);

        $adminId = auth()->id();

        if (!$adminId) {
            \Log::warning('Admin not authenticated during finalization');
            return response()->json(['error' => 'Admin not authenticated'], 401);
        }

        // Load the form with all necessary relationships
        $form = RequisitionForm::with(['requestedFacilities.facility', 'requestedEquipment.equipment', 'requisitionFees'])
            ->findOrFail($requestId);

        \Log::debug('Form found', [
            'current_status' => $form->status_id,
            'is_finalized' => $form->is_finalized,
            'current_approved_fee' => $form->approved_fee
        ]);

        // Update form fields
        $form->is_finalized = true;
        $form->finalized_at = now();
        $form->finalized_by = $adminId;
        $form->status_id = FormStatus::where('status_name', 'Awaiting Payment')->first()->status_id;

        // Only update calendar fields if provided
        if (!empty($validatedData['calendar_title'])) {
            $form->calendar_title = $validatedData['calendar_title'];
        }

        if (!empty($validatedData['calendar_description'])) {
            $form->calendar_description = $validatedData['calendar_description'];
        }

        // Recalculate the approved fee to ensure it's up to date
        $approvedFee = $this->calculateApprovedFee($form);
        $form->approved_fee = $approvedFee;

        $form->save();

        \Log::debug('Form finalized successfully', [
            'new_status' => $form->status_id,
            'calendar_title' => $form->calendar_title,
            'approved_fee' => $form->approved_fee
        ]);

        // Add a comment if remarks are provided
        if (!empty($validatedData['remarks'])) {
            RequisitionComment::create([
                'request_id' => $requestId,
                'admin_id' => $adminId,
                'comment' => $validatedData['remarks']
            ]);

            \Log::debug('Comment added', ['remarks_length' => strlen($validatedData['remarks'])]);
        }

        // Send email notification to requester
        try {
            $userName = $form->first_name . ' ' . $form->last_name;
            $userEmail = $form->email;

            $emailData = [
                'user_name' => $userName,
                'request_id' => $requestId,
                'approved_fee' => $form->approved_fee, // Use the updated value
                'payment_deadline' => now()->addDays(5)->format('F j, Y')
            ];

            \Log::debug('Sending email with data', $emailData);

            \Mail::send('emails.booking-approved', $emailData, function ($message) use ($userEmail, $userName) {
                $message->to($userEmail, $userName)
                    ->subject('Your Booking Request Has Been Approved – Payment Required');
            });

            \Log::debug('Approval email sent successfully', [
                'recipient' => $userEmail,
                'request_id' => $requestId,
                'approved_fee' => $form->approved_fee
            ]);

        } catch (\Exception $emailError) {
            \Log::error('Failed to send approval email', [
                'request_id' => $requestId,
                'error' => $emailError->getMessage(),
                'recipient' => $form->email,
                'trace' => $emailError->getTraceAsString()
            ]);
            // Don't throw error - email failure shouldn't prevent form finalization
        }

        return response()->json([
            'message' => 'Form finalized successfully',
            'new_status' => 'Awaiting Payment',
            'approved_fee' => $form->approved_fee
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Finalize form validation failed', [
            'request_id' => $requestId,
            'errors' => $e->errors(),
            'input_data' => $request->all()
        ]);

        return response()->json([
            'error' => 'Validation failed',
            'details' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Failed to finalize form', [
            'request_id' => $requestId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'input_data' => $request->all()
        ]);

        return response()->json([
            'error' => 'Failed to finalize form',
            'details' => $e->getMessage()
        ], 500);
    }
}

// Add this method to the AdminApprovalController class
public function cancelRequestPublic($requestId)
{
    try {
        \Log::info('Public cancellation request received', ['request_id' => $requestId]);

        $form = RequisitionForm::findOrFail($requestId);
        
        // Check if the request can be cancelled (only certain statuses)
        $cancellableStatuses = ['Pending Approval', 'Awaiting Payment', 'Scheduled'];
        if (!in_array($form->formStatus->status_name, $cancellableStatuses)) {
            return response()->json([
                'error' => 'Cannot cancel request',
                'details' => 'This request cannot be cancelled in its current status'
            ], 422);
        }

        DB::beginTransaction();

        // Update the requisition form
        $form->status_id = FormStatus::where('status_name', 'Cancelled')->first()->status_id;
        $form->is_closed = true;
        $form->closed_by = null; // No admin since it's public cancellation
        $form->closed_at = now();
        $form->updated_at = now();
        $form->save();

        // Create completed transaction record
        CompletedTransaction::create([
            'request_id' => $requestId,
            'official_receipt_no' => null,
            'official_receipt_url' => null,
            'official_receipt_public_id' => null
        ]);

        DB::commit();

        \Log::info('Request cancelled successfully via public route', ['request_id' => $requestId]);

        return response()->json([
            'message' => 'Request cancelled successfully',
            'request_id' => $requestId
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Failed to cancel request via public route', [
            'request_id' => $requestId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Failed to cancel request',
            'details' => $e->getMessage()
        ], 500);
    }
}

// Rename the existing cancel method for admin use
public function cancelForm(Request $request, $requestId)
{
    try {
        $adminId = auth()->id();

        if (!$adminId) {
            return response()->json(['error' => 'Admin not authenticated'], 401);
        }

        DB::beginTransaction();

        $form = RequisitionForm::findOrFail($requestId);
        
        // Update the requisition form
        $form->status_id = FormStatus::where('status_name', 'Cancelled')->first()->status_id;
        $form->is_closed = true;
        $form->closed_by = $adminId;
        $form->closed_at = now();
        $form->updated_at = now();
        $form->save();

        // Create completed transaction record
        CompletedTransaction::create([
            'request_id' => $requestId,
            'official_receipt_no' => null,
            'official_receipt_url' => null,
            'official_receipt_public_id' => null
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Form cancelled successfully',
            'request_id' => $requestId
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Failed to cancel form as admin', [
            'request_id' => $requestId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Failed to cancel form',
            'details' => $e->getMessage()
        ], 500);
    }
}


    public function closeForm($requestId)
    {
        try {
            $admin = auth()->user();

            $form = RequisitionForm::findOrFail($requestId);

            $form->is_closed = true;
            $form->closed_at = now();
            $form->closed_by = $admin->admin_id;
            $form->status_id = FormStatus::where('status_name', 'Completed')->first()->status_id;
            $form->save();

            return response()->json([
                'message' => 'Form closed successfully',
                'form' => $form
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to close form',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function markReturned(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                'is_late' => 'required|boolean',
                'late_penalty_fee' => 'required_if:is_late,true|numeric|min:0'
            ]);

            $form = RequisitionForm::findOrFail($requestId);

            $form->returned_at = now();
            $form->is_late = $validatedData['is_late'];

            if ($validatedData['is_late']) {
                $form->late_penalty_fee = $validatedData['late_penalty_fee'];
            }

            // Update status based on return time
            if ($validatedData['is_late']) {
                $form->status_id = FormStatus::where('status_name', 'Late Return')->first()->status_id;
            } else {
                $form->status_id = FormStatus::where('status_name', 'Returned')->first()->status_id;
            }

            // Recalculate approved fee
            $form->load(['requestedFacilities', 'requestedEquipment', 'requisitionFees']);
            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            return response()->json([
                'message' => 'Equipment marked as returned',
                'is_late' => $form->is_late,
                'late_penalty_fee' => $form->late_penalty_fee,
                'updated_approved_fee' => $approvedFee
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to mark equipment as returned',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $requestId)
{
    try {
        \Log::debug('Update status request received', [
            'request_id' => $requestId,
            'new_status' => $request->status_name,
            'admin_id' => auth()->id()
        ]);

        $validatedData = $request->validate([
            'status_name' => 'required|string|in:Scheduled,Ongoing,Returned,Late Return,Completed'
        ]);

        $adminId = auth()->id();
        if (!$adminId) {
            \Log::warning('Admin not authenticated during status update');
            return response()->json(['error' => 'Admin not authenticated'], 401);
        }

        $form = RequisitionForm::findOrFail($requestId);
        
        // Get the status ID for the selected status name
        $status = FormStatus::where('status_name', $validatedData['status_name'])->first();
        if (!$status) {
            \Log::error('Status not found', ['status_name' => $validatedData['status_name']]);
            return response()->json(['error' => 'Invalid status'], 422);
        }

        // Update the form status
        $form->status_id = $status->status_id;
        
        // Additional logic based on status
        if (in_array($validatedData['status_name'], ['Returned', 'Late Return', 'Completed', 'Rejected', 'Cancelled'])) {
            $form->is_closed = true;
            $form->closed_at = now();
            $form->closed_by = $adminId;
            
            // Create completed transaction record for finalized statuses
            if (!CompletedTransaction::where('request_id', $requestId)->exists()) {
                CompletedTransaction::create([
                    'request_id' => $requestId,
                    'official_receipt_no' => $form->official_receipt_no,
                    'official_receipt_url' => $form->official_receipt_url,
                    'official_receipt_public_id' => $form->official_receipt_public_id
                ]);
            }
        }
        
        $form->save();

        \Log::info('Status updated successfully', [
            'request_id' => $requestId,
            'old_status' => $form->getOriginal('status_id'),
            'new_status' => $form->status_id,
            'admin_id' => $adminId
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'new_status' => $validatedData['status_name'],
            'status_id' => $status->status_id,
            'color_code' => $status->color_code
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Status update validation failed', [
            'request_id' => $requestId,
            'errors' => $e->errors(),
            'input_data' => $request->all()
        ]);

        return response()->json([
            'error' => 'Validation failed',
            'details' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Failed to update status', [
            'request_id' => $requestId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Failed to update status',
            'details' => $e->getMessage()
        ], 500);
    }
}

    // Calculate & Finalize fees //

    private function calculateBaseFees($form)
    {
        $facilityFees = $form->requestedFacilities->sum(function ($facility) {
            return $facility->is_waived ? 0 : $facility->facility->external_fee;
        });

        $equipmentFees = $form->requestedEquipment->sum(function ($equipment) {
            return $equipment->is_waived ? 0 : ($equipment->equipment->external_fee * $equipment->quantity);
        });

        return $facilityFees + $equipmentFees;
    }

    private function calculateTentativeFee($requestId)
    {
        $form = RequisitionForm::with(['requestedFacilities', 'requestedEquipment'])
            ->findOrFail($requestId);

        $waivers = session()->get('pending_waivers', [])[$requestId] ?? [];

        // Calculate fees considering pending waivers
        $facilityFees = $form->requestedFacilities->sum(function ($facility) use ($waivers) {
            $isWaived = $waivers['facility'][$facility->requested_facility_id] ?? $facility->is_waived;
            return $isWaived ? 0 : $facility->facility->external_fee;
        });

        // Calculate fees considering pending waivers
        $equipmentFees = $form->requestedEquipment->sum(function ($equipment) use ($waivers) {
            $isWaived = $waivers['equipment'][$equipment->requested_equipment_id] ?? $equipment->is_waived;
            return $isWaived ? 0 : $equipment->equipment->external_fee;
        });

        return $facilityFees + $equipmentFees + ($form->is_late ? $form->late_penalty_fee : 0);
    }
    private function calculateApprovedFee($form)
    {
        $baseFees = $this->calculateBaseFees($form);
        $additionalFees = $this->calculateAdditionalFees($form);
        $discounts = $this->calculateTotalDiscounts($form, $baseFees + $additionalFees);

        $approvedFee = $baseFees + $additionalFees - $discounts;

        if ($form->is_late) {
            $approvedFee += $form->late_penalty_fee;
        }

        // Ensure fee doesn't go negative
        return max(0, $approvedFee);
    }

    private function calculateAdditionalFees($form)
    {
        // Sum only positive fee amounts (additional fees)
        return $form->requisitionFees->sum(function ($fee) {
            return max(0, (float) $fee->fee_amount);
        });
    }

    private function calculateTotalDiscounts($form, $subtotal)
    {
        $totalDiscount = 0;

        foreach ($form->requisitionFees as $fee) {
            $discountAmount = (float) $fee->discount_amount;

            if ($discountAmount > 0) {
                if ($fee->discount_type === 'Percentage') {
                    // Calculate percentage discount based on subtotal
                    $percentageDiscount = ($discountAmount / 100) * $subtotal;
                    $totalDiscount += $percentageDiscount;
                } else {
                    // Fixed discount
                    $totalDiscount += $discountAmount;
                }
            }
        }

        return $totalDiscount;
    }

    // Completed Transactions // 

    public function completedRequests()
    {

        /* Documentation:

            - this method gets all completed requisition forms based on these form_statuses (PK: status_id, Model: FormStatus): 'Returned' (5), 'Late Return' (6), 'Completed' (7), 'Rejected' (9), and 'Cancelled' (10). Use the status_name to pluck the status_id.

            - This method should return this json response as in the pendingRequests() method, but with the different status_id logic condition 
        */

        // Get status IDs for completed requests


        $includedStatuses = FormStatus::whereIn('status_name', [
            'Returned',
            'Late Return',
            'Completed',
            'Rejected',
            'Cancelled'
        ])->pluck('status_id');

        // Get completed forms with relationships
        $forms = RequisitionForm::whereIn('status_id', $includedStatuses)
            ->with([
                'formStatus',
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'requisitionApprovals',
                'purpose',
                'finalizedBy',
                'closedBy'
            ])
            ->get()
            ->map(function ($form) {
                // Reuse the same mapping logic as in pendingRequests()
                // Calculate tentative fee from facilities and equipment
                $facilityFees = $form->requestedFacilities->sum(function ($facility) {
                    return $facility->is_waived ? 0 : $facility->facility->external_fee;
                });

                $equipmentFees = $form->requestedEquipment->sum(function ($equipment) {
                    return $equipment->is_waived ? 0 : ($equipment->equipment->external_fee * $equipment->quantity);
                });

                $totalTentativeFee = $facilityFees + $equipmentFees;
                if ($form->is_late) {
                    $totalTentativeFee += $form->late_penalty_fee;
                }

                // Return the same structure as pendingRequests()
                return [
                    'request_id' => $form->request_id,
                    'user_details' => [
                        'user_type' => $form->user_type,
                        'first_name' => $form->first_name,
                        'last_name' => $form->last_name,
                        'email' => $form->email,
                        'school_id' => $form->school_id,
                        'organization_name' => $form->organization_name,
                        'contact_number' => $form->contact_number
                    ],
                    'form_details' => [
                        'num_participants' => $form->num_participants,
                        'purpose' => $form->purpose->purpose_name,
                        'additional_requests' => $form->additional_requests,
                        'status' => [
                            'name' => $form->formStatus->status_name,
                            'color' => $form->formStatus->color
                        ],
                        'calendar_info' => [
                            'title' => $form->calendar_title,
                            'description' => $form->calendar_description
                        ]
                    ],
                    'schedule' => [
                        'start_date' => $form->start_date,
                        'end_date' => $form->end_date,
                        'start_time' => $form->start_time,
                        'end_time' => $form->end_time
                    ],
                    'requested_items' => [
                        'facilities' => $form->requestedFacilities->map(function ($facility) {
                        return [
                            'name' => $facility->facility->facility_name,
                            'fee' => $facility->facility->external_fee,
                            'is_waived' => $facility->is_waived
                        ];
                    }),
                        'equipment' => $form->requestedEquipment->map(function ($equipment) {
                        return [
                            'name' => $equipment->equipment->equipment_name,
                            'quantity' => $equipment->quantity,
                            'fee' => $equipment->equipment->external_fee,
                            'is_waived' => $equipment->is_waived
                        ];
                    })
                    ],
                    'fees' => [
                        'tentative_fee' => $totalTentativeFee,
                        'approved_fee' => $form->approved_fee,
                        'late_penalty_fee' => $form->late_penalty_fee,
                        'is_late' => $form->is_late
                    ],
                    'status_tracking' => [
                        'is_finalized' => $form->is_finalized,
                        'finalized_at' => $form->finalized_at,
                        'finalized_by' => $form->finalizedBy ? [
                            'id' => $form->finalizedBy->admin_id,
                            'name' => $form->finalizedBy->first_name . ' ' . $form->finalizedBy->last_name
                        ] : null,
                        'is_closed' => $form->is_closed,
                        'closed_at' => $form->closed_at,
                        'closed_by' => $form->closedBy ? [
                            'id' => $form->closedBy->admin_id,
                            'name' => $form->closedBy->first_name . ' ' . $form->closedBy->last_name
                        ] : null,
                        'returned_at' => $form->returned_at
                    ],
                    'documents' => [
                        'endorser' => $form->endorser,
                        'date_endorsed' => $form->date_endorsed,
                        'formal_letter' => [
                            'url' => $form->formal_letter_url,
                            'public_id' => $form->formal_letter_public_id
                        ],
                        'facility_layout' => [
                            'url' => $form->facility_layout_url,
                            'public_id' => $form->facility_layout_public_id
                        ],
                        'official_receipt' => [
                            'number' => $form->official_receipt_no,
                            'url' => $form->official_receipt_url,
                            'public_id' => $form->official_receipt_public_id
                        ]
                    ],
                    'approvals' => [
                        'count' => $form->requisitionApprovals()->whereNotNull('approved_by')->count(),
                        'rejections' => $form->requisitionApprovals()->whereNotNull('rejected_by')->count(),
                        'latest_action' => $form->requisitionApprovals()->latest('date_updated')->first()
                    ],
                    'access_code' => $form->access_code
                ];
            });

        return response()->json($forms);
    }

// Get form by access code (Requester side)
public function getFormByAccessCode($accessCode)
{
    try {
        $form = RequisitionForm::with([
            'formStatus:status_id,status_name,color_code',
            'requestedFacilities.facility:facility_id,facility_name',
            'requestedEquipment.equipment:equipment_id,equipment_name',
            'purpose:purpose_id,purpose_name',
            'requisitionFees'
        ])->where('access_code', $accessCode)->firstOrFail();

        // Transform response
        $result = [
            'request_id' => $form->request_id,
            'user_type' => $form->user_type,
            'first_name' => $form->first_name,
            'last_name' => $form->last_name,
            'email' => $form->email,
            'organization_name' => $form->organization_name,
            'contact_number' => $form->contact_number,
            'access_code' => $form->access_code,
            'num_participants' => $form->num_participants,
            'start_date' => $form->start_date,
            'end_date' => $form->end_date,
            'start_time' => $form->start_time,
            'end_time' => $form->end_time,
            'calendar_title' => $form->calendar_title,
            'calendar_description' => $form->calendar_description,

            // Only names
            'requested_facilities' => $form->requestedFacilities->map(fn($rf) => [
                'facility_name' => $rf->facility->facility_name
            ]),

            'requested_equipment' => $form->requestedEquipment->map(fn($re) => [
                'equipment_name' => $re->equipment->equipment_name
            ]),

            'form_status' => $form->formStatus,
            'purpose' => $form->purpose,

            // Use the approved fee
            'total_fee' => $form->approved_fee
        ];

        return response()->json($result);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Form not found',
            'details' => $e->getMessage()
        ], 404);
    }
}

public function uploadPaymentReceipt(Request $request, $requestId)
{
    try {
        \Log::info('Payment receipt upload attempt', [
            'request_id' => $requestId,
            'has_receipt_url' => !empty($request->receipt_url),
            'has_public_id' => !empty($request->public_id)
        ]);

        $validatedData = $request->validate([
            'receipt_url' => 'required|url',
            'public_id' => 'required|string'
        ]);

        $form = RequisitionForm::findOrFail($requestId);

        // Check if form is in correct status for payment
        if ($form->status_id !== FormStatus::where('status_name', 'Awaiting Payment')->first()->status_id) {
            return response()->json([
                'success' => false,
                'message' => 'This request is not awaiting payment.'
            ], 422);
        }

        // Update the form with receipt details
        $form->proof_of_payment_url = $validatedData['receipt_url'];
        $form->proof_of_payment_public_id = $validatedData['public_id'];
        $form->save();

        \Log::info('Payment receipt uploaded successfully', [
            'request_id' => $requestId,
            'receipt_url' => $validatedData['receipt_url']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Receipt uploaded successfully'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Receipt upload validation failed', [
            'request_id' => $requestId,
            'errors' => $e->errors()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Failed to upload payment receipt', [
            'request_id' => $requestId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to upload receipt: ' . $e->getMessage()
        ], 500);
    }
}



}