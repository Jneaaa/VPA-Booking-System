<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminApproval extends Controller
{
    /* Controller Documentation:
        * This controller handles the admin approval process for requisition forms.
        * It includes methods for viewing pending approvals, approving or rejecting requests,
        * and managing the status of requisition forms.
        *
        * Methods:
        * - index: Display a list of pending requisition forms for admin approval.
        * - approve: Approve a requisition form and update its status.
        * - reject: Reject a requisition form and update its status.
        */

    // make a function to view pending requisition forms
    public function index()
    {

        // Logic to retrieve and display pending requisition forms
        // This function queries the RequisitionForm model for forms with status 'Pending Approval' (1). 
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

        // A form's status_id will be set to 'In Review' (2) if at least one admin has viewed the form and is currently reviewing it.
        // A form's status_id will be set to 'Awaiting Payment' (3) if the required number of approvals has been reached, allowing an admin to manually set the 'is_finalized' field in the requisition_forms table to true, with the 'finalized_by' field set to the admin's ID.
        

    }




}
