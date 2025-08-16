<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequisitionApproval;
use App\Models\FormStatus;
use App\Models\ActionType;
use App\Models\Admin;
use App\Model\AdminDepartment;
use App\Models\SystemLog;
use App\Models\RequisitionForm;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    /* Controller Documentation:

        * This controller handles the admin approval process for requisition forms.
        * It includes methods for viewing pending approvals, approving or rejecting requests,
        * and managing the status of requisition forms.
        *
        * Each action is logged in the system_logs (log_id) table (future implementation).

        
        * Methods:

        - index(): Get all records from the requisition_forms table (PK: request_id) for admin approval
            - Excluded status_name: Returned, Late Return, Completed, Rejected, and Cancelled. Pluck the status_id based on these status_names. Eloquent model relationship: FormStatus.
            - Add the total number of approvals and rejections made by admins for a requisition form in the json response.
        
        - completedTransactions(): Get all records from the requisition_forms table (PK: request_id) with status_name: Completed, Rejected, and Cancelled. Pluck the status_id based on these status_names. Eloquent model relationship: FormStatus.

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
        */

    // make a function to view pending requisition forms
    public function viewForms()
    {



        // Logic to retrieve and display pending requisition forms
        // This function queries the RequisitionForm model for forms with status 'Pending Approval' (1) and 'Awaiting Payment' (3). 
         // admins can only view forms that are under their departments (e.g., a form has a requested facility or requipment that belongs to the admin's department)

        /* 

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

    }

    // make a function to approve a requisition form
    public function approve($requestId)
    {
        // Logic to approve a requisition form
        // This could involve updating the status of the requisition form to 'Approved'
        // and possibly notifying the user about the approval
    }

    // make a function to reject a requisition form
    public function reject($requestId)
    {
        // Logic to reject a requisition form
        // This could involve updating the status of the requisition form to 'Rejected'
        // and possibly notifying the user about the rejection
    }

    // functions that shows completed requisition forms with the status 'Completed' (8) and 'Cancelled' (10).
    public function completed()
    {
        // Logic to retrieve and display completed requisition forms
        // This function queries the RequisitionForm model for forms with status 'Completed' (8) or 'Cancelled' (10).
    }

    // function that lets the admins waive fees for requisition forms by updating the 'is_waived' field in either requested_facilities or requested_equipment table to true.
    public function waiveFees($requestId)
    {
        // Logic to waive an indivual facility or equipment by updating the 'is_waived' field in the requested_facilities or requested_equipment tables.
        // could include notifying the user about the fee waiver (e.g., showing a message that the fees have been waived).
        // Ensure that the requestId corresponds to a valid requisition form
        // and that the admin has the necessary permissions to waive fees

        /* 
        All admin roles that are allowed to approve/reject forms, waive fees, and tweak fees:
        '1', 'Head Admin', 'Complete system access and administration.'
        '2', 'Vice President of Administration', 'View and approve requisition forms only.'
        '3', 'Facilities Coordinator', 'Review, approve forms and manage fees. Can also manage equipment & facilities.'
        */

    }

    // function that waives ALL fees for a requisition form by updating the 'is_waived' field in the requisition_forms table to true.
    public function waiveAllFees($requestId)
    {
        // Logic to waive all fees for a requisition form
        // This could involve updating the 'is_waived' field in the requisition_forms table to true
        // and possibly notifying the user about the fee waiver.
        // Ensure that the requestId corresponds to a valid requisition form
        // and that the admin has the necessary permissions to waive all fees.
    }

    // function that tracks the total number of approvals and rejections made by admins for a requisition form.

    public function trackApprovals($requestId)
    {
        // Logic to track the total number of approvals and rejections made by admins for a requisition form. A total of 3 approvals are required for a requisition form to be approved.
        // This could involve querying the requisition_approvals table for the given requestId
        // and counting the number of approvals and rejections.
        // Ensure that the requestId corresponds to a valid requisition form
        // and that the admin has the necessary permissions to view this information.

        /* 

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

        // A form's status_id will remain as 'Pending Approval' (1) until the required number of approvals has been reached. 
        // A form's status_id will be set to 'Awaiting Payment' (3) if the required number of approvals has been reached, allowing an admin to manually set the 'is_finalized' field in the requisition_forms table to true, with the 'finalized_by' field set to the admin's ID.


    }

    public function index()
    {
        // Get status IDs to exclude
        $excludedStatuses = FormStatus::whereIn('status_name', [
            'Returned', 'Late Return', 'Completed', 'Rejected', 'Cancelled'
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
                        'latest_action' => $form->requisitionApprovals()->latest('date_approved')->first()
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
            'date_approved' => now()
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
            'date_approved' => now()
        ]);

        $rejection->save();

        return response()->json(['message' => 'Request rejected successfully']);
    }

    public function getSimplifiedForms()
    {
        // Get status IDs to exclude (same as index)
        $excludedStatuses = FormStatus::whereIn('status_name', [
            'Returned', 'Late Return', 'Completed', 'Rejected', 'Cancelled'
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




}


/* 

How to Access Cloudinary Files Using the Token:

Database Structure:

    Store the token along with the Cloudinary public ID and URL in your requisition form record

    The key fields you need are:
'formal_letter_url' => 'https://res.cloudinary.com/.../formal_letter.pdf',
'formal_letter_public_id' => 'user-uploads/user-letters/xyz123',
'upload_token' => '40charrandomstring'

Admin View Implementation:
// In your admin controller
public function showRequisition($token) 
{
    $requisition = RequisitionForm::where('upload_token', $token)->firstOrFail();
    
    return view('admin.requisition.view', [
        'formal_letter_url' => $requisition->formal_letter_url,
        'facility_layout_url' => $requisition->facility_layout_url
        // ... other data
    ]);
}

Displaying Files in Admin View:
<!-- Blade template -->
@if($formal_letter_url)
    @if(Str::endsWith($formal_letter_url, ['.jpg', '.jpeg', '.png', '.gif']))
        <img src="{{ $formal_letter_url }}" class="img-fluid">
    @elseif(Str::endsWith($formal_letter_url, '.pdf'))
        <iframe src="{{ $formal_letter_url }}" width="100%" height="600px"></iframe>
    @else
        <a href="{{ $formal_letter_url }}" target="_blank" class="btn btn-primary">
            Download Formal Letter
        </a>
    @endif
@endif

Important Security Considerations:

Access Control:

    Always verify admin permissions before showing files

    Example middleware:
    Route::get('/admin/requisitions/{token}', [RequisitionController::class, 'show'])
    ->middleware('can:view-requisition');

    Cloudinary Security:
Use signed URLs if containing sensitive data:
$secureUrl = cloudinary()->getSignedUrl($requisition->formal_letter_public_id, [
    'expires_at' => now()->addHours(2)
]);
Set appropriate access controls in Cloudinary dashboard

File Type Handling:

    For non-image PDFs, use Cloudinary's PDF viewer:
    <iframe src="https://res.cloudinary.com/demo/image/upload/{{ $public_id }}.pdf" 
        width="100%" height="600px"></iframe>

                width="100%" height="600px"></iframe>

Alternative Approach Using API:

If you need more control, create an API endpoint:
// routes/api.php
Route::middleware('auth:sanctum')->get('/requisition-files/{token}', function ($token) {
    $requisition = RequisitionForm::where('upload_token', $token)->firstOrFail();
    
    return response()->json([
        'formal_letter' => [
            'url' => $requisition->formal_letter_url,
            'type' => pathinfo($requisition->formal_letter_url, PATHINFO_EXTENSION)
        ],
        // ... other files
    ]);
});

Key Points:

    The token is just a database reference - the actual file access uses the stored Cloudinary URL

    No need for Cloudinary API calls just to view files (unless you need transformations)

    For sensitive documents, consider:

        Temporary signed URLs

        Download counters

        Access logging

The token system works perfectly for admin review purposes while maintaining security through:

    Database-level association

    Laravel's auth system

    Cloudinary's existing URL security

*/