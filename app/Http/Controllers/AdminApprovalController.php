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
use App\Models\Admin;
use App\Models\RequisitionForm;
use App\Models\RequisitionComment;
use App\Models\AvailabilityStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        /* Documentation:

            view pending and on-going requisition forms

           // Logic to retrieve and display pending and ongoing requisition forms

           // This function queries the RequisitionForm model for forms with status 'Pending Approval' (1), 'Awaiting Payment' (2), 'Scheduled' (3), and 'Ongoing' (4). 

            // admins can only view forms that are under their departments (e.g., a form has a requested facility or requipment that belongs to the admin's department). due to unclear client use case, this is postponed for now.
       */

        // Get status IDs to exclude
        $excludedStatuses = FormStatus::whereIn('status_name', [
            // Expand to read

            'Returned',
            'Late Return',
            'Completed',
            'Rejected',
            'Cancelled'
        ])->pluck('status_id');

        // Get pending forms with relationships
        $forms = RequisitionForm::whereNotIn('status_id', $excludedStatuses)
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

    public function approveRequest(Request $request, $requestId)
    {
        // Validate admin permissions
        $allowedRoles = ['Head Admin', 'Vice President of Administration', 'Approving Officer'];
        $admin = Admin::with('roles')->find(auth()->id());

        if (!$admin->roles->whereIn('role_name', $allowedRoles)->count()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create approval record
        $approval = new RequisitionApproval([
            'approved_by' => $admin->admin_id,
            'rejected_by' => null,
            'remarks' => $request->remarks,
            'request_id' => $requestId,
            'date_updated' => now()
        ]);

        $approval->save();

        return response()->json(['message' => 'Request approved successfully']);
    }

    public function rejectRequest(Request $request, $requestId)
    {
        // Validate admin permissions
        $allowedRoles = ['Head Admin', 'Vice President of Administration', 'Approving Officer'];
        $admin = Admin::with('roles')->find(auth()->id());

        if (!$admin->roles->whereIn('role_name', $allowedRoles)->count()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create rejection record
        $rejection = new RequisitionApproval([
            'approved_by' => null,
            'rejected_by' => $admin->admin_id,
            'remarks' => $request->remarks,
            'request_id' => $requestId,
            'date_updated' => now()
        ]);

        $rejection->save();

        return response()->json(['message' => 'Request rejected successfully']);
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

    // --- NEW FUNCTIONS IN NEED OF TESTING -- //

    public function addFee(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:50',
                'fee_amount' => 'required|numeric|min:1',
            ]);

            $admin = auth()->user();

            $fee = RequisitionFee::create([
                'request_id' => $requestId,
                'added_by' => $admin->admin_id,
                'label' => $validatedData['label'],
                'fee_amount' => $validatedData['fee_amount'],
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

    public function addDiscount(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:50',
                'discount_amount' => 'required|numeric|min:1',
                'discount_type' => 'required|in:Fixed,Percentage',
            ]);

            $admin = auth()->user();

            $discount = RequisitionFee::create([
                'request_id' => $requestId,
                'added_by' => $admin->admin_id,
                'label' => $validatedData['label'],
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
                'penalty_amount' => 'required|numeric|min:0'
            ]);

            $form = RequisitionForm::findOrFail($requestId);
            $form->late_penalty_fee = $validatedData['penalty_amount'];
            $form->is_late = true;
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
            $validatedData = $request->validate([
                'calendar_title' => 'required|string|max:50',
                'calendar_description' => 'required|string|max:100',
                'official_receipt_no' => 'sometimes|string|max:50'
            ]);

            $admin = auth()->user();

            $form = RequisitionForm::findOrFail($requestId);

            // Check if form has enough approvals
            $approvalCount = $form->requisitionApprovals()
                ->whereNotNull('approved_by')
                ->count();

            if ($approvalCount < 3) {
                return response()->json([
                    'error' => 'At least 3 approvals are required to finalize'
                ], 400);
            }

            $form->is_finalized = true;
            $form->finalized_at = now();
            $form->finalized_by = $admin->admin_id;
            $form->calendar_title = $validatedData['calendar_title'];
            $form->calendar_description = $validatedData['calendar_description'];
            $form->official_receipt_no = $validatedData['official_receipt_no'] ?? null;
            $form->status_id = FormStatus::where('status_name', 'Awaiting Payment')->first()->status_id;
            $form->save();

            return response()->json([
                'message' => 'Form finalized successfully',
                'form' => $form
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to finalize form',
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
        $additionalFees = $form->requisitionFees->sum('fee_amount');
        $discounts = $form->requisitionFees->sum('discount_amount');
        
        $approvedFee = $baseFees + $additionalFees - $discounts;
        
        if ($form->is_late) {
            $approvedFee += $form->late_penalty_fee;
        }

        return $approvedFee;
    }

    // Completed Transactions // 

        public function completedRequests() {
       
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
                'formStatus',
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'requisitionApprovals.admin',
                'purpose',
                'requisitionFees'
            ])->where('access_code', $accessCode)->firstOrFail();

            return response()->json($form);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Form not found',
                'details' => $e->getMessage()
            ], 404);
        }
    }


    
    // Old code
    public function manageRequestApproval(Request $request, $requestId)
    {
        try {
            $validatedData = $request->validate([
                // Waiver fields
                'waive_all' => 'sometimes|boolean',
                'waived_items' => 'sometimes|array',
                'waived_items.*.item_type' => 'required_with:waived_items|in:facility,equipment',
                'waived_items.*.item_id' => 'required_with:waived_items|integer',
                'waived_items.*.is_waived' => 'required_with:waived_items|boolean',

                // Approval fields
                'action' => 'sometimes|in:approve,reject,finalize',
                'remarks' => 'nullable|string|max:255',
                'finalized_by' => 'required_if:action,finalize|exists:admins,admin_id',

                // Fee/discount fields
                'additional_fees' => 'sometimes|array',
                'additional_fees.*.label' => 'required_with:additional_fees|string|max:50',
                'additional_fees.*.fee_amount' => 'required_with:additional_fees|numeric|min:0',
                'additional_fees.*.discount_amount' => 'sometimes|numeric|min:0',
                'additional_fees.*.waived_facility' => 'sometimes|nullable|exists:requested_facilities,requested_facility_id',
                'additional_fees.*.waived_equipment' => 'sometimes|nullable|exists:requested_equipment,requested_equipment_id',
                'additional_fees.*.waived_form' => 'sometimes|boolean',

                // Calendar fields (only required when finalizing)
                'calendar_title' => 'required_if:action,finalize|string|max:50',
                'calendar_description' => 'required_if:action,finalize|string|max:100'
            ]);

            // Get authenticated admin
            $admin = auth()->user();

            // Get the requisition form with all requested items and approvals
            $form = RequisitionForm::with([
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'requisitionApprovals',
                'requisitionFees'
            ])->findOrFail($requestId);

            // Start a database transaction
            DB::beginTransaction();

            // Handle waivers if present in request
            if ($request->has('waive_all')) {
                RequestedFacility::where('request_id', $requestId)
                    ->update(['is_waived' => $validatedData['waive_all']]);

                RequestedEquipment::where('request_id', $requestId)
                    ->update(['is_waived' => $validatedData['waive_all']]);
            } elseif ($request->has('waived_items')) {
                foreach ($validatedData['waived_items'] as $item) {
                    if ($item['item_type'] === 'facility') {
                        RequestedFacility::where('request_id', $requestId)
                            ->where('requested_facility_id', $item['item_id'])
                            ->update(['is_waived' => $item['is_waived']]);
                    } else {
                        RequestedEquipment::where('request_id', $requestId)
                            ->where('requested_equipment_id', $item['item_id'])
                            ->update(['is_waived' => $item['is_waived']]);
                    }
                }
            }

            // Handle additional fees/discounts if present
            if ($request->has('additional_fees')) {
                foreach ($validatedData['additional_fees'] as $feeData) {
                    RequisitionFee::create([
                        'request_id' => $requestId,
                        'added_by' => $admin->admin_id,
                        'label' => $feeData['label'],
                        'fee_amount' => $feeData['fee_amount'],
                        'discount_amount' => $feeData['discount_amount'] ?? 0,
                        'waived_facility' => $feeData['waived_facility'] ?? null,
                        'waived_equipment' => $feeData['waived_equipment'] ?? null,
                        'waived_form' => $feeData['waived_form'] ?? false
                    ]);
                }
            }

            // Recalculate all fees
            $baseFees = $this->calculateBaseFees($form);
            $additionalFees = $form->requisitionFees->sum('fee_amount');
            $discounts = $form->requisitionFees->sum('discount_amount');

            $approvedFee = $baseFees + $additionalFees - $discounts;
            if ($form->is_late) {
                $approvedFee += $form->late_penalty_fee;
            }

            // Update the approved fee in the form
            $form->approved_fee = $approvedFee;

            // Handle approval/rejection/finalization if specified
            if ($request->has('action')) {
                $approvalData = [
                    'request_id' => $requestId,
                    'remarks' => $validatedData['remarks'] ?? null,
                    'date_updated' => now()
                ];

                switch ($validatedData['action']) {
                    case 'approve':
                        $approvalData['approved_by'] = $admin->admin_id;
                        $approvalData['rejected_by'] = null;
                        RequisitionApproval::create($approvalData);
                        break;

                    case 'reject':
                        $approvalData['rejected_by'] = $admin->admin_id;
                        $approvalData['approved_by'] = null;
                        RequisitionApproval::create($approvalData);
                        $form->status_id = FormStatus::where('status_name', 'Rejected')->first()->status_id;
                        break;

                    case 'finalize':
                        $approvalCount = $form->requisitionApprovals()
                            ->whereNotNull('approved_by')
                            ->count();

                        if ($approvalCount < 3) {
                            throw new \Exception('At least 3 approvals are required to finalize');
                        }

                        // Update finalization fields
                        $form->is_finalized = true;
                        $form->finalized_at = now();
                        $form->finalized_by = $validatedData['finalized_by'];
                        $form->status_id = FormStatus::where('status_name', 'Awaiting Payment')->first()->status_id;

                        // Update calendar details
                        $form->calendar_title = $validatedData['calendar_title'];
                        $form->calendar_description = $validatedData['calendar_description'];
                        break;
                }
            }

            $form->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Request updated successfully',
                'approved_fee' => $approvedFee,
                'status_id' => $form->status_id,
                'is_finalized' => $form->is_finalized,
                'approval_count' => $form->requisitionApprovals()->whereNotNull('approved_by')->count(),
                'can_finalize' => $form->requisitionApprovals()->whereNotNull('approved_by')->count() >= 3 && !$form->is_finalized,
                'calendar_details' => [
                        'title' => $form->calendar_title,
                        'description' => $form->calendar_description
                    ],
                'updated_items' => [
                    'facilities' => $form->requestedFacilities->map(function ($facility) {
                        return [
                            'id' => $facility->requested_facility_id,
                            'is_waived' => $facility->is_waived
                        ];
                    }),
                    'equipment' => $form->requestedEquipment->map(function ($equipment) {
                        return [
                            'id' => $equipment->requested_equipment_id,
                            'is_waived' => $equipment->is_waived
                        ];
                    })
                ],
                'added_fees' => $form->requisitionFees->map(function ($fee) {
                    return [
                        'label' => $fee->label,
                        'fee_amount' => $fee->fee_amount,
                        'discount_amount' => $fee->discount_amount
                    ];
                }),
                'action_performed' => $request->has('action') ? $validatedData['action'] : null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update request',
                'details' => $e->getMessage()
            ], 500);
        }
    } 


}