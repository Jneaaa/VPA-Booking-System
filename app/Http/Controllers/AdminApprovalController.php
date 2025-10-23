<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequisitionApproval;
use App\Models\RequestedFacility;
use App\Models\RequestedEquipment;
use App\Models\RequisitionFee;
use App\Models\FormStatus;
use App\Models\CompletedTransaction;
use App\Models\RequisitionForm;
use App\Models\RequisitionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/* AdminApprovalController — Summary Documentation

This controller manages the entire admin-side approval and fee handling process
for requisition forms within the booking system. It provides endpoints for viewing,
approving, rejecting, and modifying requests, as well as managing related financial actions.

The controller includes methods to fetch pending and completed requests, allowing
admins to review requisition forms that are awaiting approval or have been finalized.
Only authorized roles such as Head Admin, Vice President of Administration, and
Approving Officer can perform approval or rejection actions. When a request is approved
or rejected, a corresponding record is created in the requisition_approvals table,
capturing details such as the admin who performed the action, remarks, and the timestamp.

It also handles the financial side of the approval process. Through dedicated methods,
admins can add fees or discounts to a requisition form, each stored in the
requisition_fees table with details like label, amount, and references to any
waived facilities or equipment. Additional methods allow specific items or entire
forms to be marked as waived, updating related database records to reflect that
charges have been removed or discounted.

Overall, the AdminApprovalController serves as the core module for managing
the administrative workflow of requisition approval, ensuring that all actions,
statuses, and fee-related transactions are properly validated, recorded, and
restricted to the appropriate user roles.
*/


class AdminApprovalController extends Controller
{

    public function getApprovalHistory($requestId)
    {
        try {
            $approvals = RequisitionApproval::with(['approvedBy', 'rejectedBy'])
                ->where('request_id', $requestId)
                ->orderBy('date_updated', 'desc')
                ->get()
                ->map(function ($approval) {
                    $admin = $approval->approvedBy ?: $approval->rejectedBy;
                    $action = $approval->approved_by ? 'approved' : 'rejected';
                    $actionClass = $approval->approved_by ? 'text-success' : 'text-danger';
                    $actionIcon = $approval->approved_by ? 'fa-thumbs-up' : 'fa-thumbs-down';

                    return [
                        'admin_id' => $admin ? $admin->admin_id : null, // Add admin_id
                        'admin_name' => $admin ? $admin->first_name . ' ' . $admin->last_name : 'Unknown Admin',
                        'admin_photo' => $admin->photo_url ?? null,
                        'action' => $action,
                        'action_class' => $actionClass,
                        'action_icon' => $actionIcon,
                        'remarks' => $approval->remarks,
                        'date_updated' => $approval->date_updated,
                        'formatted_date' => Carbon::parse($approval->date_updated)->format('M j, Y g:i A')
                    ];
                });

            return response()->json($approvals);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch approval history', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch approval history',
                'details' => $e->getMessage()
            ], 500);
        }
    }

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

                // Get overlapping requests that share facilities or equipment
                $overlappingRequests = $this->getOverlappingRequests($form);

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
                            'id' => $form->formStatus->status_id,
                            'name' => $form->formStatus->status_name,
                            'color' => $form->formStatus->color_code
                        ],
                        'calendar_info' => [
                            'title' => $form->calendar_title,
                            'description' => $form->calendar_description
                        ],
                        'official_receipt_num' => $form->official_receipt_num
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
                            'rate_type' => $facility->facility->rate_type,
                            'is_waived' => $facility->is_waived
                        ];
                    }),
                        'equipment' => $form->requestedEquipment->map(function ($equipment) {
                        return [
                            'requested_equipment_id' => $equipment->requested_equipment_id, // Single ID
                            'name' => $equipment->equipment->equipment_name,
                            'quantity' => $equipment->quantity,
                            'fee' => $equipment->equipment->external_fee,
                            'rate_type' => $equipment->equipment->rate_type,
                            'is_waived' => $equipment->is_waived,
                            'total_fee' => $equipment->equipment->external_fee * $equipment->quantity
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
                    'overlapping_requests' => $overlappingRequests,
                    'access_code' => $form->access_code
                ];
            });

        return response()->json($forms);
    }

    // Add this helper method to find overlapping requests
    private function getOverlappingRequests($currentForm)
    {
        try {
            // Get current form's facility and equipment IDs
            $currentFacilityIds = $currentForm->requestedFacilities->pluck('facility_id')->toArray();
            $currentEquipmentIds = $currentForm->requestedEquipment->pluck('equipment_id')->toArray();

            if (empty($currentFacilityIds) && empty($currentEquipmentIds)) {
                return [];
            }

            // Get status IDs to exclude (completed forms)
            $excludedStatuses = FormStatus::whereIn('status_name', [
                'Returned',
                'Late Return',
                'Completed',
                'Rejected',
                'Cancelled'
            ])->pluck('status_id');

            // Find other pending requests that share facilities or equipment
            $overlappingRequests = RequisitionForm::where('request_id', '!=', $currentForm->request_id)
                ->whereNotIn('status_id', $excludedStatuses)
                ->where(function ($query) use ($currentFacilityIds, $currentEquipmentIds) {
                    // Check for shared facilities
                    if (!empty($currentFacilityIds)) {
                        $query->whereHas('requestedFacilities', function ($q) use ($currentFacilityIds) {
                            $q->whereIn('facility_id', $currentFacilityIds);
                        });
                    }

                    // Check for shared equipment
                    if (!empty($currentEquipmentIds)) {
                        $query->orWhereHas('requestedEquipment', function ($q) use ($currentEquipmentIds) {
                            $q->whereIn('equipment_id', $currentEquipmentIds);
                        });
                    }
                })
                ->with(['formStatus', 'requestedFacilities.facility', 'requestedEquipment.equipment'])
                ->get()
                ->map(function ($form) {
                    return [
                        'request_id' => $form->request_id,
                        'requester_name' => $form->first_name . ' ' . $form->last_name,
                        'status' => $form->formStatus->status_name,
                        'schedule' => [
                            'start_date' => $form->start_date,
                            'end_date' => $form->end_date,
                            'start_time' => $form->start_time,
                            'end_time' => $form->end_time
                        ],
                        'shared_facilities' => $form->requestedFacilities->pluck('facility.facility_name')->toArray(),
                        'shared_equipment' => $form->requestedEquipment->groupBy('equipment.equipment_name')
                            ->map(function ($group) {
                                return $group->first()->equipment->equipment_name . ' (×' . $group->sum('quantity') . ')';
                            })->values()->toArray()
                    ];
                });

            return $overlappingRequests;

        } catch (\Exception $e) {
            \Log::error('Error finding overlapping requests', [
                'request_id' => $currentForm->request_id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function getRequisitionFormById($requestId)
    {
        try {
            \Log::debug('Fetching specific requisition form', ['request_id' => $requestId]);

            $form = RequisitionForm::with([
                'formStatus',
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'requisitionApprovals',
                'requisitionFees.addedBy',
                'purpose',
                'finalizedBy.role',
                'closedBy'
            ])->findOrFail($requestId);

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

            // Add finalization info
            $isFinalized = $form->is_finalized;
            $finalizedBy = $form->finalizedBy ? [
                'id' => $form->finalizedBy->admin_id,
                'name' => $form->finalizedBy->first_name . ' ' . $form->finalizedBy->last_name,
                'role' => $form->finalizedBy->role->role_title ?? 'Unknown'
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

            // Return the same structure as pendingRequests() but for single form
            $response = [
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
                        'color' => $form->formStatus->color_code
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
                            'rate_type' => $facility->facility->rate_type,
                            'is_waived' => $facility->is_waived
                        ];
                    }),
                    'equipment' => $form->requestedEquipment->groupBy('equipment.equipment_id')->map(function ($group) {
                        $firstItem = $group->first();
                        $totalQuantity = $group->sum('quantity');

                        return [
                            'requested_equipment_ids' => $group->pluck('requested_equipment_id')->toArray(),
                            'name' => $firstItem->equipment->equipment_name,
                            'quantity' => $totalQuantity,
                            'fee' => $firstItem->equipment->external_fee,
                            'rate_type' => $firstItem->equipment->rate_type,
                            'is_waived' => $firstItem->is_waived,
                            'total_fee' => $firstItem->equipment->external_fee * $totalQuantity
                        ];
                    })->values()
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
                    'approvals' => $form->requisitionApprovals->whereNotNull('approved_by')->map(function ($approval) {
                        return [
                            'admin_id' => $approval->approved_by,
                            'date_updated' => $approval->date_updated
                        ];
                    }),
                    'rejections' => $form->requisitionApprovals->whereNotNull('rejected_by')->map(function ($rejection) {
                        return [
                            'admin_id' => $rejection->rejected_by,
                            'date_updated' => $rejection->date_updated
                        ];
                    }),
                    'is_finalized' => $isFinalized,
                    'finalized_by' => $finalizedBy,
                    'can_finalize' => $approvalCount >= 3 && !$isFinalized,
                    'latest_action' => $form->requisitionApprovals()->latest('date_updated')->first()
                ],
                'access_code' => $form->access_code
            ];

            \Log::debug('Successfully fetched requisition form', [
                'request_id' => $requestId,
                'approval_count' => $approvalCount,
                'rejection_count' => $rejectionCount
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch requisition form by ID', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch requisition form',
                'details' => $e->getMessage()
            ], 404);
        }
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

            // Create comment record for activity timeline
            $commentText = "Approved this request" . ($request->input('remarks') ? ": " . $request->input('remarks') : "");
            RequisitionComment::create([
                'request_id' => $requestId,
                'admin_id' => $adminId,
                'comment' => $commentText
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

            // Create comment record for activity timeline
            $commentText = "Rejected this request" . ($request->input('remarks') ? ": " . $request->input('remarks') : "");
            RequisitionComment::create([
                'request_id' => $requestId,
                'admin_id' => $adminId,
                'comment' => $commentText
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
        // Get status IDs to exclude
        $excludedStatuses = FormStatus::whereIn('status_name', [
            'Late',
            'Ongoing',
            'Scheduled',
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

                // Group equipment by name and sum quantities
                $equipmentGroups = $form->requestedEquipment->groupBy('equipment.equipment_name')
                    ->map(function ($group) {
                    $totalQuantity = $group->sum('quantity');
                    return $group->first()->equipment->equipment_name . ' (×' . $totalQuantity . ')';
                });

                // Format requested items
                $requestedItems = collect([
                    ...$form->requestedFacilities->map(fn($rf) => $rf->facility->facility_name),
                    ...$equipmentGroups->values()
                ])->join(', ');

                return [
                    'request_id' => $form->request_id,
                    'purpose' => $form->purpose->purpose_name,
                    'schedule' => $startDateTime . ' to ' . $endDateTime,
                    'requester' => $form->first_name . ' ' . $form->last_name,
                    'status_id' => $form->status_id,
                    'requested_items' => $requestedItems,
                    'tentative_fee' => number_format($totalTentativeFee, 2),
                    'approvals' => $form->requisitionApprovals()->whereNotNull('approved_by')->count(),
                    'rejections' => $form->requisitionApprovals()->whereNotNull('rejected_by')->count(),
                    'date_submitted' => $form->created_at
                ];
            })
            ->sortBy('status_id') // Sort ascending by status_id
            ->values(); // Reset indexes

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
                'penalty_amount' => 'required|numeric|min:0'
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

            // First, let's log all equipment for this request to see what should be valid
            $validEquipmentIds = RequestedEquipment::where('request_id', $requestId)
                ->pluck('requested_equipment_id')
                ->toArray();

            $validFacilityIds = RequestedFacility::where('request_id', $requestId)
                ->pluck('requested_facility_id')
                ->toArray();

            \Log::debug('Valid IDs for this request', [
                'valid_equipment_ids' => $validEquipmentIds,
                'valid_facility_ids' => $validFacilityIds,
                'requested_equipment' => $request->waived_equipment,
                'requested_facilities' => $request->waived_facilities
            ]);

            // Custom validation to check if items belong to this request
            $validator = Validator::make($request->all(), [
                'waive_all' => 'sometimes|boolean',
                'waived_facilities' => 'sometimes|array',
                'waived_facilities.*' => [
                    function ($attribute, $value, $fail) use ($requestId, $validFacilityIds) {
                        if (!in_array($value, $validFacilityIds)) {
                            $fail("The selected facility (ID: $value) is invalid for this request. Valid facilities: " . implode(', ', $validFacilityIds));
                        }
                    }
                ],
                'waived_equipment' => 'sometimes|array',
                'waived_equipment.*' => [
                    function ($attribute, $value, $fail) use ($requestId, $validEquipmentIds) {
                        if (!in_array($value, $validEquipmentIds)) {
                            $fail("The selected equipment (ID: $value) is invalid for this request. Valid equipment: " . implode(', ', $validEquipmentIds));
                        }
                    }
                ]
            ]);

            if ($validator->fails()) {
                \Log::error('Waive items validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all(),
                    'valid_equipment_ids' => $validEquipmentIds,
                    'valid_facility_ids' => $validFacilityIds
                ]);

                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors(),
                    'debug' => [
                        'valid_equipment_ids' => $validEquipmentIds,
                        'valid_facility_ids' => $validFacilityIds
                    ]
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

        // Remove 'remarks' from validation
        $validatedData = $request->validate([
            'calendar_title' => 'sometimes|string|max:50|nullable',
            'calendar_description' => 'sometimes|string|max:100|nullable',
        ]);

        \Log::debug('Validation passed', ['validated_data' => $validatedData]);

        $adminId = auth()->id();

        if (!$adminId) {
            \Log::warning('Admin not authenticated during finalization');
            return response()->json(['error' => 'Admin not authenticated'], 401);
        }

        $form = RequisitionForm::with([
            'requestedFacilities.facility',
            'requestedEquipment.equipment',
            'requisitionFees'
        ])->findOrFail($requestId);

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

        if (!empty($validatedData['calendar_title'])) {
            $form->calendar_title = $validatedData['calendar_title'];
        }

        if (!empty($validatedData['calendar_description'])) {
            $form->calendar_description = $validatedData['calendar_description'];
        }

        $form->approved_fee = $this->calculateApprovedFee($form);
        $form->save();

        \Log::debug('Form finalized successfully', [
            'new_status' => $form->status_id,
            'calendar_title' => $form->calendar_title,
            'approved_fee' => $form->approved_fee
        ]);

        // Email notification
        try {
            $userName = $form->first_name . ' ' . $form->last_name;
            $userEmail = $form->email;

            $emailData = [
                'user_name' => $userName,
                'request_id' => $requestId,
                'approved_fee' => $form->approved_fee,
                'payment_deadline' => now()->addDays(5)->format('F j, Y'),
                'access_code' => $form->access_code // Add access_code to email data
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

            // Create completed transaction record
            CompletedTransaction::create([
                'request_id' => $requestId,
                'official_receipt_no' => null,
                'official_receipt_url' => null,
                'official_receipt_public_id' => null
            ]);

            DB::commit();

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
                'status_name' => 'required|string|in:Scheduled,Ongoing,Late,Returned,Late Return,Completed',
                'late_penalty_fee' => 'sometimes|nullable|numeric|min:0'
            ]);

            $adminId = auth()->id();
            if (!$adminId) {
                \Log::warning('Admin not authenticated during status update');
                return response()->json(['error' => 'Admin not authenticated'], 401);
            }

            $form = RequisitionForm::with('formStatus')->findOrFail($requestId);

            // VALIDATION: Can only mark as Late if current status is Ongoing
            if ($validatedData['status_name'] === 'Late') {
                $currentStatus = $form->formStatus->status_name;
                if ($currentStatus !== 'Ongoing') {
                    return response()->json([
                        'error' => 'Cannot mark as Late',
                        'details' => 'Can only mark forms as Late when they are in Ongoing status. Current status: ' . $currentStatus
                    ], 422);
                }
            }

            // Get the status ID for the selected status name
            $status = FormStatus::where('status_name', $validatedData['status_name'])->first();
            if (!$status) {
                \Log::error('Status not found', ['status_name' => $validatedData['status_name']]);
                return response()->json(['error' => 'Invalid status'], 422);
            }

            // Handle Late status specifically
            if ($validatedData['status_name'] === 'Late') {
                $form->is_late = true;

                // Set late penalty fee if provided
                if (isset($validatedData['late_penalty_fee']) && $validatedData['late_penalty_fee'] > 0) {
                    $form->late_penalty_fee = $validatedData['late_penalty_fee'];
                }
            }
            // Handle unmarking late (when changing from Late to another status)
            elseif ($form->formStatus->status_name === 'Late' && $validatedData['status_name'] !== 'Late') {
                $form->is_late = false;
                $form->late_penalty_fee = 0; // Reset penalty fee
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

            // Recalculate approved fee after status change
            $form->load(['requestedFacilities', 'requestedEquipment', 'requisitionFees']);
            $approvedFee = $this->calculateApprovedFee($form);
            $form->approved_fee = $approvedFee;
            $form->save();

            \Log::info('Status updated successfully', [
                'request_id' => $requestId,
                'old_status' => $form->getOriginal('status_id'),
                'new_status' => $form->status_id,
                'is_late' => $form->is_late,
                'late_penalty_fee' => $form->late_penalty_fee,
                'admin_id' => $adminId
            ]);

            return response()->json([
                'message' => 'Status updated successfully',
                'new_status' => $validatedData['status_name'],
                'status_id' => $status->status_id,
                'color_code' => $status->color_code,
                'is_late' => $form->is_late,
                'late_penalty_fee' => $form->late_penalty_fee,
                'updated_approved_fee' => $approvedFee
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

    // Add better error logging to the calculateBaseFees method
    private function calculateBaseFees($form)
    {
        try {
            \Log::debug('Calculating base fees', [
                'request_id' => $form->request_id,
                'facilities_count' => $form->requestedFacilities->count(),
                'equipment_count' => $form->requestedEquipment->count()
            ]);

            // Calculate facility fees with rate_type logic
            $facilityFees = $form->requestedFacilities->sum(function ($facility) use ($form) {
                if ($facility->is_waived) {
                    return 0;
                }

                $fee = $facility->facility->external_fee;

                // Check if rate_type is "Per Hour" and calculate based on duration
                if ($facility->facility->rate_type === 'Per Hour') {
                    try {
                        $startDateTime = Carbon::parse($form->start_date . ' ' . $form->start_time);
                        $endDateTime = Carbon::parse($form->end_date . ' ' . $form->end_time);
                        $durationInHours = $startDateTime->diffInHours($endDateTime);

                        \Log::debug('Per Hour facility calculation', [
                            'facility_id' => $facility->facility_id,
                            'base_fee' => $fee,
                            'duration_hours' => $durationInHours,
                            'total' => $fee * $durationInHours
                        ]);

                        return $fee * $durationInHours;
                    } catch (\Exception $e) {
                        \Log::error('Error calculating per hour facility fee', [
                            'facility_id' => $facility->facility_id,
                            'error' => $e->getMessage()
                        ]);
                        return $fee; // Fallback to base fee
                    }
                }

                // For "Per Event" or any other rate type, return the base fee
                return $fee;
            });

            // Calculate equipment fees with rate_type logic
            $equipmentFees = $form->requestedEquipment->sum(function ($equipment) use ($form) {
                if ($equipment->is_waived) {
                    return 0;
                }

                $fee = $equipment->equipment->external_fee;

                // Check if rate_type is "Per Hour" and calculate based on duration
                if ($equipment->equipment->rate_type === 'Per Hour') {
                    try {
                        $startDateTime = Carbon::parse($form->start_date . ' ' . $form->start_time);
                        $endDateTime = Carbon::parse($form->end_date . ' ' . $form->end_time);
                        $durationInHours = $startDateTime->diffInHours($endDateTime);

                        \Log::debug('Per Hour equipment calculation', [
                            'equipment_id' => $equipment->equipment_id,
                            'base_fee' => $fee,
                            'quantity' => $equipment->quantity,
                            'duration_hours' => $durationInHours,
                            'total' => ($fee * $durationInHours) * $equipment->quantity
                        ]);

                        return ($fee * $durationInHours) * $equipment->quantity;
                    } catch (\Exception $e) {
                        \Log::error('Error calculating per hour equipment fee', [
                            'equipment_id' => $equipment->equipment_id,
                            'error' => $e->getMessage()
                        ]);
                        return $fee * $equipment->quantity; // Fallback to base fee
                    }
                }

                // For "Per Event" or any other rate type, return the base fee multiplied by quantity
                return $fee * $equipment->quantity;
            });

            $total = $facilityFees + $equipmentFees;

            \Log::debug('Base fees calculation completed', [
                'request_id' => $form->request_id,
                'facility_fees' => $facilityFees,
                'equipment_fees' => $equipmentFees,
                'total_base_fees' => $total
            ]);

            return $total;

        } catch (\Exception $e) {
            \Log::error('Error in calculateBaseFees', [
                'request_id' => $form->request_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0; // Return 0 on error to prevent calculation issues
        }
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

    // Add this method to AdminApprovalController.php

   public function markAsScheduled(Request $request, $requestId)
{
    try {
        \Log::debug('Mark as scheduled request received', [
            'request_id' => $requestId,
            'admin_id' => auth()->id(),
            'official_receipt_num' => $request->official_receipt_num
        ]);

        $validatedData = $request->validate([
            'official_receipt_num' => 'required|string|max:50|unique:requisition_forms,official_receipt_num',
            'calendar_title' => 'sometimes|string|max:50|nullable',
            'calendar_description' => 'sometimes|string|max:100|nullable',
        ]);

        $adminId = auth()->id();
        if (!$adminId) {
            return response()->json(['error' => 'Admin not authenticated'], 401);
        }

        $form = RequisitionForm::with([
            'requestedFacilities.facility',
            'requestedEquipment.equipment',
            'requisitionFees',
            'purpose',
            'formStatus'
        ])->findOrFail($requestId);

        // Update equipment conditions to "In Use"
        $this->updateEquipmentConditions($form);

        // Update form with official receipt number and status
        $scheduledStatus = FormStatus::where('status_name', 'Scheduled')->first();
        if (!$scheduledStatus) {
            throw new \Exception('Scheduled status not found');
        }

        $form->official_receipt_num = $validatedData['official_receipt_num'];
        $form->status_id = $scheduledStatus->status_id;

        if (!empty($validatedData['calendar_title'])) {
            $form->calendar_title = $validatedData['calendar_title'];
        }

        if (!empty($validatedData['calendar_description'])) {
            $form->calendar_description = $validatedData['calendar_description'];
        }

        $form->save();

        // Send confirmation email
        $this->sendScheduledConfirmationEmail($form);

        \Log::info('Form marked as scheduled successfully', [
            'request_id' => $requestId,
            'official_receipt_num' => $form->official_receipt_num,
            'admin_id' => $adminId
        ]);

        return response()->json([
            'message' => 'Form marked as scheduled successfully',
            'official_receipt_num' => $form->official_receipt_num,
            'new_status' => 'Scheduled'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Mark as scheduled validation failed', [
                'request_id' => $requestId,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Failed to mark form as scheduled', [
                'request_id' => $requestId,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to mark form as scheduled',
                'details' => $e->getMessage()
            ], 500);
        }
    }

public function getEquipmentStatus($requestId)
{
    try {
        $form = RequisitionForm::with([
            'requestedEquipment.equipment.items.condition'
        ])->findOrFail($requestId);

        $equipmentStatus = [];

        foreach ($form->requestedEquipment as $reqEquipment) {
            $equipment = $reqEquipment->equipment;
            
            // Get items that are currently marked as "In Use" (condition_id = 6)
            // for this equipment type (we assume they're being used for this request)
            $inUseItems = \App\Models\EquipmentItem::where('equipment_id', $equipment->equipment_id)
                ->where('condition_id', 6) // In Use
                ->with('condition')
                ->limit($reqEquipment->quantity) // Only show up to the requested quantity
                ->get();

            foreach ($inUseItems as $item) {
                $equipmentStatus[] = [
                    'equipment_name' => $equipment->equipment_name,
                    'item_id' => $item->item_id,
                    'condition_name' => $item->condition->condition_name,
                    'condition_color' => $item->condition->color_code
                ];
            }
        }

        return response()->json([
            'equipment_status' => $equipmentStatus,
            'has_equipment' => count($equipmentStatus) > 0
        ]);

    } catch (\Exception $e) {
        \Log::error('Failed to fetch equipment status', [
            'request_id' => $requestId,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'error' => 'Failed to fetch equipment status',
            'details' => $e->getMessage()
        ], 500);
    }
}

    private function sendScheduledConfirmationEmail($form)
    {
        try {
            $userName = $form->first_name . ' ' . $form->last_name;
            $userEmail = $form->email;

            $emailData = [
                'user_name' => $userName,
                'request_id' => $form->request_id,
                'official_receipt_num' => $form->official_receipt_num,
                'purpose' => $form->purpose->purpose_name,
                'start_date' => $form->start_date,
                'start_time' => $form->start_time,
                'end_date' => $form->end_date,
                'end_time' => $form->end_time,
                'approved_fee' => $form->approved_fee
            ];

            // Use view() instead of loading the file directly
            \Mail::send('emails.booking-scheduled', $emailData, function ($message) use ($userEmail, $userName) {
                $message->to($userEmail, $userName)
                    ->subject('Your Booking Has Been Scheduled – Official Receipt Generated');
            });

            \Log::debug('Scheduled confirmation email sent successfully', [
                'recipient' => $userEmail,
                'request_id' => $form->request_id,
                'official_receipt_num' => $form->official_receipt_num
            ]);

        } catch (\Exception $emailError) {
            \Log::error('Failed to send scheduled confirmation email', [
                'request_id' => $form->request_id,
                'error' => $emailError->getMessage(),
                'recipient' => $form->email
            ]);
        }
    }
    public function generateOfficialReceipt($requestId)
    {
        try {
            \Log::debug('=== GENERATE OFFICIAL RECEIPT CALLED ===', [
                'request_id' => $requestId,
                'full_url' => request()->fullUrl(),
                'method' => request()->method()
            ]);

            $form = RequisitionForm::with([
                'requestedFacilities.facility',
                'requestedEquipment.equipment',
                'purpose',
                'requisitionFees',
                'formStatus'
            ])->findOrFail($requestId);

            // Check if official receipt number exists
            if (empty($form->official_receipt_num)) {
                abort(404, 'Official receipt not generated yet');
            }

            // Calculate total fee
            $totalFee = $form->approved_fee;

            // Prepare receipt data
            $receiptData = [
                'official_receipt_num' => $form->official_receipt_num,
                'user_name' => $form->first_name . ' ' . $form->last_name,
                'user_email' => $form->email,
                'organization_name' => $form->organization_name,
                'contact_number' => $form->contact_number,
                'request_id' => $form->request_id,
                'facility_name' => $form->requestedFacilities->first()->facility->facility_name ?? 'N/A',
                'purpose' => $form->purpose->purpose_name,
                'num_participants' => $form->num_participants,
                'total_fee' => $totalFee,
                'issued_date' => $form->updated_at->format('F j, Y'),
                'schedule' => Carbon::parse($form->start_date)->format('F j, Y') . ' — ' .
                    Carbon::parse($form->start_time)->format('g:i A') . ' to ' .
                    Carbon::parse($form->end_time)->format('g:i A'),
                'start_schedule' => Carbon::parse($form->start_date)->format('F j, Y') . ' — ' .
                    Carbon::parse($form->start_time)->format('g:i A'),
                'end_schedule' => Carbon::parse($form->end_date)->format('F j, Y') . ' — ' .
                    Carbon::parse($form->end_time)->format('g:i A'),
                'fee_breakdown' => $this->getFeeBreakdown($form)
            ];

            return view('public.official-receipt', compact('receiptData'));

        } catch (\Exception $e) {
            \Log::error('Failed to generate official receipt', [
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);

            abort(404, 'Receipt not found');
        }
    }

    private function getFeeBreakdown($form)
    {
        $breakdown = [];

        // Add facility fees
        foreach ($form->requestedFacilities as $facility) {
            if (!$facility->is_waived) {
                $breakdown[] = [
                    'description' => $facility->facility->facility_name . ' Rental',
                    'amount' => $facility->facility->external_fee
                ];
            }
        }

        // Add equipment fees
        foreach ($form->requestedEquipment as $equipment) {
            if (!$equipment->is_waived) {
                $breakdown[] = [
                    'description' => $equipment->equipment->equipment_name . ' Rental' .
                        ($equipment->quantity > 1 ? ' (×' . $equipment->quantity . ')' : ''),
                    'amount' => $equipment->equipment->external_fee * $equipment->quantity
                ];
            }
        }

        // Add additional fees
        foreach ($form->requisitionFees as $fee) {
            if ($fee->fee_amount > 0) {
                $breakdown[] = [
                    'description' => $fee->label,
                    'amount' => $fee->fee_amount
                ];
            }
        }

        // Add late penalty if applicable
        if ($form->is_late && $form->late_penalty_fee > 0) {
            $breakdown[] = [
                'description' => 'Late Penalty Fee',
                'amount' => $form->late_penalty_fee
            ];
        }

        return $breakdown;
    }

private function updateEquipmentConditions($form)
{
    try {
        \Log::debug('Updating equipment conditions for scheduled form', [
            'request_id' => $form->request_id
        ]);

        // Get all requested equipment for this form
        $requestedEquipment = $form->requestedEquipment;

        foreach ($requestedEquipment as $reqEquipment) {
            // Get the equipment type
            $equipment = $reqEquipment->equipment;
            
            // Get available equipment items for this equipment type with conditions 1, 2, or 3
            $availableItems = \App\Models\EquipmentItem::where('equipment_id', $equipment->equipment_id)
                ->whereIn('condition_id', [1, 2, 3]) // New, Good, Fair
                ->orderBy('condition_id', 'asc') // Prefer better condition first
                ->limit($reqEquipment->quantity)
                ->get();

            \Log::debug('Found available equipment items', [
                'equipment_id' => $equipment->equipment_id,
                'required_quantity' => $reqEquipment->quantity,
                'available_count' => $availableItems->count(),
                'item_ids' => $availableItems->pluck('item_id')
            ]);

            if ($availableItems->count() < $reqEquipment->quantity) {
                \Log::warning('Not enough available equipment items', [
                    'equipment_id' => $equipment->equipment_id,
                    'required' => $reqEquipment->quantity,
                    'available' => $availableItems->count()
                ]);
                // Continue with available items
            }

            // Update each item to "In Use" (condition_id = 6)
            foreach ($availableItems as $item) {
                $item->condition_id = 6; // In Use
                $item->save();
                
                \Log::debug('Updated equipment item condition', [
                    'item_id' => $item->item_id,
                    'old_condition' => $item->getOriginal('condition_id'),
                    'new_condition' => $item->condition_id
                ]);
            }
        }

        return true;

    } catch (\Exception $e) {
        \Log::error('Failed to update equipment conditions', [
            'request_id' => $form->request_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

    public function updateCalendarInfo(Request $request, $requestId)
{
    try {
        \Log::debug('Updating calendar info', [
            'request_id' => $requestId,
            'admin_id' => auth()->id(),
            'input_data' => $request->all()
        ]);

        $validatedData = $request->validate([
            'calendar_title' => 'sometimes|string|max:50|nullable',
            'calendar_description' => 'sometimes|string|max:100|nullable',
        ]);

        $adminId = auth()->id();
        if (!$adminId) {
            return response()->json(['error' => 'Admin not authenticated'], 401);
        }

        $form = RequisitionForm::findOrFail($requestId);

        // Update only the provided fields
        if (array_key_exists('calendar_title', $validatedData)) {
            $form->calendar_title = $validatedData['calendar_title'];
        }

        if (array_key_exists('calendar_description', $validatedData)) {
            $form->calendar_description = $validatedData['calendar_description'];
        }

        $form->save();

        \Log::info('Calendar info updated successfully', [
            'request_id' => $requestId,
            'calendar_title' => $form->calendar_title,
            'calendar_description' => $form->calendar_description,
            'admin_id' => $adminId
        ]);

        return response()->json([
            'message' => 'Calendar information updated successfully',
            'calendar_title' => $form->calendar_title,
            'calendar_description' => $form->calendar_description
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Calendar info update validation failed', [
            'request_id' => $requestId,
            'errors' => $e->errors()
        ]);

        return response()->json([
            'error' => 'Validation failed',
            'details' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Failed to update calendar info', [
            'request_id' => $requestId,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Failed to update calendar information',
            'details' => $e->getMessage()
        ], 500);
    }
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
                            'requested_facility_id' => $facility->requested_facility_id,
                            'name' => $facility->facility->facility_name,
                            'fee' => $facility->facility->external_fee,
                            'rate_type' => $facility->facility->rate_type,
                            'is_waived' => $facility->is_waived
                        ];
                    }),
                        'equipment' => $form->requestedEquipment->map(function ($equipment) {
                        return [
                            'requested_equipment_id' => $equipment->requested_equipment_id,
                            'name' => $equipment->equipment->equipment_name,
                            'quantity' => $equipment->quantity,
                            'fee' => $equipment->equipment->external_fee,
                            'rate_type' => $equipment->equipment->rate_type,
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

    public function autoMarkLateForms()
    {
        try {
            \Log::info('Starting automatic late form detection');

            // Get forms that are in Ongoing status and not already marked as late
            $ongoingStatus = FormStatus::where('status_name', 'Ongoing')->first();
            $lateStatus = FormStatus::where('status_name', 'Late')->first();

            if (!$ongoingStatus || !$lateStatus) {
                \Log::error('Required statuses not found');
                return response()->json([
                    'error' => 'Required statuses not found',
                    'processed' => 0,
                    'marked_late' => 0
                ], 500);
            }

            $formsToMarkLate = RequisitionForm::where('status_id', $ongoingStatus->status_id)
                ->where('is_late', false)
                ->get();

            $markedLateCount = 0;

            foreach ($formsToMarkLate as $form) {
                try {
                    // Calculate end datetime with 4 hours grace period
                    $endDateTime = Carbon::parse($form->end_date . ' ' . $form->end_time);
                    $gracePeriodEnd = $endDateTime->copy()->addHours(4);

                    // Check if grace period has passed
                    if (now()->greaterThan($gracePeriodEnd)) {
                        \Log::info('Marking form as late automatically', [
                            'request_id' => $form->request_id,
                            'end_datetime' => $endDateTime,
                            'grace_period_end' => $gracePeriodEnd,
                            'current_time' => now()
                        ]);

                        // Update form to late status
                        $form->status_id = $lateStatus->status_id;
                        $form->is_late = true;
                        $form->save();

                        $markedLateCount++;

                        // Log the automatic action
                        \Log::info('Form automatically marked as late', [
                            'request_id' => $form->request_id,
                            'requester' => $form->first_name . ' ' . $form->last_name,
                            'original_end' => $endDateTime,
                            'marked_late_at' => now()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing form for late marking', [
                        'request_id' => $form->request_id,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            \Log::info('Automatic late form detection completed', [
                'processed' => $formsToMarkLate->count(),
                'marked_late' => $markedLateCount
            ]);

            return response()->json([
                'message' => 'Automatic late detection completed',
                'processed' => $formsToMarkLate->count(),
                'marked_late' => $markedLateCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to automatically mark late forms', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to automatically mark late forms',
                'details' => $e->getMessage(),
                'processed' => 0,
                'marked_late' => 0
            ], 500);
        }
    }


}