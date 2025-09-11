@extends('layouts.admin')
@section('title', 'Request View')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>

        /* Messenger-style chat bubbles */
.message-bubble {
    position: relative;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    word-wrap: break-word;
    background-color: #dce6eeff !important; /* Your custom color */
    color: black !important;
    border-top-left-radius: 0% !important;
}

.message-bubble::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 0;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-right: 8px solid #dce6eeff;
    border-bottom: 8px solid transparent;
}

/* Smooth scrolling for comments container */
.comments-container {
    scroll-behavior: smooth;
}

/* Custom scrollbar for comments */
.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.card-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Loading animation */
.comment-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

/* Empty state styling */
.empty-comments {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-comments i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

        #main {
            background-color: none !important;
        }

        /* Modern Status Selector Styles */
.status-selector-container {
    display: inline-block;
    z-index: 1000;
}

.btn-status-expand {
    width: 60px;
    height: 40px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 1.1rem;
    border: 1px solid #dee2e6;
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn-status-expand:hover {
    width: 200px;
    justify-content: flex-start;
    padding-left: 15px;
    background-color: #fff;
    border-color: #ced4da;
}

.btn-status-expand .btn-text {
    font-size: 0.85rem;
    font-weight: 500;
    margin-left: 10px;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
}

.btn-status-expand:hover .btn-text {
    opacity: 1;
    transform: translateX(0);
}

.status-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    width: 200px;
    background: white;
    border-radius: 8px;
    padding: 0.5rem;
    margin-top: 0.5rem;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s ease;
}

.status-selector-container:hover .status-dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.status-option {
    display: flex;
    align-items: center;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.status-option:hover {
    background-color: #f8f9fa;
}

.status-option i {
    margin-right: 10px;
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

.status-option span {
    font-size: 0.9rem;
}

        /* Allow event text to wrap inside timeGrid events */
        .fc-timegrid-event .fc-event-title,
        .fc-timegrid-event .fc-event-time {
            white-space: normal;
            /* allow line breaks */
            overflow-wrap: break-word;
            /* break long words if necessary */
            word-break: break-word;
            /* extra safety for long words */
        }

        .fc-timegrid-event {
            height: auto !important;
            /* let the event grow with content */
            min-height: 2.5em;
            /* optional minimum height */
        }

        #formStatus .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
            vertical-align: middle;
            /* or top */
        }

        .btn-ghost {
            background-color: transparent;
            border: none !important;
            color: inherit !important;
            /* keeps text color same as parent */
            padding: 0.375rem 0.75rem;
            /* same as default btn padding */
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .btn-ghost:hover {
            background-color: #e0e0e0 !important;
            /* light gray hover */
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
            border: 1px solid lightgray !important;
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


        .admin-container .card {
            border: none;
            border-color: none;
            background-color: none;
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
            padding: 10px;
            border-radius: 10px;;

        }

        /* Make the remark textarea resize automatically */
        .card-footer textarea {
            resize: none;
            overflow-y: hidden;
            transition: height 0.2s ease-in-out;
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

    <!-- Main Content -->
    <main id="main">
      
            <div class="card bg-transparent shadow-none pt-0" style="border: none !important; background-color: transparent !important">
                <div class="card-body">
                    <!-- Skeleton Loading -->
                    <div id="loadingState">
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
<!-- Form Status + Requester Details (same row) -->
<div class="row g-2">
    <!-- Left column: Requester Details -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Requester Details</h5>
            </div>
            <div class="card-body">
                <div id="formDetails"></div>
            </div>
        </div>
    </div>
    
    <!-- Right column: Requisition ID + Form Remarks -->
    <div class="col-md-8">
        <div class="d-flex flex-column h-100">
            <!-- Requisition ID # container -->
            <div class="card mb-2 flex-grow-1">
                <!-- Card header with Requisition ID and Status Badge -->
                <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
<h5 class="card-title mb-0 d-flex align-items-center justify-content-between w-100">
    <!-- Left side -->
    <div class="d-flex align-items-center">
        R-ID #<span id="requestIdTitle"></span>:
        <span id="statusBadge" class="badge ms-1"></span>
    </div>

    <!-- Right side - Modernized Status Selector -->
    <div class="status-selector-container position-relative">
        <div class="status-selector-trigger d-flex align-items-center">
            <button class="btn-status-expand expand-btn" type="button">
                <i class="bi bi-pencil-square"></i>
                <span class="btn-text">Change Status</span>
            </button>
        </div>
        
        <div class="status-dropdown-menu shadow">
            <div class="status-option" data-value="Scheduled">
                <i class="bi bi-calendar-check"></i>
                <span>Mark as Scheduled</span>
            </div>
            <div class="status-option" data-value="Ongoing">
                <i class="bi bi-play-circle"></i>
                <span>Mark as Ongoing</span>
            </div>
            <div class="status-option" data-value="Late">
                <i class="bi bi-clock-history"></i>
                <span>Mark as Late</span>
            </div>
        </div>
    </div>
</h5>




                </div>
                <!-- Card body -->
                <div class="card-body" style="padding: 40px !important;">
                    <div id="formStatus"></div>
                </div>
            </div>
            
            <!-- Form Remarks Container -->
<div class="card flex-grow-1" style="min-height: 300px !important;">
    <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Form Comments
        </h5>
        <span class="badge bg-primary" id="commentCount">0</span>
    </div>
    <div class="card-body p-3 comments-container" style="overflow-y: auto; max-height: 250px;">
        <div id="formRemarks">
            <div class="comment-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading comments...</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white p-3 border-top">
        <div class="input-group">
            <textarea class="form-control" rows="1" placeholder="Type a message..." 
                     aria-label="Type a message" id="commentTextarea" 
                     style="resize: none; border-radius: 20px;"></textarea>
            <button class="btn btn-primary rounded-circle ms-2" type="button" id="sendCommentBtn" 
                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-send"></i>
            </button>
        </div>
    </div>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="statusModalContent">
                        <!-- Content will be dynamically populated -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Confirm Change</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-stretch g-2 mt-1">
        <!-- Left column: Fee Waivers + Additional Fees -->
        <div class="col-lg-5 d-flex flex-column">
            <!-- Fee Waivers -->
            <div class="card mb-2">
                <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Requested Items</h5>
                    <!-- Toggle Switch -->
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="waiveAllSwitch">
                        <label class="form-check-label" for="waiveAllSwitch">
                            Waive All Fees
                        </label>
                    </div>
                </div>
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

            <!-- Additional Fees & Discounts -->
            <div class="card flex-grow-1">
                <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
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
            </div>
        </div>

        <!-- Right column: Calendar (reduced width) -->
        <div class="col-lg-7 d-flex">
            <div class="card flex-fill d-flex flex-column">
                <div class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Events Calendar</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="calendar-container flex-fill">
                        <div id="calendar" style="height:100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                            <!-- Add Fee Modal -->
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

                            <div class="row g-2 mt-1">
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

                            <!-- FOR APPROVE/REJECT BUTTONS -->

                            <style>
                                .expand-btn {
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 25px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: flex-start;
                                    padding: 2px 13px;
                                    font-size: 1.5rem;
                                    border: none;
                                    cursor: pointer;
                                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                    position: relative;
                                    overflow: hidden;
                                }

                                .expand-btn i {
                                    flex-shrink: 0;
                                    z-index: 2;
                                    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                }

                                .expand-btn .btn-text {
                                    font-size: 0.9rem;
                                    font-weight: 500;
                                    margin-left: 12px;
                                    opacity: 0;
                                    transform: translateX(-10px);
                                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                    white-space: nowrap;
                                }

                                .expand-btn:hover {
                                    width: 130px;
                                    justify-content: flex-start;
                                    padding-left: 15px;
                                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                                }

                                .expand-btn:hover .btn-text {
                                    opacity: 1;
                                    transform: translateX(0);
                                }

                                .expand-btn:hover i {
                                    transform: scale(1.1);
                                }

                                /* Button specific styles */
                                .btn-danger.expand-btn {
                                    background-color: #dc3545;
                                    color: white;
                                }

                                .btn-danger.expand-btn:hover {
                                    background-color: #c82333;
                                }

                                .btn-success.expand-btn {
                                    background-color: #28a745;
                                    color: white;
                                }

                                .btn-success.expand-btn:hover {
                                    background-color: #218838;
                                }

                                .expand-btn:active {
                                    transform: scale(0.98);
                                }
                            </style>

                            <div class="row mt-4">
                                <div class="col-12 text-center mb-3">
                                    <h5 class="demo-title fw-bold" style="color:#313131";>Approve Request?</h5>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-3 justify-content-center align-items-center">
                                        <!-- Reject button -->
                                        <button type="button" id="rejectBtn" class="btn btn-danger expand-btn">
                                            <i class="bi bi-x"></i>
                                            <span class="btn-text">Reject</span>
                                        </button>
                                        <!-- Accept button -->
                                        <button type="button" id="approveBtn" class="btn btn-success expand-btn">
                                            <i class="bi bi-check"></i>
                                            <span class="btn-text">Approve</span>
                                        </button>
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
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to approve this request? This action cannot be undone.</p>
                                    <p class="text-muted small">You will not be able to take further actions on this form
                                        after
                                        approval.</p>
                                    <div class="mb-3">
                                        <label for="approveRemarks" class="form-label">Remarks (Optional)</label>
                                        <textarea class="form-control" id="approveRemarks" rows="3"
                                            placeholder="Add any remarks here..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-success" id="confirmApprove">Confirm
                                        Approval</button>
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
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to reject this request? This action cannot be undone.</p>
                                    <p class="text-muted small">You will not be able to take further actions on this form
                                        after
                                        rejection.</p>
                                    <div class="mb-3">
                                        <label for="rejectRemarks" class="form-label">Remarks (Optional)</label>
                                        <textarea class="form-control" id="rejectRemarks" rows="3"
                                            placeholder="Add any remarks here..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" id="confirmReject">Confirm
                                        Rejection</button>
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
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
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
                                            placeholder="Enter calendar event description" required
                                            maxlength="100"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="finalizeRemarks" class="form-label">Remarks (Optional)</label>
                                        <textarea class="form-control" id="finalizeRemarks" rows="3"
                                            placeholder="Add any remarks here..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="confirmFinalize">Finalize
                                        Request</button>
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
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
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
                  
    </main>

@endsection





@section('scripts')
    <script src="{{ asset('js/admin/calendar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

             const commentsContainer = document.getElementById('formRemarks');
            const commentTextarea = document.querySelector('.card-footer textarea');
            const commentSendBtn = document.querySelector('.card-footer button');
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
            const statusOptions = document.querySelectorAll('.status-option');
            const updateStatusBtn = document.getElementById('updateStatusBtn');
            const statusUpdateModal = new bootstrap.Modal(document.
                getElementById('statusUpdateModal'));
            let selectedStatus = '';

            statusOptions.forEach(option => {
        option.addEventListener('click', function() {
            selectedStatus = this.dataset.value;
            
            // Remove any existing active class
            statusOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to selected option
            this.classList.add('active');
            
            // Enable the update button (though we'll handle this differently)
            // In the new UI, we'll show the modal immediately on selection
            showStatusUpdateModal(selectedStatus);
        });
    });

     // Load existing comments
   // Load comments
async function loadComments() {
    try {
        commentsContainer.innerHTML = `
            <div class="comment-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading comments...</span>
                </div>
            </div>
        `;

        const response = await fetch(`/api/admin/requisition/${requestId}/comments`, {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to load comments');

        const result = await response.json();
        
        // Update comment count badge
        document.getElementById('commentCount').textContent = result.comments?.length || 0;
        
        if (result.success && result.comments.length > 0) {
            displayComments(result.comments);
            // Auto-scroll to bottom to show newest messages
            scrollToBottom();
        } else {
            commentsContainer.innerHTML = `
                <div class="empty-comments">
                    <i class="bi bi-chat"></i>
                    <p>No comments added to this form yet.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading comments:', error);
        commentsContainer.innerHTML = `
            <div class="empty-comments text-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <p>Failed to load comments.</p>
            </div>
        `;
    }
}

function scrollToBottom() {
    const container = document.querySelector('.comments-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}
    
    // Display comments in the container

function displayComments(comments) {
    if (comments.length === 0) {
        commentsContainer.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 2rem;">No comments yet. Start the conversation!</p>';
        return;
    }

    commentsContainer.innerHTML = comments.map(comment => `
        <div class="comment mb-3">
            <div class="d-flex align-items-start">
                <!-- Admin Profile Picture -->
                <div class="me-2 flex-shrink-0">
                    ${comment.admin.photo_url ? 
                        `<img src="${comment.admin.photo_url}" class="rounded-circle" width="40" height="40" alt="${comment.admin.first_name}'s profile picture" style="object-fit: cover;">` :
                        `<div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" style="width: 40px; height: 40px; font-size: 1rem;">
                            ${comment.admin.first_name.charAt(0)}${comment.admin.last_name.charAt(0)}
                        </div>`
                    }
                </div>
                
                <!-- Message Bubble -->
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <strong class="me-2">${comment.admin.first_name} ${comment.admin.last_name}</strong>
                        <small class="text-muted">${formatTimeAgo(comment.created_at)}</small>
                    </div>
                    <div class="message-bubble bg-primary text-white p-3 rounded-3" style="max-width: 80%; border-bottom-left-radius: 4px !important;">
                        <p class="mb-0" style="white-space: pre-wrap; line-height: 1.4;">${escapeHtml(comment.comment)}</p>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}
// Helper function to format time ago (e.g., "2 minutes ago")
function formatTimeAgo(timestamp) {
    const now = new Date();
    const commentTime = new Date(timestamp);
    const diffInSeconds = Math.floor((now - commentTime) / 1000);
    
    if (diffInSeconds < 60) {
        return 'just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 604800) {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days !== 1 ? 's' : ''} ago`;
    } else {
        return commentTime.toLocaleDateString();
    }
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

    // Auto-resize textarea
    commentTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Send comment
    commentSendBtn.addEventListener('click', async function() {
        const commentText = commentTextarea.value.trim();
        
        if (!commentText) {
            alert('Please enter a comment');
            return;
        }

        try {
            const response = await fetch(`/api/admin/requisition/${requestId}/comment`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${adminToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    comment: commentText
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to add comment');
            }

            if (result.success) {
                // Clear textarea and reset height
                commentTextarea.value = '';
                commentTextarea.style.height = 'auto';
                
                // Reload comments to show the new one
                loadComments();
                
                // Show success message
                showToast('Comment added successfully', 'success');
            }

        } catch (error) {
            console.error('Error adding comment:', error);
            alert('Failed to add comment: ' + error.message);
        }
    });

    // Allow sending with Enter key (but allow Shift+Enter for new lines)
    commentTextarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            commentSendBtn.click();
        }
    });

     // Simple toast notification function
    window.showToast = function (message, type = 'success', duration = 3000) {
                    const toast = document.createElement('div');

                    // Toast base styles
                    toast.className = `toast align-items-center border-0 position-fixed start-0 mb-2`;
                    toast.style.zIndex = '1100';
                    toast.style.bottom = '0';
                    toast.style.left = '0';
                    toast.style.margin = '1rem';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    toast.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
                    toast.setAttribute('role', 'alert');
                    toast.setAttribute('aria-live', 'assertive');
                    toast.setAttribute('aria-atomic', 'true');

                    // Colors
                    const bgColor = type === 'success' ? '#004183ff' : '#dc3545';
                    toast.style.backgroundColor = bgColor;
                    toast.style.color = '#fff';
                    toast.style.minWidth = '250px';
                    toast.style.borderRadius = '0.3rem';

                    toast.innerHTML = `
                                    <div class="d-flex align-items-center px-3 py-1"> 
                                        <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i>
                                        <div class="toast-body flex-grow-1" style="padding: 0.25rem 0;">${message}</div>
                                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="loading-bar" style="
                                        height: 3px;
                                        background: rgba(255,255,255,0.7);
                                        width: 100%;
                                        transition: width ${duration}ms linear;
                                    "></div>
                                `;

                    document.body.appendChild(toast);

                    // Bootstrap toast instance
                    const bsToast = new bootstrap.Toast(toast, { autohide: false });
                    bsToast.show();

                    // Float up appear animation
                    requestAnimationFrame(() => {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateY(0)';
                    });

                    // Start loading bar animation
                    const loadingBar = toast.querySelector('.loading-bar');
                    requestAnimationFrame(() => {
                        loadingBar.style.width = '0%';
                    });

                    // Remove after duration
                    setTimeout(() => {
                        // Float down disappear animation
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(20px)';

                        setTimeout(() => {
                            bsToast.hide();
                            toast.remove();
                        }, 400);
                    }, duration);
                };


    // Load comments when page loads
    loadComments();


      // Function to show status update modal
    function showStatusUpdateModal(status) {
        const modalContent = document.getElementById('statusModalContent');
        const statusBadge = document.getElementById('statusBadge');
        const statusElement = document.querySelector('#formStatus .badge');
        const currentStatusName = statusElement ? statusElement.textContent.trim() : '';

        if (currentStatusName === 'Pending Approval') {
            alert('Finalize the form first');
            return;
        }

        // Set modal content based on selected status
        switch (status) {
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
            case 'Late':
                modalContent.innerHTML = `
                    <p>This will set the form's status to <strong>Late</strong>.</p>
                    <p class="text-muted small">Note: This may incur additional fees.</p>
                `;
                break;
        }

        statusUpdateModal.show();
    }
            

            // Confirm status update
            document.getElementById('confirmStatusUpdate').addEventListener('click', async function () {
        // Use selectedStatus instead of statusDropdown.value
        if (!selectedStatus) return;
        
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
                const currentRequest = allRequests.find(req => req.request_id == requestId);
                if (!calendarEl) return;

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    initialDate: currentRequest.schedule.start_date,
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
                        const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);

                        if (request) {
                            info.el.style.backgroundColor = request.form_details.status.color;
                            info.el.style.borderColor = request.form_details.status.color;
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
                document.getElementById('modalStatus').innerHTML = `
                <span class="badge" style="background-color: ${request.form_details.status.color}">
                    ${request.form_details.status.name}
                </span>
            `;
                document.getElementById('modalFee').textContent = `₱${request.fees.tentative_fee}`;
                document.getElementById('modalApprovals').textContent = `${request.approval_info.approval_count}`;

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
statusBadge.style.backgroundColor = request.form_details.status.color;


                    // Update user details
                    document.getElementById('formDetails').innerHTML = `
                                                                                                                                                                                                                                                                                                                <p><strong>Name:</strong> ${request.user_details.first_name} ${request.user_details.last_name}</p>
                                                                                                                                                                                                                                                                                                                <p><strong>Email:</strong> ${request.user_details.email}</p>
                                                                                                                                                                                                                                                                                                                <p><strong>User Type:</strong> ${request.user_details.user_type}</p>
                                                                                                                                                                                                                                                                                                                <p><strong>School ID:</strong> ${request.user_details.school_id || 'N/A'}</p>
                                                                                                                                                                                                                                                                                                                <p><strong>Organization:</strong> ${request.user_details.organization_name || 'N/A'}</p>
                                                                                                                                                                                                                                                                                                                <p><strong>Contact:</strong> ${request.user_details.contact_number || 'N/A'}</p>

                                                                                                                                                                                                                                                                                                                <p><strong>Endorser:</strong> ${request.documents.endorser || 'N/A'}</p>
                                                                                                                                                                                                                                                                                                                <p><strong>Date Endorsed:</strong> ${request.documents.date_endorsed || 'N/A'}</p>

                                                                                                                                                                                                                                                                                                                  <p><strong>Purpose:</strong> ${request.form_details.purpose}</p>
              <p><strong>Participants:</strong> ${request.form_details.num_participants}</p>
              <p><strong>Schedule:</strong> ${formatDateTime(request.schedule)}</p>
              <p><strong>Additional Requests:</strong> ${request.form_details.additional_requests || 'None'}</p>                                                                                                                `;


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
                data-id="${f.requested_facility_id}"
                ${f.is_waived ? 'checked' : ''}>
        </div>
        <span class="item-name">${f.name}</span>
    </div>
    <span class="item-price">₱${f.fee}${f.rate_type === 'Per Hour' ? '/hour' : '/event'}</span>
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
                data-id="${e.requested_equipment_id}"
                ${e.is_waived ? 'checked' : ''}>
        </div>
        <span class="item-name">• ${e.name}</span>
    </div>
    <span class="item-price">₱${e.fee}${e.rate_type === 'Per Hour' ? '/hour' : '/event'} × ${e.quantity}</span>
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
<!-- First row: Approvals / Rejections / Late / Penalty -->
<div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
  <div class="text-center small">
    <div class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
      <i class="bi bi-check-circle text-success"></i>
    </div>
    <div class="card-label fw-bold">Approvals</div>
    <div>${request.approval_info.approval_count}</div>
  </div>
  
  <div class="text-center small">
    <div class="bg-danger-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
      <i class="bi bi-x-circle text-danger"></i>
    </div>
    <div class="card-label fw-bold">Rejections</div>
    <div>${request.approval_info.rejection_count}</div>
  </div>
  
  <div class="text-center small">
    <div class="bg-warning-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
      <i class="bi bi-clock-history text-warning"></i>
    </div>
    <div class="card-label fw-bold">Is Late</div>
    <div>${is_late ? 'Yes' : 'No'}</div>
  </div>
  
  <div class="text-center small">
    <div class="bg-info-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
      <i class="bi bi-cash-coin text-info"></i>
    </div>
    <div class="card-label fw-bold">Late Penalty</div>
    <div>${request.fees.late_penalty_fee ? `₱${request.fees.late_penalty_fee}` : 'N/A'}</div>
  </div>
</div>


<div class="d-flex flex-wrap justify-content-between gap-3">
  <div class="text-center small">
    <div class="fw-bold">Formal Letter</div>
    <div>
      ${request.documents.formal_letter.url ?
        `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
          data-document-url="${request.documents.formal_letter.url}" data-document-title="Formal Letter">
            View Document
         </button>` :
        'Not uploaded'}
    </div>
  </div>

  <div class="text-center small">
    <div class="fw-bold">Facility Setup</div>
    <div>
      ${request.documents.facility_layout.url ?
        `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
          data-document-url="${request.documents.facility_layout.url}" data-document-title="Facility Setup">
            View Document
         </button>` :
        'Not uploaded'}
    </div>
  </div>

  <div class="text-center small">
    <div class="fw-bold">Proof of Payment</div>
    <div>
      ${request.documents.proof_of_payment.url ?
        `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
          data-document-url="${request.documents.proof_of_payment.url}" data-document-title="Proof of Payment">
            View Document
         </button>` :
        'Not uploaded'}
    </div>
  </div>

  <div class="text-center small">
    <div class="fw-bold">Official Receipt</div>
    <div>
      ${request.documents.official_receipt.url ?
        `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
          data-document-url="${request.documents.official_receipt.url}" data-document-title="Official Receipt">
            View Document
         </button>` :
        'Not uploaded'}
    </div>
  </div>
</div>


`;






                    // After successful data fetch and updates, show content and hide loading state
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('contentState').style.display = 'block';

                    loadFees();
                    updateBaseFees(request.requested_items, request.schedule);
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

            // Add this function to calculate rental duration
function calculateRentalDuration(startDate, startTime, endDate, endTime) {
    const start = new Date(`${startDate}T${startTime}`);
    const end = new Date(`${endDate}T${endTime}`);
    
    const durationInHours = Math.round((end - start) / (1000 * 60 * 60) * 100) / 100;
    const formattedStart = start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const formattedEnd = end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    return {
        hours: durationInHours,
        formatted: `${durationInHours} hours (${formattedStart} – ${formattedEnd})`
    };
}

          // Function to update base fees display
// Function to update base fees display
function updateBaseFees(requestedItems, schedule) {
    const facilitiesContainer = document.getElementById('facilitiesFees');
    const equipmentContainer = document.getElementById('equipmentFees');
    
    // Clear existing content
    facilitiesContainer.innerHTML = '';
    equipmentContainer.innerHTML = '';

    // Calculate rental duration for hourly rate calculations
    const startDateTime = new Date(`${schedule.start_date}T${schedule.start_time}`);
    const endDateTime = new Date(`${schedule.end_date}T${schedule.end_time}`);
    const durationHours = Math.max(0, (endDateTime - startDateTime) / (1000 * 60 * 60));

    let totalBaseFees = 0;

    // Add facilities with proper rate type logic
    if (requestedItems.facilities && requestedItems.facilities.length > 0) {
        requestedItems.facilities.forEach(facility => {
            if (facility.is_waived) return; // Skip waived items
            
            const facilityElement = document.createElement('div');
            facilityElement.className = 'fee-item d-flex justify-content-between mb-2';
            
            let feeAmount = parseFloat(facility.fee);
            let itemTotal = 0;
            let rateDescription = '';
            
            if (facility.rate_type === 'Per Hour' && durationHours > 0) {
                itemTotal = feeAmount * durationHours;
                rateDescription = `₱${feeAmount.toLocaleString()}/hr × ${durationHours.toFixed(1)} hrs`;
                totalBaseFees += itemTotal;
            } else {
                itemTotal = feeAmount;
                rateDescription = `₱${feeAmount.toLocaleString()}/event`;
                totalBaseFees += itemTotal;
            }
            
            facilityElement.innerHTML = `
                <span class="item-name">
                    ${facility.name}
                </span>
                <div class="text-end">
                    <small>${rateDescription}</small>
                    <div><strong>₱${itemTotal.toLocaleString()}</strong></div>
                </div>
            `;
            facilitiesContainer.appendChild(facilityElement);
        });
    } else {
        facilitiesContainer.innerHTML = '<p class="text-muted">No facilities requested</p>';
    }

    // Add equipment with proper rate type logic
    if (requestedItems.equipment && requestedItems.equipment.length > 0) {
        requestedItems.equipment.forEach(equipment => {
            if (equipment.is_waived) return; // Skip waived items
            
            const equipmentElement = document.createElement('div');
            equipmentElement.className = 'fee-item d-flex justify-content-between mb-2';
            
            let unitFee = parseFloat(equipment.fee);
            const quantity = equipment.quantity || 1;
            let itemTotal = 0;
            let rateDescription = '';
            
            if (equipment.rate_type === 'Per Hour' && durationHours > 0) {
                itemTotal = (unitFee * durationHours) * quantity;
                rateDescription = `₱${unitFee.toLocaleString()}/hr × ${durationHours.toFixed(1)} hrs × ${quantity}`;
                totalBaseFees += itemTotal;
            } else {
                itemTotal = unitFee * quantity;
                rateDescription = `₱${unitFee.toLocaleString()}/event × ${quantity}`;
                totalBaseFees += itemTotal;
            }
            
            equipmentElement.innerHTML = `
                <span class="item-name">
                    ${equipment.name} ${quantity > 1 ? `(×${quantity})` : ''}
                </span>
                <div class="text-end">
                    <small>${rateDescription}</small>
                    <div><strong>₱${itemTotal.toLocaleString()}</strong></div>
                </div>
            `;
            equipmentContainer.appendChild(equipmentElement);
        });
    } else {
        equipmentContainer.innerHTML = '<p class="text-muted">No equipment requested</p>';
    }

    // Update the total base fees display if you have one
    console.log('Total Base Fees:', totalBaseFees);
    console.log('Facilities:', requestedItems.facilities);
console.log('Equipment:', requestedItems.equipment);
console.log('Rate types - Facilities:', requestedItems.facilities.map(f => f.rate_type));
console.log('Rate types - Equipment:', requestedItems.equipment.map(e => e.rate_type));
}

            function formatDateTime(schedule) {
                const startDate = new Date(schedule.start_date + 'T' + schedule.start_time);
                const endDate = new Date(schedule.end_date + 'T' + schedule.end_time);
                return `${startDate.toLocaleString()} to ${endDate.toLocaleString()}`;
            }

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
                                                                                                                                                                                                                                                                                <button class="btn btn-sm remove-btn text-secondary p-0 border-0">
    <i class="bi bi-x-lg"></i>
</button>

                                                                                                                                                                                                                                                                            
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
            // document.getElementById('finalizeBtn').addEventListener('click', function () {
            //     finalizeModal.show();
            // });

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