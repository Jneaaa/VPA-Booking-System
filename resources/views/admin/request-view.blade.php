@extends('layouts.admin')
@section('title', 'Manage Requisitions: View Request')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .btn-ghost {
    background-color: transparent;
    border: none !important;
    color: inherit !important;          /* keeps text color same as parent */
    padding: 0.375rem 0.75rem; /* same as default btn padding */
    transition: background-color 0.2s;
    cursor: pointer;
}

.btn-ghost:hover {
    background-color: #e0e0e0 !important; /* light gray hover */
    border: none !important;
}

    :root {
        --primary-color: #003366;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #0d6efd;
        --light-bg: #f8f9fa;
        --card-radius: 8px;
        --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .form-switch .form-check-input {
        width: 3rem;
    }

    .form-switch .form-check-input:checked {
        background-color: #eeaf01ff;
        /* Bootstrap primary color */
        border-color: #eeaf01ff;
    }

    .fc-header-toolbar {
        background-color: #ffffff;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 0;
    }

    .card {
        border-radius: 0 !important;
        border: 1px solid lightgray !important;
        background: white !important;
        margin-bottom: 1rem;
    }

    .card-title {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .card-divider {
        border: 1px solid rgba(0, 0, 0, 0.1);
        margin: 1rem 0;
    }

    .card-header {
        min-height: 56px;
        /* adjust as needed */
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    /* Improved Skeleton Loading */
    .skeleton {
        background: #eee;
        background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
        background-size: 200% 100%;
        animation: 1.5s shine linear infinite;
        border-radius: 0.375rem;
        overflow: hidden;
        position: relative;
    }

    @keyframes shine {
        to {
            background-position-x: -200%;
        }
    }

    .skeleton-circle {
        border-radius: 50%;
    }

    .skeleton-text {
        height: 1em;
        border-radius: 4px;
        margin-bottom: 0.5rem;
    }

    .skeleton-card {
        height: 100%;
        min-height: 150px;
        border-radius: 0.375rem;
    }

    .skeleton-card-title {
        width: 70%;
        height: 1.5rem;
        margin-bottom: 1rem;
    }

    .skeleton-card-text {
        width: 90%;
        height: 1rem;
        margin-bottom: 0.5rem;
    }

    .skeleton-card-text:last-child {
        width: 80%;
    }

    .skeleton-img {
        width: 100%;
        height: 100%;
        min-height: 120px;
        border-radius: 0.375rem 0.375rem 0 0;
    }

    .item-row {
        padding: 0.5rem;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .item-row:hover {
        background-color: rgba(0, 51, 102, 0.05);
    }

    .item-row.waived,
    .waived .item-name,
    .waived .item-price {
        opacity: 0.7;
        text-decoration: line-through;
        color: #999;
    }

    .admin-container {
        padding: 1rem;
        max-width: 81%;
        margin-left: 16rem !important;
        margin-top: 4rem !important;
    }

    .admin-container .card {
        border: none;
        border-color: none;
    }

    /* Calendar container adjustments */
    .calendar-container {
        height: 500px;
        min-height: 350px;
        overflow: hidden;
    }

    #calendar {
        height: 100% !important;
        font-size: 0.9rem;
    }

    /* Compact spacing */
    .row.g-3 {
        --bs-gutter-y: 0.75rem;
        --bs-gutter-x: 0.75rem;
    }

    /* Breadcrumb adjustments */
    .breadcrumb {
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }

    /* Form elements */
    .form-control,
    .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    /* Text sizes */
    p {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    /* Modal adjustments */
    .modal-body {
        padding: 1rem;
        font-size: 0.9rem;
    }

    @media (max-width: 991.98px) {
        .admin-container {
            padding: 0.75rem;
            max-width: 100%;
        }

        .calendar-container {
            height: 400px;
        }
    }

    @media (max-width: 767.98px) {
        .admin-container {
            padding: 0.5rem;
        }
    }

    /* Custom styling for the fee items */
    .fee-item {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    /* Make the remark textarea resize automatically */
    .card-footer textarea {
        resize: none;
        overflow-y: hidden;
        transition: height 0.2s ease-in-out;
        min-height: 56px;
    }

    /* Style for the icon-only remove button */
    .btn-icon {
        padding: 0.25rem 0.5rem;
        line-height: 1;
    }

    .btn-light-danger {
        background-color: #fbe9e9;
        color: #dc3545;
        border: none;
    }

    .btn-light-danger:hover {
        background-color: #f8d7da;
        color: #dc3545;
    }

    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        vertical-align: text-bottom;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<div class="container-fluid admin-container">
    <div class="card bg-transparent shadow-none pt-0" style="border: none !important;">
        <div class="card-body p-4">
            <a href="{{ url('/admin/manage-requests') }}" class="btn btn-primary mb-4">
                ← Back to Requisitions
            </a>
            <!-- Skeleton Loading -->
            <div id="loadingState">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="skeleton skeleton-text" style="width: 200px; height: 30px;"></div>
                    <div class="skeleton skeleton-text" style="width: 100px; height: 30px;"></div>
                </div>
                <div class="row g-3">
                    <!-- User Information Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <hr>
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 95%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Request Details Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <hr>
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 95%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Calendar Skeleton -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <div class="skeleton" style="height: 450px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Items Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Fees Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Actual Content (Initially Hidden) -->
            <div id="contentState" style="display: none;">
                <!-- Form Status (full width) -->
                <div class="row g-2">
                    <div class="col-12">
                        <div class="card">
                            <!-- Move Title + Status Badge into card-header -->
                            <div
                                class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                    Requisition ID #<span id="requestIdTitle"></span>:
                                    <!-- Replace "Form Status" with dropdown + button -->
                                    <select class="form-select form-select-sm" id="statusDropdown"
                                        style="max-width: 150px;">
                                        <option value="">Change Status...</option>
                                        <option value="Scheduled">Scheduled</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Returned">Returned</option>
                                        <option value="Late Return">Late Return</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary" id="updateStatusBtn" disabled>
                                        Update
                                    </button>
                                </h5>
                                <span class="badge" id="statusBadge"></span>
                            </div>
                            <!-- Card body -->
                            <div class="card-body">
                                <div id="formStatus"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Status Update Confirmation Modal -->
                <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Status Change</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="statusModalContent">
                                    <!-- Content will be dynamically populated -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Confirm
                                    Change</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row g-3 align-items-stretch">
                    <!-- Left column: Requester + Reservation -->
                    <div class="col-md-4 d-flex flex-column gap-2">
                        <!-- Requester Details -->
                        <div class="card mb-2">
                            <div
                                class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Requester Details</h5>
                            </div>
                            <div class="card-body">
                                <div id="userDetails"></div>
                            </div>
                        </div>

                        <!-- Reservation Details -->
                        <div class="card flex-grow-1">
                            <div
                                class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Reservation Details</h5>
                            </div>
                            <div class="card-body">
                                <div id="formDetails"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column: Calendar -->
                    <div class="col-md-8 d-flex">
                        <div class="card flex-fill d-flex flex-column">
                            <div
                                class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Schedule Visualization</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="calendar-container flex-fill">
                                    <div id="calendar" style="height:100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requested Items + Form Actions -->
                <div class="row g-3">

                    <!-- Requested Items -->
                    <div class="col-md-6">
                        <div class="card h-100 d-flex flex-column">
                            <!-- Card Header -->
                            <div
                                class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Fee Waivers</h5>

                                <!-- Toggle Switch -->
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="waiveAllSwitch">
                                    <label class="form-check-label" for="waiveAllSwitch">
                                        Waive All Fees
                                    </label>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body">
                                <div id="requestedItems"></div>
                            </div>

                            <!-- Card Footer (Tentative Fee) -->
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Tentative Fee:</strong>
                                    <span id="tentativeFee"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->

                    <div class="col-md-6">
                        <div class="card h-100 d-flex flex-column">
                            <div
                                class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Additional Fees & Discounts</h5>
                                <button id="addFeeBtn" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add Fee
                                </button>
                            </div>
                            <div class="card-body" style="flex: 1 1 auto; overflow-y: auto;">
                                <p id="feesPlaceholder" class="text-muted fst-italic text-center">
                                    No additional fees have been added yet.
                                </p>
                                <div id="additionalFees">
                                </div>
                            </div>
                            <div class="card-footer bg-white p-2">
                                <div class="input-group">
                                    <textarea class="form-control" rows="1" placeholder="Leave a remark..."
                                        aria-label="Leave a remark"></textarea>
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="bi bi-send-fill"
                                            style="border: none !important; border-color: none !important;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Modal -->
                    <div class="modal fade" id="feeModal" tabindex="-1" aria-labelledby="feeModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="feeModalLabel">Add Fee or Discount</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="feeForm">
                                        <input type="hidden" id="feeRequestId" value="{{ $requestId }}">
                                        <div class="mb-2">
                                            <label for="feeType" class="form-label">Fee Type</label>
                                            <select id="feeType" class="form-select" required>
                                                <option value="">Select type...</option>
                                                <option value="additional">Additional Fee</option>
                                                <option value="discount">Discount</option>
                                            </select>
                                        </div>
                                        <div class="mb-2" id="discountTypeSection" style="display: none;">
                                            <label for="discountType" class="form-label">Discount Type</label>
                                            <select id="discountType" class="form-select">
                                                <option value="Fixed">Fixed Amount</option>
                                                <option value="Percentage">Percentage</option>
                                            </select>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label for="feeLabel" class="form-label">Fee Label</label>
                                                <input type="text" id="feeLabel" class="form-control"
                                                    placeholder="Fee Label" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="feeValue" class="form-label">Amount</label>
                                                <input type="number" id="feeValue" class="form-control" step="0.01"
                                                    min="0.01" placeholder="Enter amount" required>
                                            </div>
                                        </div>


                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" id="saveFeeBtn" class="btn btn-primary">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <div class="card">

                                <!-- Fee Breakdown Header -->
                                <div
                                    class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Fee Breakdown</h5>
                                </div>

                                <div class="card-body">
                                    <!-- Base Fees -->
                                    <div class="mb-3">
                                        <h6 class="fw-bold mb-2">Base Fees</h6>
                                        <div id="baseFeesContainer">
                                            <!-- Facilities -->
                                            <div id="facilitiesFees">
                                                <!-- Populated by JS -->
                                            </div>

                                            <!-- Equipment -->
                                            <div id="equipmentFees">
                                                <!-- Populated by JS -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Fees -->
                                    <div class="mb-3">
                                        <h6 class="fw-bold mb-2">Additional Fees & Discounts</h6>
                                        <div id="additionalFeesContainer">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div
                                        class="d-flex justify-content-between align-items-center fw-bold mt-3 border-top pt-2">
                                        <span>Total Approved Fee:</span>
                                        <span id="totalApprovedFee">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center">
                                <a href="{{ url('/admin/manage-requests') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Requests
                                </a>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-success" id="approveBtn">
                                        <i class="bi bi-check-circle me-1"></i> Approve Request
                                    </button>
                                    <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item text-danger" id="rejectBtn">
                                                <i class="bi bi-x-octagon me-2"></i> Reject Request
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-primary" id="finalizeBtn">
                                                <i class="bi bi-check2-all me-2"></i> Finalize Request
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal for Approve Action -->
            <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Approval</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to approve this request? This action cannot be undone.</p>
                            <p class="text-muted small">You will not be able to take further actions on this form after
                                approval.</p>
                            <div class="mb-3">
                                <label for="approveRemarks" class="form-label">Remarks (Optional)</label>
                                <textarea class="form-control" id="approveRemarks" rows="3"
                                    placeholder="Add any remarks here..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="confirmApprove">Confirm Approval</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal for Reject Action -->
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Rejection</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to reject this request? This action cannot be undone.</p>
                            <p class="text-muted small">You will not be able to take further actions on this form after
                                rejection.</p>
                            <div class="mb-3">
                                <label for="rejectRemarks" class="form-label">Remarks (Optional)</label>
                                <textarea class="form-control" id="rejectRemarks" rows="3"
                                    placeholder="Add any remarks here..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmReject">Confirm Rejection</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal for Finalize Action -->
            <div class="modal fade" id="finalizeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Finalize Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Current Status:</strong>
                                <span id="currentApprovalCount" class="fw-bold"></span> approvals,
                                <span id="currentRejectionCount" class="fw-bold"></span> rejections
                            </div>
                            <p>Finalizing this request will change its status from "Pending Approval" to "Awaiting
                                Payment".</p>
                            <p class="text-muted small">This action cannot be undone and will prevent further
                                approvals/rejections.</p>

                            <div class="mb-3">
                                <label for="calendarTitle" class="form-label">Event Title *</label>
                                <input type="text" class="form-control" id="calendarTitle"
                                    placeholder="Enter calendar event title" required maxlength="50">
                            </div>

                            <div class="mb-3">
                                <label for="calendarDescription" class="form-label">Event Description *</label>
                                <textarea class="form-control" id="calendarDescription" rows="3"
                                    placeholder="Enter calendar event description" required maxlength="100"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="finalizeRemarks" class="form-label">Remarks (Optional)</label>
                                <textarea class="form-control" id="finalizeRemarks" rows="3"
                                    placeholder="Add any remarks here..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmFinalize">Finalize Request</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Event Details Modal -->
            <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="eventModalBody">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Requester:</strong> <span id="modalRequester"></span></p>
                                    <p><strong>Purpose:</strong> <span id="modalPurpose"></span></p>
                                    <p><strong>Participants:</strong> <span id="modalParticipants"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                                    <p><strong>Tentative Fee:</strong> <span id="modalFee"></span></p>
                                    <p><strong>Approvals:</strong> <span id="modalApprovals"></span></p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6>Requested Items:</h6>
                                <div id="modalItems"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="modalViewDetails">View Full
                                Details</button>
                        </div>
                    </div>
                </div>
            </div>





            @section('scripts')
                            <script src="{{ asset('js/admin/calendar.js') }}"></script>
                            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {

                                    const requestId = window.location.pathname.split('/').pop();
                                    const adminToken = localStorage.getItem('adminToken');
                                    let allRequests = [];

                                    // Initialize Bootstrap modal
                                    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                                    const feeModal = new bootstrap.Modal(document.getElementById('feeModal'));

                                    // Form Action Modals
                                    const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
                                    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                                    const finalizeModal = new bootstrap.Modal(document.getElementById('finalizeModal'));

                                    // Fee management elements
                                    const feesContainer = document.getElementById("additionalFees");
                                    const placeholder = document.getElementById("feesPlaceholder");
                                    const addFeeBtn = document.getElementById("addFeeBtn");
                                    const saveFeeBtn = document.getElementById("saveFeeBtn");
                                    const feeTypeSelect = document.getElementById("feeType");
                                    const feeValueInput = document.getElementById("feeValue");
                                    const discountTypeSection = document.getElementById("discountTypeSection");

                                    // Status update functionality
                                    const statusDropdown = document.getElementById('statusDropdown');
                                    const updateStatusBtn = document.getElementById('updateStatusBtn');
                                    const statusUpdateModal = new bootstrap.Modal(document.
                                        getElementById('statusUpdateModal'));
                                    let selectedStatus = '';




                                    // Enable/disable update button based on selection
                                    statusDropdown.addEventListener('change', function () {
                                        updateStatusBtn.disabled = !this.value;
                                        selectedStatus = this.value;
                                        console.log('Dropdown changed to:', this.value, 'Button disabled:', updateStatusBtn.disabled);
                                    });

                                    // Update status button click handler
                                    updateStatusBtn.addEventListener('click', function () {
                                        const modalContent = document.getElementById('statusModalContent');
                                        // Grab the latest status from the DOM or a stored variable
                                        const statusBadge = document.getElementById('statusBadge');
                                        const currentStatusName = statusBadge.textContent.trim(); // fallback if `request` isn't defined


                                        if (currentStatusName === 'Pending Approval') {
                                            alert('Finalize the form first'); // temporary replacement for toast
                                            return; // stop further execution
                                        }



                                        // Set modal content based on selected status
                                        switch (selectedStatus) {
                                            case 'Scheduled':
                                                modalContent.innerHTML = `
                                    <p>This will set the form's status to <strong>Scheduled</strong>.</p>
                                    <p class="text-muted small">Note: The request can still be cancelled if an emergency happens. 
                                    If such a situation occurs, contact the requester about refund details and settle it in the business office on campus.</p>
                                `;
                                                break;
                                            case 'Ongoing':
                                                modalContent.innerHTML = `
                                    <p>This will set the form's status to <strong>Ongoing</strong>.</p>
                                    <p class="text-muted small">Note: The form cannot be cancelled now.</p>
                                `;
                                                break;
                                            case 'Returned':
                                            case 'Late Return':
                                            case 'Completed':
                                                modalContent.innerHTML = `
                                    <p>This will set the form's status to <strong>${selectedStatus}</strong>.</p>
                                    <p class="text-muted small">Note: This form will be marked as a <strong>Completed Transaction</strong>.</p>
                                `;
                                                break;
                                        }

                                        statusUpdateModal.show();
                                    });

                                    // Confirm status update
                                    document.getElementById('confirmStatusUpdate').addEventListener('click', async function () {
                                        const adminToken = localStorage.getItem('adminToken');

                                        try {
                                            const response = await fetch(`/api/admin/requisition/${requestId}/update-status`, {
                                                method: 'POST',
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    status_name: selectedStatus
                                                })
                                            });

                                            const contentType = response.headers.get('content-type');
                                            let responseData;

                                            if (contentType && contentType.includes('application/json')) {
                                                responseData = await response.json();
                                            } else {
                                                const textResponse = await response.text();
                                                throw new Error(textResponse || 'Non-JSON response from server');
                                            }

                                            if (!response.ok) {
                                                const errorMessage = responseData.error ||
                                                    responseData.message ||
                                                    JSON.stringify(responseData) ||
                                                    'Failed to update status';
                                                throw new Error(errorMessage);
                                            }

                                            alert('Status updated successfully!');
                                            statusUpdateModal.hide();
                                            statusDropdown.value = '';
                                            updateStatusBtn.disabled = true;

                                            // Refresh the page to show updated status
                                            fetchRequestDetails();

                                        } catch (error) {
                                            console.error('Error updating status:', error);
                                            alert('Error: ' + error.message);
                                        }
                                    });

                                    // Handle individual waiver checkbox changes
                                    async function handleWaiverChange(checkbox) {
                                        const type = checkbox.dataset.type;
                                        const id = checkbox.dataset.id;
                                        const isWaived = checkbox.checked;

                                        // Update UI immediately for better UX
                                        const itemRow = checkbox.closest('.item-row');
                                        if (itemRow) {
                                            if (isWaived) {
                                                itemRow.classList.add('waived');
                                            } else {
                                                itemRow.classList.remove('waived');
                                            }
                                        }

                                        // Collect all waived items and convert IDs to numbers
                                        const waivedFacilities = [];
                                        const waivedEquipment = [];

                                        document.querySelectorAll('.waiver-checkbox:checked').forEach(cb => {
                                            if (cb.dataset.type === 'facility') {
                                                waivedFacilities.push(parseInt(cb.dataset.id)); // Convert to number
                                            } else if (cb.dataset.type === 'equipment') {
                                                waivedEquipment.push(parseInt(cb.dataset.id)); // Convert to number
                                            }
                                        });

                                        try {
                                            const response = await fetch(`/api/admin/requisition/${requestId}/waive`, {
                                                method: 'POST',
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    waived_facilities: waivedFacilities,
                                                    waived_equipment: waivedEquipment
                                                })
                                            });

                                            if (!response.ok) {
                                                const errorData = await response.json();
                                                // Improved error message handling
                                                let errorMessage = 'Failed to update waiver status';
                                                if (errorData.details) {
                                                    if (typeof errorData.details === 'object') {
                                                        errorMessage = Object.values(errorData.details).flat().join(', ');
                                                    } else {
                                                        errorMessage = errorData.details;
                                                    }
                                                } else if (errorData.error) {
                                                    errorMessage = errorData.error;
                                                }
                                                throw new Error(errorMessage);
                                            }

                                            const result = await response.json();

                                            // Update fees display
                                            document.getElementById('tentativeFee').textContent = `₱${parseFloat(result.tentative_fee).toFixed(2)}`;
                                            document.getElementById('totalApprovedFee').textContent = `₱${parseFloat(result.updated_approved_fee).toFixed(2)}`;

                                            // Refresh the request details to get updated item waiver status
                                            fetchRequestDetails();
                                            console.log('Sending waiver request:', {
                                                waived_facilities: waivedFacilities,
                                                waived_equipment: waivedEquipment
                                            });

                                        } catch (error) {
                                            console.error('Error updating waiver:', error);
                                            // Revert checkbox state on error
                                            checkbox.checked = !isWaived;
                                            if (itemRow) {
                                                itemRow.classList.toggle('waived');
                                            }
                                            alert('Failed to update waiver: ' + error.message);
                                        }
                                    }

                                    // Handle "Waive All" toggle switch
                                    async function handleWaiveAll(switchElement) {
                                        const waiveAll = switchElement.checked;

                                        try {
                                            const response = await fetch(`/api/admin/requisition/${requestId}/waive`, {
                                                method: 'POST',
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    waive_all: waiveAll
                                                })
                                            });

                                            if (!response.ok) {
                                                const errorData = await response.json();
                                                throw new Error(errorData.details || 'Failed to update waiver status');
                                            }

                                            const result = await response.json();

                                            // Update all checkboxes to match the "waive all" state
                                            document.querySelectorAll('.waiver-checkbox').forEach(checkbox => {
                                                checkbox.checked = waiveAll;
                                                const itemRow = checkbox.closest('.item-row');
                                                if (itemRow) {
                                                    if (waiveAll) {
                                                        itemRow.classList.add('waived');
                                                    } else {
                                                        itemRow.classList.remove('waived');
                                                    }
                                                }
                                            });

                                            // Update fees display
                                            document.getElementById('tentativeFee').textContent = `₱${parseFloat(result.tentative_fee).toFixed(2)}`;
                                            document.getElementById('totalApprovedFee').textContent = `₱${parseFloat(result.updated_approved_fee).toFixed(2)}`;

                                            // Refresh the request details
                                            fetchRequestDetails();

                                        } catch (error) {
                                            console.error('Error updating waive all:', error);
                                            // Revert switch state on error
                                            switchElement.checked = !waiveAll;
                                            alert('Failed to update waive all: ' + error.message);
                                        }
                                    }

                                    // A map to get user-friendly names for fee types
                                    const feeTypeNames = {
                                        additional: 'Additional fee',
                                        discount: 'Discount',
                                    };



                                    // Function to toggle the placeholder text
                                    function togglePlaceholder() {
                                        placeholder.style.display = feesContainer.children.length === 0 ? "block" : "none";
                                    }
                                    togglePlaceholder(); // Initial check

                                    // Show the modal when "Add Fee" is clicked
                                    addFeeBtn.addEventListener("click", function () {
                                        feeModal.show();
                                    });


                                    // Handle fee type change to show/hide discount type
                                    feeTypeSelect.addEventListener('change', function () {
                                        discountTypeSection.style.display = this.value === 'discount' ? 'block' : 'none';
                                    });


                                    // Save Fee button logic
                                    saveFeeBtn.addEventListener("click", async function () {
                                        const type = feeTypeSelect.value;
                                        const value = parseFloat(feeValueInput.value);
                                        const label = document.getElementById('feeLabel').value;
                                        const discountType = document.getElementById('discountType').value;

                                        if (!type || !value || !label) {
                                            alert("Please fill all required fields.");
                                            return;
                                        }

                                        try {
                                            let endpoint = '';
                                            let requestData = {};

                                            // Determine which API endpoint to call based on fee type
                                            switch (type) {
                                                case 'additional':
                                                    endpoint = `/api/admin/requisition/${requestId}/fee`;
                                                    requestData = {
                                                        label: label,
                                                        fee_amount: value
                                                    };
                                                    break;
                                                case 'discount':
                                                    endpoint = `/api/admin/requisition/${requestId}/discount`;
                                                    requestData = {
                                                        label: label,
                                                        discount_amount: value,
                                                        discount_type: discountType
                                                    };
                                                    break;
                                            }

                                            const response = await fetch(endpoint, {
                                                method: 'POST',
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify(requestData)
                                            });

                                            if (!response.ok) {
                                                const errorData = await response.json();
                                                throw new Error(errorData.details || 'Failed to add fee/discount');
                                            }

                                            const result = await response.json();

                                            // Reset and close modal
                                            feeValueInput.value = "";
                                            feeTypeSelect.value = "";
                                            document.getElementById('feeLabel').value = "";
                                            discountTypeSection.style.display = 'none';
                                            feeModal.hide();

                                            // Show success message
                                            alert(result.message || 'Fee/discount added successfully');

                                            // Refresh the fees list from the database
                                            loadFees();
                                            // Refresh the page data to update fee breakdown
                                            fetchRequestDetails();

                                        } catch (error) {
                                            console.error('Error:', error);
                                            alert('Failed to add fee/discount: ' + error.message);
                                        }
                                    });


                                    // Document preview functionality - clean overlay for PDFs and images
                                    document.addEventListener('click', function (event) {
                                        // Check if the click is on a button that has data-bs-target="#documentModal" 
                                        // and data-document-url attributes
                                        let button = null;

                                        if (event.target.matches('[data-bs-target="#documentModal"]')) {
                                            button = event.target;
                                        } else if (event.target.closest('[data-bs-target="#documentModal"]')) {
                                            button = event.target.closest('[data-bs-target="#documentModal"]');
                                        }

                                        if (button && button.hasAttribute('data-document-url')) {
                                            event.preventDefault();
                                            event.stopPropagation();

                                            const documentUrl = button.getAttribute('data-document-url');
                                            const documentTitle = button.getAttribute('data-document-title');
                                            const fileExtension = documentUrl.split('.').pop().toLowerCase();
                                            const isPDF = fileExtension === 'pdf';
                                            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension);

                                            // Disable main page scrolling
                                            const originalBodyStyles = {
                                                overflow: document.body.style.overflow,
                                                position: document.body.style.position,
                                                width: document.body.style.width,
                                                height: document.body.style.height
                                            };

                                            document.body.style.overflow = 'hidden';
                                            document.body.style.position = 'fixed';
                                            document.body.style.width = '100%';
                                            document.body.style.height = '100%';

                                            // Create overlay container
                                            const overlay = document.createElement('div');
                                            overlay.style.position = 'fixed';
                                            overlay.style.top = '0';
                                            overlay.style.left = '0';
                                            overlay.style.width = '100%';
                                            overlay.style.height = '100%';
                                            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
                                            overlay.style.zIndex = '9999';
                                            overlay.style.display = 'flex';
                                            overlay.style.justifyContent = 'center';
                                            overlay.style.alignItems = 'center';
                                            overlay.style.cursor = 'pointer';

                                            // Close overlay when clicking on the background
                                            overlay.onclick = function (event) {
                                                if (event.target === overlay) {
                                                    closeOverlay();
                                                }
                                            };

                                            // Close on ESC key
                                            const handleEscape = function (event) {
                                                if (event.key === 'Escape') {
                                                    closeOverlay();
                                                }
                                            };
                                            document.addEventListener('keydown', handleEscape);

                                            // Function to close overlay and restore scrolling
                                            function closeOverlay() {
                                                document.body.removeChild(overlay);
                                                document.removeEventListener('keydown', handleEscape);

                                                // Restore original body styles
                                                document.body.style.overflow = originalBodyStyles.overflow || '';
                                                document.body.style.position = originalBodyStyles.position || '';
                                                document.body.style.width = originalBodyStyles.width || '';
                                                document.body.style.height = originalBodyStyles.height || '';
                                            }

                                            // Create close button (positioned absolutely in the top right corner)
                                            const closeButton = document.createElement('button');
                                            closeButton.innerHTML = '&times;';
                                            closeButton.style.position = 'absolute';
                                            closeButton.style.top = '20px';
                                            closeButton.style.right = '20px';
                                            closeButton.style.background = 'rgba(255, 255, 255, 0.2)';
                                            closeButton.style.color = 'white';
                                            closeButton.style.border = 'none';
                                            closeButton.style.borderRadius = '50%';
                                            closeButton.style.width = '40px';
                                            closeButton.style.height = '40px';
                                            closeButton.style.fontSize = '24px';
                                            closeButton.style.cursor = 'pointer';
                                            closeButton.style.transition = 'background 0.2s';
                                            closeButton.style.zIndex = '10000';
                                            closeButton.onmouseover = function () {
                                                this.style.background = 'rgba(255, 255, 255, 0.3)';
                                            };
                                            closeButton.onmouseout = function () {
                                                this.style.background = 'rgba(255, 255, 255, 0.2)';
                                            };
                                            closeButton.onclick = function (e) {
                                                e.stopPropagation();
                                                closeOverlay();
                                            };
                                            overlay.appendChild(closeButton);

                                            // Create loading indicator container
                                            const loadingContainer = document.createElement('div');
                                            loadingContainer.style.display = 'flex';
                                            loadingContainer.style.flexDirection = 'column';
                                            loadingContainer.style.alignItems = 'center';
                                            loadingContainer.style.justifyContent = 'center';
                                            loadingContainer.style.position = 'absolute';
                                            loadingContainer.style.zIndex = '1000';

                                            // Create loading indicator
                                            const loadingIndicator = document.createElement('div');
                                            loadingIndicator.className = 'spinner-border text-light';
                                            loadingIndicator.style.width = '3rem';
                                            loadingIndicator.style.height = '3rem';
                                            loadingContainer.appendChild(loadingIndicator);

                                            // Create loading text
                                            const loadingText = document.createElement('div');
                                            loadingText.textContent = 'Loading document...';
                                            loadingText.style.color = 'white';
                                            loadingText.style.marginTop = '1.5rem';
                                            loadingText.style.fontSize = '1.1rem';
                                            loadingContainer.appendChild(loadingText);

                                            // Create troubleshooting text (only shown during loading)
                                            const troubleText = document.createElement('div');
                                            troubleText.innerHTML = '<p class="text-light text-center mt-2" style="font-size: 0.9rem;">If the document doesn\'t load, try <a href="' + documentUrl + '" target="_blank" style="color: #328bffff; text-decoration: underline;">opening in a new tab</a> or refreshing the page.</p>';
                                            troubleText.style.color = 'white';
                                            troubleText.style.marginTop = '0.5rem';
                                            troubleText.style.textAlign = 'center';
                                            loadingContainer.appendChild(troubleText);

                                            overlay.appendChild(loadingContainer);

                                            // Function to remove loading elements
                                            function removeLoadingElements() {
                                                loadingContainer.remove();
                                            }

                                            if (isPDF) {
                                                // For PDF files - Google Docs viewer in full overlay
                                                const viewerUrl = `https://docs.google.com/gview?url=${encodeURIComponent(documentUrl)}&embedded=true`;

                                                const iframe = document.createElement('iframe');
                                                iframe.src = viewerUrl;
                                                iframe.style.width = '90%';
                                                iframe.style.height = '90%';
                                                iframe.style.border = 'none';
                                                iframe.style.borderRadius = '8px';
                                                iframe.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.5)';
                                                iframe.style.opacity = '0';
                                                iframe.style.transition = 'opacity 0.3s ease-in-out';

                                                iframe.onload = function () {
                                                    // Hide loading elements when iframe loads
                                                    removeLoadingElements();
                                                    iframe.style.opacity = '1';
                                                };

                                                overlay.appendChild(iframe);

                                            } else if (isImage) {
                                                // For image files - centered image with close button
                                                const imageContainer = document.createElement('div');
                                                imageContainer.style.position = 'relative';
                                                imageContainer.style.maxWidth = '90%';
                                                imageContainer.style.maxHeight = '90%';

                                                const img = document.createElement('img');
                                                img.src = documentUrl;
                                                img.alt = documentTitle;
                                                img.style.maxWidth = '100%';
                                                img.style.maxHeight = '90vh';
                                                img.style.borderRadius = '8px';
                                                img.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.5)';
                                                img.style.objectFit = 'contain';
                                                img.style.opacity = '0';
                                                img.style.transition = 'opacity 0.3s ease-in-out';

                                                img.onload = function () {
                                                    // Hide loading elements when image loads
                                                    removeLoadingElements();
                                                    img.style.opacity = '1';
                                                };

                                                img.onerror = function () {
                                                    // If image fails to load, show error message
                                                    loadingIndicator.style.display = 'none';
                                                    loadingText.textContent = 'Failed to load image. Please try opening in a new tab.';
                                                    loadingText.style.color = '#ff6b6b';
                                                    // Keep the troubleshooting text visible for errors
                                                };

                                                imageContainer.appendChild(img);
                                                overlay.appendChild(imageContainer);

                                            } else {
                                                // For other file types - download link
                                                removeLoadingElements();

                                                const downloadContainer = document.createElement('div');
                                                downloadContainer.style.background = 'white';
                                                downloadContainer.style.padding = '2rem';
                                                downloadContainer.style.borderRadius = '8px';
                                                downloadContainer.style.textAlign = 'center';

                                                const message = document.createElement('p');
                                                message.textContent = 'This file type cannot be previewed.';
                                                message.style.marginBottom = '1rem';

                                                const downloadLink = document.createElement('a');
                                                downloadLink.href = documentUrl;
                                                downloadLink.className = 'btn btn-primary';
                                                downloadLink.innerHTML = '<i class="bi bi-download"></i> Download File';
                                                downloadLink.download = documentTitle;
                                                downloadLink.onclick = function () {
                                                    closeOverlay();
                                                };

                                                downloadContainer.appendChild(message);
                                                downloadContainer.appendChild(downloadLink);
                                                overlay.appendChild(downloadContainer);
                                            }

                                            // Add to document body
                                            document.body.appendChild(overlay);
                                        }
                                    });

                                    // Initialize compact calendar
                                    let calendar;
                                    function initializeCalendar() {
                                        const calendarEl = document.getElementById('calendar');
                                        if (!calendarEl) return;

                                        calendar = new FullCalendar.Calendar(calendarEl, {
                                            initialView: 'timeGridWeek',
                                            headerToolbar: {
                                                left: 'prev,next today',
                                                center: 'title',
                                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                                            },
                                            height: 'auto',
                                            handleWindowResize: true,
                                            windowResizeDelay: 200,
                                            aspectRatio: 1.5,
                                            expandRows: true,
                                            events: [],
                                            eventClick: function (info) {
                                                const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);
                                                if (request) {
                                                    showEventModal(request);
                                                }
                                            },
                                            eventDidMount: function (info) {
                                                // Add custom styling based on status
                                                const status = info.event.extendedProps.status;
                                                if (status === 'Pending Approval') {
                                                    info.el.style.backgroundColor = 'var(--warning-color)';
                                                    info.el.style.borderColor = 'var(--warning-color)';
                                                } else if (status === 'Approved' || status === 'Scheduled') {
                                                    info.el.style.backgroundColor = 'var(--success-color)';
                                                    info.el.style.borderColor = 'var(--success-color)';
                                                } else if (status === 'Rejected') {
                                                    info.el.style.backgroundColor = 'var(--danger-color)';
                                                    info.el.style.borderColor = 'var(--danger-color)';
                                                } else if (status === 'Awaiting Payment') {
                                                    info.el.style.backgroundColor = 'var(--info-color)';
                                                    info.el.style.borderColor = 'var(--info-color)';
                                                }
                                            },
                                            datesSet: function (info) {
                                                // Force refresh of calendar rendering
                                                calendar.updateSize();
                                            },
                                            viewDidMount: function (info) {
                                                // Ensure proper initial rendering
                                                setTimeout(() => calendar.updateSize(), 0);
                                            },
                                            eventTimeFormat: {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                hour12: true
                                            },
                                            slotMinTime: '06:00:00',
                                            slotMaxTime: '22:00:00',
                                            allDaySlot: false,
                                            nowIndicator: true,
                                            navLinks: true,
                                            dayHeaderFormat: { weekday: 'short', month: 'short', day: 'numeric' },
                                            eventDisplay: 'block'
                                        });

                                        calendar.render();
                                    }

                                    // Function to show event details in modal
                                    function showEventModal(request) {
                                        document.getElementById('eventModalTitle').textContent = request.form_details.calendar_info.title;
                                        document.getElementById('modalRequester').textContent = `${request.user_details.first_name} ${request.user_details.last_name}`;
                                        document.getElementById('modalPurpose').textContent = request.form_details.purpose;
                                        document.getElementById('modalParticipants').textContent = request.form_details.num_participants;
                                        document.getElementById('modalStatus').textContent = request.form_details.status.name;
                                        document.getElementById('modalFee').textContent = `₱${request.fees.tentative_fee}`;
                                        document.getElementById('modalApprovals').textContent = `${request.approval_info.approval_count}/3`;

                                        // Format requested items
                                        let itemsHtml = '';
                                        if (request.requested_items.facilities.length > 0) {
                                            itemsHtml += '<h6>Facilities:</h6>';
                                            itemsHtml += request.requested_items.facilities.map(f =>
                                                `<p>• ${f.name} - ₱${f.fee} ${f.is_waived ? '(Waived)' : ''}</p>`
                                            ).join('');
                                        }

                                        if (request.requested_items.equipment.length > 0) {
                                            itemsHtml += '<h6 class="mt-2">Equipment:</h6>';
                                            itemsHtml += request.requested_items.equipment.map(e =>
                                                `<p>• ${e.name} - ₱${e.fee} ${e.is_waived ? '(Waived)' : ''}</p>`
                                            ).join('');
                                        }

                                        document.getElementById('modalItems').innerHTML = itemsHtml || '<p>No items requested</p>';

                                        // Set up view details button
                                        document.getElementById('modalViewDetails').onclick = function () {
                                            window.location.href = `/admin/requisition/${request.request_id}`;
                                        };

                                        eventModal.show();
                                    }

                                    // Function to update additional fees display
                                    function updateAdditionalFees(requisitionFees) {
                                        const additionalFeesContainer = document.getElementById('additionalFeesContainer');

                                        // Clear existing content
                                        additionalFeesContainer.innerHTML = '';

                                        if (requisitionFees && requisitionFees.length > 0) {
                                            requisitionFees.forEach(fee => {
                                                const feeElement = document.createElement('div');
                                                feeElement.className = 'd-flex justify-content-between align-items-center mb-1';

                                                let amountText = '';
                                                if (fee.type === 'fee') {
                                                    amountText = `₱${parseFloat(fee.fee_amount).toFixed(2)}`;
                                                } else if (fee.type === 'discount') {
                                                    if (fee.discount_type === 'Percentage') {
                                                        amountText = `${parseFloat(fee.discount_amount).toFixed(2)}%`;
                                                    } else {
                                                        amountText = `-₱${parseFloat(fee.discount_amount).toFixed(2)}`;
                                                    }
                                                } else if (fee.type === 'mixed') {
                                                    amountText = `₱${parseFloat(fee.fee_amount).toFixed(2)} - ${parseFloat(fee.discount_amount).toFixed(2)}${fee.discount_type === 'Percentage' ? '%' : '₱'}`;
                                                }

                                                feeElement.innerHTML = `
                                                                                                                                                                                                        <span class="item-name">${fee.label}</span>
                                                                                                                                                                                                        <span class="item-price">${amountText}</span>
                                                                                                                                                                                                    `;
                                                additionalFeesContainer.appendChild(feeElement);
                                            });
                                        } else {
                                            additionalFeesContainer.innerHTML = '<p class="text-muted">No additional fees or discounts</p>';
                                        }
                                    }

                                    // Update the fetchRequestDetails function to initialize calendar after content is visible
                                    async function fetchRequestDetails() {
                                        try {
                                            const response = await fetch(`http://127.0.0.1:8000/api/admin/requisition-forms`, {
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Accept': 'application/json'
                                                }
                                            });

                                            if (!response.ok) throw new Error('Failed to fetch request details');

                                            allRequests = await response.json();
                                            const request = allRequests.find(req => req.request_id == requestId);
                                            if (!request) throw new Error('Request not found');

                                            // Update approval counts for the finalize modal
                                            updateApprovalCounts(
                                                request.approval_info.approval_count,
                                                request.approval_info.rejection_count
                                            );

                                            const is_late = request.status_tracking.is_late;
                                            console.log("is_late:", is_late);
                                            console.log("Full request object:", request);

                                            // Update request ID in title
                                            document.getElementById('requestIdTitle').textContent = String(requestId).padStart(4, '0');

                                            // After successful data fetch and updates, show content and hide loading state
                                            document.getElementById('loadingState').style.display = 'none';
                                            document.getElementById('contentState').style.display = 'block';
                                            document.getElementById('totalApprovedFee').textContent = `₱${parseFloat(request.fees.approved_fee).toFixed(2)}`;

                                            // Initialize calendar AFTER content is visible
                                            setTimeout(() => {
                                                initializeCalendar();

                                                // Update calendar with all events
                                                if (calendar) {
                                                    calendar.removeAllEvents();
                                                    allRequests.forEach(req => {
                                                        calendar.addEvent({
                                                            title: req.form_details.calendar_info.title,
                                                            start: `${req.schedule.start_date}T${req.schedule.start_time}`,
                                                            end: `${req.schedule.end_date}T${req.schedule.end_time}`,
                                                            extendedProps: {
                                                                status: req.form_details.status.name,
                                                                requestId: req.request_id
                                                            },
                                                            description: req.form_details.calendar_info.description
                                                        });
                                                    });

                                                    // Highlight current request
                                                    calendar.getEvents().forEach(event => {
                                                        if (event.extendedProps.requestId == requestId) {
                                                            event.setProp('backgroundColor', 'var(--primary-color)');
                                                            event.setProp('borderColor', 'var(--primary-color)');
                                                        }
                                                    });
                                                }
                                            }, 100);


                                            // Update status badge
                                            const statusBadge = document.getElementById('statusBadge');
                                            statusBadge.textContent = request.form_details.status.name;
                                            statusBadge.className = `badge bg-${getStatusColor(request.form_details.status.name)}`;


                                            // Update user details
                                            document.getElementById('userDetails').innerHTML = `
                                                                                                                                                                                                                                                                                                <p><strong>Name:</strong> ${request.user_details.first_name} ${request.user_details.last_name}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Email:</strong> ${request.user_details.email}</p>
                                                                                                                                                                                                                                                                                                <p><strong>User Type:</strong> ${request.user_details.user_type}</p>
                                                                                                                                                                                                                                                                                                <p><strong>School ID:</strong> ${request.user_details.school_id || 'N/A'}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Organization:</strong> ${request.user_details.organization_name || 'N/A'}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Contact:</strong> ${request.user_details.contact_number || 'N/A'}</p>
                                                                                                                                                                                                                                                                                            `;

                                            // Update form details
                                            document.getElementById('formDetails').innerHTML = `
                                                                                                                                                                                                                                                                                                <p><strong>Purpose:</strong> ${request.form_details.purpose}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Participants:</strong> ${request.form_details.num_participants}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Schedule:</strong> ${formatDateTime(request.schedule)}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Additional Requests:</strong> ${request.form_details.additional_requests || 'None'}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Formal Letter:</strong> ${request.documents.formal_letter.url ?
                                                    `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                                                                                                                                                                                                                                                                                        data-document-url="${request.documents.formal_letter.url}" data-document-title="Formal Letter">
                                                                                                                                                                                                                                                                                                        View Document
                                                                                                                                                                                                                                                                                                    </button>` :
                                                    'Not uploaded'}</p>
                                                                                                                                                                                                                                                                                                <p><strong>Facility Setup:</strong> ${request.documents.facility_layout.url ?
                                                    `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                                                                                                                                                                                                                                                                                        data-document-url="${request.documents.facility_layout.url}" data-document-title="Facility Setup">
                                                                                                                                                                                                                                                                                                        View Document
                                                                                                                                                                                                                                                                                                    </button>` :
                                                    'Not uploaded'}</p>
                                                                                                                                                                                                                                                                                            `;
                                            // Update requested items with fee breakdown and waiver checkboxes
                                            document.getElementById('requestedItems').innerHTML = `
                                                                                                                                                                                                                                                    <div class="mb-3">
                                                                                                                                                                                                                                                        <h6>Facilities:</h6>
                                                                                                                                                                                                                                                        ${request.requested_items.facilities.length > 0 ?
                                                    request.requested_items.facilities.map(f =>
                                                        `<div class="d-flex justify-content-between align-items-center mb-2 item-row ${f.is_waived ? 'waived' : ''}">
                                                                                                                                                                                                                                                                    <div class="d-flex align-items-center">
                                                                                                                                                                                                                                                                        <div class="form-check me-2">
                                                                                                                                                                                                                                                                            <input class="form-check-input waiver-checkbox" type="checkbox" 
                                                                                                                                                                                                                                                                                data-type="facility" 
                                                                                                                                                                                                                                                                                data-id="${f.id}"
                                                                                                                                                                                                                                                                                ${f.is_waived ? 'checked' : ''}>
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                        <span class="item-name">${f.name}</span>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                    <span class="item-price">₱${f.fee}</span>
                                                                                                                                                                                                                                                                </div>`
                                                    ).join('') : '<p>No facilities requested</p>'}

                                                                                                                                                                                                                                                        <h6 class="mt-3">Equipment:</h6>
                                                                                                                                                                                                                                                        ${request.requested_items.equipment.length > 0 ?
                                                    request.requested_items.equipment.map(e =>
                                                        `<div class="d-flex justify-content-between align-items-center mb-2 item-row ${e.is_waived ? 'waived' : ''}">
                                                                                                                                                                                                                                                                    <div class="d-flex align-items-center">
                                                                                                                                                                                                                                                                        <div class="form-check me-2">
                                                                                                                                                                                                                                                                            <input class="form-check-input waiver-checkbox" type="checkbox" 
                                                                                                                                                                                                                                                                                data-type="equipment" 
                                                                                                                                                                                                                                                                                data-id="${e.id}"
                                                                                                                                                                                                                                                                                ${e.is_waived ? 'checked' : ''}>
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                        <span class="item-name">• ${e.name}</span>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                    <span class="item-price">₱${e.fee}</span>
                                                                                                                                                                                                                                                                </div>`
                                                    ).join('') : '<p>No equipment requested</p>'}
                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                    <div class="d-flex justify-content-end mb-2">

                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                `;

                                            // Update requested items with fee breakdown and waiver checkboxes
                                            document.getElementById('requestedItems').innerHTML = `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6>Facilities:</h6>
                                                                                                                        ${request.requested_items.facilities.length > 0 ?
                                                    request.requested_items.facilities.map(f =>
                                                        `<div class="d-flex justify-content-between align-items-center mb-2 item-row ${f.is_waived ? 'waived' : ''}">
                                                                                                                                    <div class="d-flex align-items-center">
                                                                                                                                        <div class="form-check me-2">
                                                                                                                                            <input class="form-check-input waiver-checkbox" type="checkbox" 
                                                                                                                                                data-type="facility" 
                                                                                                                                                data-id="${f.requested_facility_id}"  // CHANGED: Use pivot ID
                                                                                                                                                ${f.is_waived ? 'checked' : ''}>
                                                                                                                                        </div>
                                                                                                                                        <span class="item-name">${f.name}</span>
                                                                                                                                    </div>
                                                                                                                                    <span class="item-price">₱${f.fee}</span>
                                                                                                                                </div>`
                                                    ).join('') : '<p>No facilities requested</p>'}

                                                                                                                        <h6 class="mt-3">Equipment:</h6>
                                                                                                                        ${request.requested_items.equipment.length > 0 ?
                                                    request.requested_items.equipment.map(e =>
                                                        `<div class="d-flex justify-content-between align-items-center mb-2 item-row ${e.is_waived ? 'waived' : ''}">
                                                                                                                                    <div class="d-flex align-items-center">
                                                                                                                                        <div class="form-check me-2">
                                                                                                                                            <input class="form-check-input waiver-checkbox" type="checkbox" 
                                                                                                                                                data-type="equipment" 
                                                                                                                                                data-id="${e.requested_equipment_id}"  // CHANGED: Use pivot ID
                                                                                                                                                ${e.is_waived ? 'checked' : ''}>
                                                                                                                                        </div>
                                                                                                                                        <span class="item-name">• ${e.name}</span>
                                                                                                                                    </div>
                                                                                                                                    <span class="item-price">₱${e.fee}</span>
                                                                                                                                </div>`
                                                    ).join('') : '<p>No equipment requested</p>'}
                                                                                                                    </div>
                                                                                                                    <div class="d-flex justify-content-end mb-2">
                                                                                                                    </div>
                                                                                                                `;

                                            // Add event listeners to waiver checkboxes
                                            document.querySelectorAll('.waiver-checkbox').forEach(checkbox => {
                                                checkbox.addEventListener('change', function () {
                                                    handleWaiverChange(this);
                                                });
                                            });

                                            // Add event listener to waive all switch
                                            document.getElementById('waiveAllSwitch').addEventListener('change', function () {
                                                handleWaiveAll(this);
                                            });



                                            // Update footer fee dynamically
                                            document.getElementById('tentativeFee').textContent = `₱${request.fees.tentative_fee}`;


                                            // Update form status with Bootstrap Icons and card footers
                                            document.getElementById('formStatus').innerHTML = `
                <div class="row g-2">
                    <div class="col-md d-flex">
                        <div class="card text-center flex-fill h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                                <p class="mb-0 text-center">
                                    <i class="bi bi-cash fs-3"></i><br>
                                    <strong>Approved Fee:</strong><br>
                                    ${request.fees.approved_fee ? `₱${request.fees.approved_fee}` : 'Pending'}
                                </p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-ghost">Configure Fees</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md d-flex">
                        <div class="card text-center flex-fill h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                                <p class="mb-0 text-center">
                                    <i class="bi bi-check2-circle fs-3"></i><br>
                                    <strong>Approvals:</strong><br>
                                    ${request.approval_info.approval_count}
                                </p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-ghost">Approve</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md d-flex">
                        <div class="card text-center flex-fill h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                                <p class="mb-0 text-center">
                                    <i class="bi bi-x-circle fs-3"></i><br>
                                    <strong>Rejections:</strong><br>
                                    ${request.approval_info.rejection_count}
                                </p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-ghost">Reject</button>
                            </div>
                        </div>
                    </div>

                                        <div class="col-md d-flex">
                        <div class="card text-center flex-fill h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                                <p class="mb-0 text-center">
                                    <i class="bi bi-receipt fs-3"></i><br>
                                    <strong>Proof of Payment:</strong><br>
                                    ${request.documents.proof_of_payment.url ? 'Uploaded' : 'Not Uploaded'}
                                </p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-ghost">View Receipt</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md d-flex">
                        <div class="card text-center flex-fill h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                                <p class="mb-0 text-center">
                                    <i class="bi bi-exclamation-triangle fs-3"></i><br>
                                    <strong>Late Penalty:</strong><br>
                                    ${request.fees.late_penalty_fee ? `₱${request.fees.late_penalty_fee}` : 'N/A'}
                                </p>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-ghost">Set Penalty Fee</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md d-flex">
                        <div class="card text-center flex-fill h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                        <p class="mb-0 text-center">
                        <i class="bi bi-clock-history fs-3"></i><br>
                        <strong>Is Late:</strong><br>
                        ${is_late ? 'Yes' : 'No'}
                         </p>
                </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-ghost">Mark as Late</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
                `;





                                            // After successful data fetch and updates, show content and hide loading state
                                            document.getElementById('loadingState').style.display = 'none';
                                            document.getElementById('contentState').style.display = 'block';

                                            loadFees();
                                            updateBaseFees(request.requested_items);
                                            updateAdditionalFees(request.fees.requisition_fees);


                                        } catch (error) {
                                            console.error('Error:', error);
                                            alert('Failed to load request details');
                                            // Show error state
                                            document.getElementById('loadingState').style.display = 'none';
                                            document.getElementById('contentState').innerHTML = `
                                                                                                                                                                                                                                                                                                <div class="alert alert-danger">
                                                                                                                                                                                                                                                                                                    Failed to load request details. Please try refreshing the page.
                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                            `;
                                            document.getElementById('contentState').style.display = 'block';
                                        }
                                    }

                                    // Function to update base fees display
                                    function updateBaseFees(requestedItems) {
                                        const facilitiesContainer = document.getElementById('facilitiesFees');
                                        const equipmentContainer = document.getElementById('equipmentFees');

                                        // Clear existing content
                                        facilitiesContainer.innerHTML = '';
                                        equipmentContainer.innerHTML = '';

                                        // Add facilities
                                        if (requestedItems.facilities && requestedItems.facilities.length > 0) {
                                            requestedItems.facilities.forEach(facility => {
                                                const facilityElement = document.createElement('div');
                                                facilityElement.className = 'd-flex justify-content-between align-items-center mb-1';
                                                facilityElement.innerHTML = `
                                                                                                                                                                                                                                            <span class="item-name ${facility.is_waived ? 'waived' : ''}">
                                                                                                                                                                                                                                                ${facility.name}${facility.is_waived ? ' (Waived)' : ''}
                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                            <span class="item-price ${facility.is_waived ? 'waived' : ''}">
                                                                                                                                                                                                                                                ₱${facility.fee}
                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                        `;
                                                facilitiesContainer.appendChild(facilityElement);
                                            });
                                        } else {
                                            facilitiesContainer.innerHTML = '<p></p>';
                                        }

                                        // Add equipment
                                        if (requestedItems.equipment && requestedItems.equipment.length > 0) {
                                            requestedItems.equipment.forEach(equipment => {
                                                const equipmentElement = document.createElement('div');
                                                equipmentElement.className = 'd-flex justify-content-between align-items-center mb-1';
                                                equipmentElement.innerHTML = `
                                                                                                                                                                                                                                            <span class="item-name ${equipment.is_waived ? 'waived' : ''}">
                                                                                                                                                                                                                                                ${equipment.name}${equipment.is_waived ? ' (Waived)' : ''}
                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                            <span class="item-price ${equipment.is_waived ? 'waived' : ''}">
                                                                                                                                                                                                                                                ₱${equipment.fee} × ${equipment.quantity}
                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                        `;
                                                equipmentContainer.appendChild(equipmentElement);
                                            });
                                        } else {
                                            equipmentContainer.innerHTML = '<p></p>';
                                        }
                                    }

                                    function formatDateTime(schedule) {
                                        const startDate = new Date(schedule.start_date + 'T' + schedule.start_time);
                                        const endDate = new Date(schedule.end_date + 'T' + schedule.end_time);
                                        return `${startDate.toLocaleString()} to ${endDate.toLocaleString()}`;
                                    }

                                    function getStatusColor(status) {
                                        const colors = {
                                            'Pending Approval': 'warning',
                                            'Awaiting Payment': 'primary',
                                            'Scheduled': 'success',
                                            'Rejected': 'danger',
                                            'Cancelled': 'secondary'
                                        };
                                        return colors[status] || 'secondary';
                                    }

                                    // Function to load and display fees
                                    // Function to load and display fees
                                    async function loadFees() {
                                        try {
                                            const response = await fetch(`/api/admin/requisition/${requestId}/fees`, {
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Accept': 'application/json'
                                                }
                                            });

                                            if (!response.ok) throw new Error('Failed to fetch fees');

                                            const fees = await response.json();

                                            // Clear existing fees
                                            feesContainer.innerHTML = '';

                                            if (fees.length === 0) {
                                                togglePlaceholder();
                                                return;
                                            }

                                            // Add each fee to the container
                                            for (const fee of fees) {
                                                const feeItem = await createFeeItem(fee);
                                                feesContainer.appendChild(feeItem);
                                            }

                                            togglePlaceholder();

                                        } catch (error) {
                                            console.error('Error loading fees:', error);
                                        }
                                    }

                                    // Function to create a fee item element
                                    async function createFeeItem(fee) {
                                        const feeItem = document.createElement("div");
                                        feeItem.className = "fee-item d-flex align-items-start p-2 mb-2 rounded";
                                        feeItem.dataset.feeId = fee.fee_id;

                                        const timestamp = new Date(fee.created_at).toLocaleString('en-US', {
                                            month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
                                        });

                                        // Ensure amount is a number before using toFixed()
                                        const amount = parseFloat(fee.type === 'discount' ? fee.discount_amount : fee.fee_amount);
                                        const typeName = fee.type === 'discount' ? 'Discount' : 'Additional fee';

                                        // Use the admin info from the fee response if available
                                        let adminName = fee.added_by?.name || 'Admin';
                                        let adminPhoto = null;

                                        // If we don't have admin info from the fee response, try to get it
                                        if (!fee.added_by) {
                                            try {
                                                const adminResponse = await fetch('http://127.0.0.1:8000/api/admin/profile', {
                                                    headers: {
                                                        'Authorization': `Bearer ${adminToken}`,
                                                        'Accept': 'application/json'
                                                    }
                                                });

                                                if (adminResponse.ok) {
                                                    const adminData = await adminResponse.json();
                                                    adminName = `${adminData.first_name}${adminData.middle_name ? ' ' + adminData.middle_name : ''} ${adminData.last_name}`;
                                                    adminPhoto = adminData.photo_url;
                                                }
                                            } catch (error) {
                                                console.error('Failed to fetch admin info:', error);
                                            }
                                        } else {
                                            // Use the admin info from the fee response
                                            adminName = fee.added_by.name;
                                        }

                                        feeItem.innerHTML = `
                                                                                                                                                                                                                                                        ${adminPhoto ?
                                                `<img src="${adminPhoto}" class="rounded-circle me-3" width="32" height="32" alt="Admin Photo">` :
                                                `<i class="bi bi-person-circle fs-5 me-3 text-secondary"></i>`
                                            }
                                                                                                                                                                                                                                                        <div class="flex-grow-1">
                                                                                                                                                                                                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                                                                                                                                                                                                <div>
                                                                                                                                                                                                                                                                    <small class="text-muted fst-italic">
                                                                                                                                                                                                                                                                        ${fee.label} (${typeName}) of ₱${amount.toFixed(2)} added by <strong>${adminName}</strong>
                                                                                                                                                                                                                                                                    </small>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                <button class="btn btn-sm btn-icon btn-light-danger remove-btn">
                                                                                                                                                                                                                                                                    <i class="bi bi-x-lg"></i>
                                                                                                                                                                                                                                                                </button>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                            <small class="text-muted fst-italic">${timestamp}</small>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                    `;

                                        // Add remove functionality for regular fees
                                        feeItem.querySelector(".remove-btn").addEventListener("click", async function () {
                                            try {
                                                const deleteResponse = await fetch(`http://127.0.0.1:8000/api/admin/requisition/${requestId}/fee/${fee.fee_id}`, {
                                                    method: 'DELETE',
                                                    headers: {
                                                        'Authorization': `Bearer ${adminToken}`,
                                                        'Accept': 'application/json'
                                                    }
                                                });

                                                if (!deleteResponse.ok) {
                                                    throw new Error('Failed to delete fee from database');
                                                }

                                                feeItem.remove();
                                                togglePlaceholder();
                                                fetchRequestDetails(); // Refresh fee breakdown

                                            } catch (error) {
                                                console.error('Error removing fee:', error);
                                                alert('Failed to remove fee: ' + error.message);
                                            }
                                        });

                                        return feeItem;
                                    }

                                    let currentApprovalCount = 0;
                                    let currentRejectionCount = 0;

                                    // Update approval counts when data is loaded
                                    function updateApprovalCounts(approvalCount, rejectionCount) {
                                        currentApprovalCount = approvalCount;
                                        currentRejectionCount = rejectionCount;
                                        document.getElementById('currentApprovalCount').textContent = approvalCount;
                                        document.getElementById('currentRejectionCount').textContent = rejectionCount;
                                    }

                                    // Approve button handler
                                    document.getElementById('approveBtn').addEventListener('click', function () {
                                        approveModal.show();
                                    });

                                    // Reject button handler (from dropdown)
                                    document.getElementById('rejectBtn').addEventListener('click', function () {
                                        rejectModal.show();
                                    });

                                    // Finalize button handler (from dropdown)
                                    document.getElementById('finalizeBtn').addEventListener('click', function () {
                                        finalizeModal.show();
                                    });

                                    // Confirm approve action
                                    document.getElementById('confirmApprove').addEventListener('click', async function () {
                                        const remarks = document.getElementById('approveRemarks').value;
                                        const adminToken = localStorage.getItem('adminToken');

                                        console.log('Approve button clicked', {
                                            requestId: requestId,
                                            hasToken: !!adminToken,
                                            remarks: remarks
                                        });

                                        try {
                                            const response = await fetch(`/api/admin/requisition/${requestId}/approve`, {
                                                method: 'POST',
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    remarks: remarks
                                                })
                                            });

                                            console.log('Response status:', response.status, response.statusText);

                                            // First, check if response is JSON
                                            const contentType = response.headers.get('content-type');
                                            let responseData;

                                            if (contentType && contentType.includes('application/json')) {
                                                responseData = await response.json();
                                                console.log('Response JSON:', responseData);
                                            } else {
                                                // If not JSON, get the text response
                                                const textResponse = await response.text();
                                                console.log('Response text:', textResponse);
                                                throw new Error(textResponse || 'Non-JSON response from server');
                                            }

                                            if (!response.ok) {
                                                // Check for different error response formats
                                                const errorMessage = responseData.error ||
                                                    responseData.message ||
                                                    JSON.stringify(responseData) ||
                                                    'Failed to approve request';
                                                throw new Error(errorMessage);
                                            }

                                            alert('Request approved successfully!');
                                            approveModal.hide();

                                            // Refresh the page to show updated approval status
                                            fetchRequestDetails();

                                        } catch (error) {
                                            console.error('Error approving request:', error);
                                            console.error('Full error details:', error.message);
                                            alert('Error: ' + error.message);
                                        }
                                    });

                                    // Confirm reject action
                                    document.getElementById('confirmReject').addEventListener('click', async function () {
                                        const remarks = document.getElementById('rejectRemarks').value;
                                        const adminToken = localStorage.getItem('adminToken');

                                        try {
                                            const response = await fetch(`/api/admin/requisition/${requestId}/reject`, {
                                                method: 'POST',
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    remarks: remarks
                                                })
                                            });

                                            // First, check if response is JSON
                                            const contentType = response.headers.get('content-type');
                                            let responseData;

                                            if (contentType && contentType.includes('application/json')) {
                                                responseData = await response.json();
                                            } else {
                                                // If not JSON, get the text response
                                                const textResponse = await response.text();
                                                throw new Error(textResponse || 'Non-JSON response from server');
                                            }

                                            if (!response.ok) {
                                                const errorMessage = responseData.error ||
                                                    responseData.message ||
                                                    JSON.stringify(responseData) ||
                                                    'Failed to reject request';
                                                throw new Error(errorMessage);
                                            }

                                            alert('Request rejected successfully!');
                                            rejectModal.hide();

                                            // Refresh the page to show updated status
                                            fetchRequestDetails();

                                        } catch (error) {
                                            console.error('Error rejecting request:', error);
                                            console.error('Full error details:', error.message);
                                            alert('Error: ' + error.message);
                                        }
                                    });

                                    // Confirm finalize action - Add proper validation
                                    document.getElementById('confirmFinalize').addEventListener('click', async function () {
                                        const calendarTitle = document.getElementById('calendarTitle').value.trim();
                                        const calendarDescription = document.getElementById('calendarDescription').value.trim();
                                        const remarks = document.getElementById('finalizeRemarks').value.trim();
                                        const adminToken = localStorage.getItem('adminToken');

                                        // Validate length constraints if fields are provided
                                        if (calendarTitle && calendarTitle.length > 50) {
                                            alert('Calendar Title must not exceed 50 characters.');
                                            document.getElementById('calendarTitle').focus();
                                            return;
                                        }

                                        if (calendarDescription && calendarDescription.length > 100) {
                                            alert('Calendar Description must not exceed 100 characters.');
                                            document.getElementById('calendarDescription').focus();
                                            return;
                                        }

                                        try {
                                            console.log('Finalizing request with:', {
                                                calendar_title: calendarTitle || null,
                                                calendar_description: calendarDescription || null,
                                                remarks: remarks || null
                                            });

                                            const response = await fetch(`/api/admin/requisition/${requestId}/finalize`, {
                                                method: 'POST',
                                                headers: {
                                                    'Authorization': `Bearer ${adminToken}`,
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    calendar_title: calendarTitle || null,
                                                    calendar_description: calendarDescription || null,
                                                    remarks: remarks || null
                                                })
                                            });

                                            // Log response for debugging
                                            console.log('Finalize response status:', response.status);

                                            const contentType = response.headers.get('content-type');
                                            let responseData;

                                            if (contentType && contentType.includes('application/json')) {
                                                responseData = await response.json();
                                                console.log('Finalize response JSON:', responseData);
                                            } else {
                                                const textResponse = await response.text();
                                                console.log('Finalize response text:', textResponse);
                                                throw new Error(textResponse || 'Non-JSON response from server');
                                            }

                                            if (!response.ok) {
                                                const errorMessage = responseData.error ||
                                                    responseData.message ||
                                                    (responseData.details ? JSON.stringify(responseData.details) : 'Failed to finalize request');
                                                throw new Error(errorMessage);
                                            }

                                            alert('Request finalized successfully! Status changed to Awaiting Payment.');
                                            finalizeModal.hide();

                                            // Clear modal fields for next use
                                            document.getElementById('calendarTitle').value = '';
                                            document.getElementById('calendarDescription').value = '';
                                            document.getElementById('finalizeRemarks').value = '';

                                            // Refresh the page to show updated status
                                            fetchRequestDetails();

                                        } catch (error) {
                                            console.error('Error finalizing request:', error);
                                            console.error('Full error details:', error.message);

                                            // More user-friendly error messages
                                            if (error.message.includes('calendar title')) {
                                                alert('Error: Calendar Title must not exceed 50 characters.');
                                            } else if (error.message.includes('calendar description')) {
                                                alert('Error: Calendar Description must not exceed 100 characters.');
                                            } else {
                                                alert('Error: ' + error.message);
                                            }
                                        }
                                    });

                                    fetchRequestDetails();
                                });
                            </script>
            @endsection