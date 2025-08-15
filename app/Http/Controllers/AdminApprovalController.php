<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionType;
use App\Models\Admin;
use App\Model\AdminDepartment;
use App\Models\SystemLog;
use App\Models\RequisitionForm;

class AdminApprovalController extends Controller
{
    /* Controller Documentation:
        * This controller handles the admin approval process for requisition forms.
        * It includes methods for viewing pending approvals, approving or rejecting requests,
        * and managing the status of requisition forms.
        *
        * Each action is logged in the system_logs (log_id) table.
        
        * Methods:

        - index(): Get all pending requests from the requisition_forms table (PK: request_id) for admin approval
            - only show forms with a status_id (from FormStatus model) of 1 (Pending approval) and 2 (Awaiting payment).
            - Admins can only view forms that are under their departments (e.g., a form has a requested facility or equipment that belongs to the admin's department) with the use of the AdminDepartment eloquent model relationship.

        - approveRequest(): an admin approves a requisition form
            - If an admin approves a form, add to approval count in the requisition_approvals table, getting the admin_id and the request_id and committing it to this table:

                approved_by = admin_id
                request_id = request_id

        - rejectRequest(): an admin rejects a requisition form 
            - If an admin rejects a form, add to rejection count in the requisition_approvals table, getting the admin_id and the request_id and committing it to this table:

                rejected_by = admin_id
                request_id = request_id

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