@extends('layouts.admin')
@section('title', 'Review Request')
@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/request-view.css') }}">

    <style>
        .request-code {
            background-color: #f4f4f4;
            /* subtle background */
            color: #292929ff;
            /* Bootstrap primary color */
            font-family: "Courier New", monospace;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            /* vertically center with text */
            line-height: 1.2;
        }

        .fc .fc-toolbar-chunk .fc-button:focus,
        .fc .fc-toolbar-chunk .fc-button:active {
            outline: none !important;
            box-shadow: none !important;
        }

        /* FullCalendar Toolbar Buttons */
        .fc .fc-toolbar-chunk .fc-button {
            background-color: #ffffff !important;
            /* White background */
            color: #6c757d !important;
            /* Gray text */
            border: none !important;
            /* No border */
            font-weight: 500;
            border-radius: 6px !important;
        }

        /* Hover state */
        .fc .fc-toolbar-chunk .fc-button:hover {
            background-color: #f8f9fa !important;
            /* Slightly off-white hover */
            color: #495057 !important;
            /* Darker gray text on hover */
            border: none !important;
        }

        /* Active/Pressed state */
        .fc .fc-toolbar-chunk .fc-button.fc-button-active {
            background-color: #4272b1ff !important;
            color: #ffffffff !important;
            border: none !important;
        }

        .fc .fc-today-button {
            text-transform: capitalize !important;
        }
    </style>

    <!-- Main Content -->
    <main id="main" style="padding-top: 50px;">

        <div class="card bg-transparent shadow-none pt-0"
            style="border: none !important; background-color: transparent !important">
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

                    <!-- Request Status + Action Panel -->
                    <div class="row g-2 mb-2">
                        <!-- Status Summary -->
                        <div class="col-md-9">
                            <div class="card h-100">
                                <div
                                    class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center text-primary fw-bold">
                                        Currently Reviewing:
                                        <code id="requestIdTitle" class="request-code ms-1"></code>
                                    </div>
                                    <span id="statusBadge" class="badge ms-1"></span>
                                </div>


                                <div class="card-body d-flex flex-wrap gap-2 justify-content-start">
                                    <!-- Approvals -->
                                    <div class="px-3 py-1 bg-success-subtle text-success rounded-pill d-inline-flex align-items-center hover-pointer"
                                        role="button" data-bs-toggle="modal" data-bs-target="#approvalsModal">
                                        <i class="fa fa-thumbs-up me-1"></i>
                                        <span class="fw-bold me-2" id="approvalsCount">0</span>
                                    </div>

                                    <!-- Rejections -->
                                    <div class="px-3 py-1 bg-danger-subtle text-danger rounded-pill d-inline-flex align-items-center hover-pointer"
                                        role="button" data-bs-toggle="modal" data-bs-target="#rejectionsModal">
                                        <i class="fa fa-thumbs-down me-1"></i>
                                        <span class="fw-bold me-1" id="rejectionsCount">0</span>
                                    </div>

                                    <!-- Bootstrap Modals -->
                                    <div class="modal fade" id="approvalsModal" tabindex="-1"
                                        aria-labelledby="approvalsModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="approvalsModalLabel">Approvals</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Empty content for now -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="rejectionsModal" tabindex="-1"
                                        aria-labelledby="rejectionsModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectionsModalLabel">Rejections</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Empty content for now -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Optional CSS for hover effect -->
                                    <style>
                                        .hover-pointer:hover {
                                            cursor: pointer;
                                            background-color: rgba(0, 0, 0, 0.05);
                                            /* subtle hover effect */
                                        }
                                    </style>


                                    <div
                                        class="px-3 py-1 bg-secondary-subtle text-dark rounded-pill d-inline-flex align-items-center">
                                        <i class="fa fa-hourglass-end me-1"></i>
                                        <span class="me-2" id="isLateStatus">Not Late</span>
                                    </div>

                                    <!-- Total Fee Pill -->
                                    <div
                                        class="px-3 py-1 bg-secondary-subtle text-dark rounded-pill d-inline-flex align-items-center">
                                        <i class="fa fa-money-bill me-2"></i>
                                        <span class="fw-bold me-1" id="totalApprovedFee">0</span>
                                    </div>

                                    <!-- Comments Pill -->
                                    <div
                                        class="px-3 py-1 bg-secondary-subtle text-dark rounded-pill d-inline-flex align-items-center">
                                        <i class="fa fa-commenting me-2"></i>
                                        <span class="fw-bold me-1" id="commentCount">0</span>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- Action Panel -->
                        <div class="col-md-3">
                            <div class="card h-100 d-flex flex-column justify-content-center align-items-center p-3">

                                <button class="btn btn-success w-100 mb-2" id="approveBtn">
                                    <i class="bi bi-hand-thumbs-up me-1"></i> Approve
                                </button>

                                <button class="btn btn-danger w-100 mb-2" id="rejectBtn">
                                    <i class="bi bi-hand-thumbs-down me-1"></i> Reject
                                </button>

                                <!-- Finalize Button (Hidden by default, shown for Head Admin) -->
                                <button class="btn btn-primary w-100 mb-2" id="finalizeBtn" style="display: none;">
                                    <i class="bi bi-check-circle me-1"></i> Finalize
                                </button>

                                <!-- More Actions Dropdown -->
                                <div class="dropdown w-100" id="moreActionsDropdown">
                                    <button class="btn btn-secondary bg-secondary-subtle text-dark w-100 dropdown-toggle"
                                        type="button" id="moreActionsBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots me-1"></i> More
                                    </button>

                                    <ul class="dropdown-menu" style="min-width: 100px;" aria-labelledby="moreActionsBtn">
                                        <li>
                                            <a class="dropdown-item text-start w-100 status-option" id="statusScheduled"
                                                data-value="Scheduled" href="#">
                                                <i class="bi bi-calendar-event me-2"></i> Mark Scheduled
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-start w-100 status-option" id="statusOngoing"
                                                data-value="Ongoing" href="#">
                                                <i class="bi bi-play-circle me-2"></i> Mark Ongoing
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-start w-100 status-option" id="statusLate"
                                                data-value="Late" href="#">
                                                <i class="bi bi-exclamation-circle me-2"></i> Mark Late
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-start w-100" id="closeForm" href="#">
                                                <i class="bi bi-x-circle me-2"></i> Close Form
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row g-2">
                        <!-- Left column: Contact Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div
                                    class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Contact Information</h5>
                                </div>
                                <div class="card-body" style="padding:0;">
                                    <div id="formDetails"></div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="card documents-card h-100">
                                <div
                                    class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Documents</h5>
                                </div>
                                <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                    <div class="row g-1 justify-content-center w-100 flex-nowrap">
                                        <!-- Formal Letter Card -->
                                        <div class="col-auto">
                                            <div class="card text-center">
                                                <div class="card-body p-2 d-flex flex-column justify-content-center">
                                                    <i id="formalLetterIcon"
                                                        class="fas fa-file-alt fa-2x text-muted mb-1"></i>
                                                    <small class="text-muted mb-1">Formal Letter <br>(Required)</small>
                                                    <div id="formalLetterDocument"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Facility Setup Card -->
                                        <div class="col-auto">
                                            <div class="card text-center">
                                                <div class="card-body p-2 d-flex flex-column justify-content-center">
                                                    <i id="facilityLayoutIcon"
                                                        class="fas fa-map-marked-alt fa-2x text-muted mb-1"></i>
                                                    <small class="text-muted mb-1">Facility<br> Layout</small>
                                                    <div id="facilityLayoutDocument"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Proof of Payment Card -->
                                        <div class="col-auto">
                                            <div class="card text-center">
                                                <div class="card-body p-2 d-flex flex-column justify-content-center">
                                                    <i id="proofOfPaymentIcon"
                                                        class="fas fa-receipt fa-2x text-muted mb-1"></i>
                                                    <small class="text-muted mb-1">Payment confirmation</small>
                                                    <div id="proofOfPaymentDocument"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Official Receipt Card -->
                                        <!-- Official Receipt Card -->
                                        <div class="col-auto">
                                            <div class="card text-center">
                                                <div class="card-body p-2 d-flex flex-column justify-content-center">
                                                    <i id="officialReceiptIcon"
                                                        class="fas fa-file-invoice-dollar fa-2x text-muted mb-1"></i>
                                                    <small class="text-muted mb-1">Transaction record</small>
                                                    <div id="officialReceiptDocument">
                                                        <!-- Dynamic content will be inserted here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end row -->
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-stretch g-2 mt-1">
                            <!-- Left column: Merged Card -->
                            <div class="col-lg-5 d-flex">
                                <div class="card flex-fill d-flex flex-column">
                                    <div
                                        class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Booking Details</h5>
                                    </div>
                                    <!-- Scrollable body -->
                                    <div class="card-body flex-fill overflow-auto">
                                        <!-- Event Details Section -->
                                        <div id="eventDetails" class="mb-3"></div>

                                        <!-- Requested Items Section -->
                                        <h6 class="fw-bold text-center mb-3"
                                            style="font-size:0.9rem; display:flex; align-items:center;">
                                            <span style="flex:1; height:1px; background:#ccc; margin-right:0.5rem;"></span>
                                            Requested Items
                                            <span style="flex:1; height:1px; background:#ccc; margin-left:0.5rem;"></span>
                                        </h6>

                                        <div id="requestedItems" style="font-size:0.85rem;"></div>
                                    </div>
                                </div>
                            </div>


                            <!-- Right column: Calendar -->
                            <div class="col-lg-7 d-flex">
                                <div class="card flex-fill d-flex flex-column">
                                    <div class="card-body d-flex flex-column">
                                        <div class="calendar-container flex-fill">
                                            <div id="calendar" style="height:100%;"></div>
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
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="statusModalContent">
                                            <!-- Content will be dynamically populated -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="confirmStatusUpdate">
                                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                                aria-hidden="true"></span>
                                            Confirm Change
                                        </button>
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
                                                    <option value="additional">Additional Fee
                                                    </option>
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

                        <div class="row g-2 mt-1 align-items-stretch">
                            <!-- Left Column: Activity Timeline -->
                            <div class="col-lg-5 d-flex">
                                <div class="card flex-fill d-flex flex-column" style="height: 100%;">

                                    <div
                                        class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Activity Timeline</h5>
                                        <select id="activityFilter" class="form-select form-select-sm w-auto">
                                            <option value="all">Show All</option>
                                            <option value="comment">Comments</option>
                                            <option value="fee">Added Fees</option>
                                        </select>
                                    </div>

                                    <!-- Scrollable Body -->
                                    <div class="card-body flex-grow-1 overflow-auto p-3" style="max-height: 400px;">
                                        <div id="formRemarks">
                                            <div class="comment-loading">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading activity...</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="additionalFees" style="display: none;"></div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="card-footer bg-white border-top mt-auto d-flex align-items-center justify-content-between"
                                        style="height: 70px; flex-shrink: 0; padding: 0 1rem;">
                                        <div class="input-group align-items-center w-100" style="gap: 0.5rem;">
                                            <textarea class="form-control" rows="1" placeholder="Leave a comment..."
                                                id="commentTextarea"
                                                style="resize: none; border-radius: 20px; height: 36px;"></textarea>
                                            <button class="btn btn-primary rounded-circle" type="button" id="sendCommentBtn"
                                                style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <!-- Right Column: Fee Breakdown -->
                            <div class="col-lg-7 d-flex">
                                <div class="card flex-fill d-flex flex-column">
                                    <div
                                        class="card-header bg-white text-dark d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Fee Breakdown</h5>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="waiveAllSwitch">
                                            <label class="form-check-label" for="waiveAllSwitch">Waive All Fees</label>
                                        </div>
                                    </div>

                                    <div class="card-body flex-fill overflow-auto">
                                        <div class="mb-3">
                                            <h6 class="fw-bold mb-2">Base Fees</h6>
                                            <div id="baseFeesContainer">
                                                <div id="facilitiesFees"></div>
                                                <div id="equipmentFees"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold mb-0">Miscellaneous Fees</h6>
                                                <button id="addFeeBtn" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-plus-circle"></i> Add Fee
                                                </button>
                                            </div>
                                            <div id="additionalFeesContainer"></div>
                                        </div>
                                    </div>

                                    <div
                                        class="card-footer bg-white d-flex justify-content-between align-items-center fw-bold p-3 border-top">
                                        <span>Total Approved Fee:</span>
                                        <span id="feeBreakdownTotal">₱0.00</span>
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
                        <div>
                            <br><br><br><br><br>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Finalize Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="alert alert-success mb-0">
                                            <i class="bi bi-check-circle me-2"></i>
                                            <strong>Approvals:</strong>
                                            <span id="currentApprovalCount" class="fw-bold"></span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="alert alert-danger mb-0">
                                            <i class="bi bi-x-circle me-2"></i>
                                            <strong>Rejections:</strong>
                                            <span id="currentRejectionCount" class="fw-bold"></span>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="text-center">
                                    <h6 class="fw-bold mb-3">Are you sure? This action cannot be undone.</h6>
                                    <p class="text-muted small">Finalizing this request will change its status from "Pending
                                        Approval" to "Awaiting Payment". This action cannot be undone and will prevent
                                        further approvals/rejections for signatories.</p>
                                </div>


                                <div class="mb-3">
                                    <label for="calendarTitle" class="form-label">Event Title</label>
                                    <input type="text" class="form-control" id="calendarTitle"
                                        placeholder="Enter calendar event title" required maxlength="50"
                                        value="{{ $request->form_details->purpose ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label for="calendarDescription" class="form-label">Event Description</label>
                                    <textarea class="form-control" id="calendarDescription" rows="3"
                                        placeholder="Enter calendar event description" required maxlength="100"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmFinalize">
                                    <span class="spinner-border spinner-border-sm me-1 align-middle d-none" role="status"
                                        aria-hidden="true"></span>
                                    Finalize Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Event Details Modal -->
                <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 800px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="eventModalBody">

                                <div class="card border-0 shadow-none mb-3 p-3">
                                    <table class="table table-bordered mb-0 w-100"
                                        style="table-layout: fixed; border: 1px solid #dee2e6;">
                                        <thead>
                                            <tr>
                                                <th class="bg-light p-2" style="width: 50%; border: 1px solid #dee2e6;">
                                                    Event Information</th>
                                                <th class="bg-light p-2" style="width: 50%; border: 1px solid #dee2e6;">
                                                    Requested Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="border: 1px solid #dee2e6; padding: 0;">
                                                    <table class="table mb-0 w-100" style="border-collapse: collapse;">
                                                        <tbody>
                                                            <tr>
                                                                <th class="bg-light text-nowrap p-2"
                                                                    style="width: 40%; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                                                                    Requester</th>
                                                                <td id="modalRequester" class="p-2"
                                                                    style="border-bottom: 1px solid #dee2e6;"></td>
                                                            </tr>
                                                            <tr>
                                                                <th class="bg-light text-nowrap p-2"
                                                                    style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                                                                    Purpose</th>
                                                                <td id="modalPurpose" class="p-2"
                                                                    style="border-bottom: 1px solid #dee2e6;"></td>
                                                            </tr>
                                                            <tr>
                                                                <th class="bg-light text-nowrap p-2"
                                                                    style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                                                                    Participants</th>
                                                                <td id="modalParticipants" class="p-2"
                                                                    style="border-bottom: 1px solid #dee2e6;"></td>
                                                            </tr>
                                                            <tr>
                                                                <th class="bg-light text-nowrap p-2"
                                                                    style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                                                                    Status</th>
                                                                <td id="modalStatus" class="p-2"
                                                                    style="border-bottom: 1px solid #dee2e6;"></td>
                                                            </tr>
                                                            <tr>
                                                                <th class="bg-light text-nowrap p-2"
                                                                    style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                                                                    Tentative Fee</th>
                                                                <td id="modalFee" class="p-2"
                                                                    style="border-bottom: 1px solid #dee2e6;"></td>
                                                            </tr>
                                                            <tr>
                                                                <th class="bg-light text-nowrap p-2"
                                                                    style="border-right: 1px solid #dee2e6;">Approvals</th>
                                                                <td id="modalApprovals" class="p-2"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="border: 1px solid #dee2e6; vertical-align: top; padding: 0;">
                                                    <div id="modalItems" class="p-3" style="min-height: 100%;">
                                                        <!-- JS will insert items here -->
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                <!-- Close Form Confirmation Modal -->
                <div class="modal fade" id="closeFormModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Close Form</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center">
                                    <i class="bi bi-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <p>Are you sure you want to close this form?</p>
                                    <p class="text-muted small">
                                        This will mark the form as completed. This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmCloseForm">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    Confirm Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="markScheduledModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Mark as Scheduled</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to mark this request as scheduled? This will generate an official
                                    receipt.</p>

                                <div class="mb-3">
                                    <label for="officialReceiptNum" class="form-label">Official Receipt Number *</label>
                                    <input type="text" class="form-control" id="officialReceiptNum"
                                        placeholder="Enter official receipt number" required>
                                    <div class="form-text">This will be the unique identifier for the official receipt.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="scheduledCalendarTitle" class="form-label">Calendar Event Title</label>
                                    <input type="text" class="form-control" id="scheduledCalendarTitle"
                                        placeholder="Enter calendar event title" maxlength="50">
                                </div>

                                <div class="mb-3">
                                    <label for="scheduledCalendarDescription" class="form-label">Calendar Event
                                        Description</label>
                                    <textarea class="form-control" id="scheduledCalendarDescription" rows="3"
                                        placeholder="Enter calendar event description" maxlength="100"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmMarkScheduled">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    Confirm & Generate Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Back to Top Button -->
                <button class="back-to-top" id="backToTop" title="Back to Top">
                    <i class="bi bi-arrow-up"></i>
                </button>


    </main>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // Back to Top Button
            const backToTopButton = document.getElementById('backToTop');

            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.add('show');
                } else {
                    backToTopButton.classList.remove('show');
                }
            });

            backToTopButton.addEventListener('click', function () {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            const markScheduledModal = new bootstrap.Modal(document.getElementById('markScheduledModal'));
            const closeFormModal = new bootstrap.Modal(document.getElementById('closeFormModal'));
            const commentsContainer = document.getElementById('formRemarks');
            const commentTextarea = document.querySelector('.card-footer textarea');
            const commentSendBtn = document.querySelector('.card-footer button');
            const requestId = window.location.pathname.split('/').pop();
            const adminToken = localStorage.getItem('adminToken');
            let allRequests = [];
            let currentComments = [];
            let currentFees = [];


            // Initialize Bootstrap modal
            const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            const feeModal = new bootstrap.Modal(document.getElementById('feeModal'));

            // Form Action Modals
            const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
            const finalizeModal = new bootstrap.Modal(document.getElementById('finalizeModal'));

            // Function to handle mark scheduled action
            function handleMarkScheduled() {
                markScheduledModal.show();
            }


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
            const moreDropdown = document.getElementById('moreDropdown');
            const statusOptions = document.querySelectorAll('.status-option');
            const updateStatusBtn = document.getElementById('updateStatusBtn');
            const statusUpdateModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
            let selectedStatus = '';






            // Function to check admin role and update UI accordingly
            async function checkAdminRoleAndUpdateUI() {
                try {
                    const adminToken = localStorage.getItem('adminToken');
                    const response = await fetch('http://127.0.0.1:8000/api/admin/profile', {
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        console.error('Failed to fetch admin profile. Status:', response.status);
                        return;
                    }

                    const adminData = await response.json();
                    console.log('Admin role data:', {
                        adminId: adminData.admin_id,
                        roleTitle: adminData.role?.role_title,
                        departments: adminData.departments?.map(d => d.department_name)
                    });

                    // Define role checks based on admin data
                    const isHeadAdmin = adminData.role?.role_title === 'Head Admin';
                    const isApprovingOfficer = adminData.role?.role_title === 'Approving Officer';
                    const isVPA = adminData.role?.role_title === 'Vice President for Administration';

                    const approveBtn = document.getElementById('approveBtn');
                    const rejectBtn = document.getElementById('rejectBtn');
                    const finalizeBtn = document.getElementById('finalizeBtn');
                    const moreActionsDropdown = document.getElementById('moreActionsDropdown');
                    const moreActionsBtn = document.getElementById('moreActionsBtn');
                    const actionPanel = document.querySelector('.col-md-3 .card');

                    // First, check if this admin has already taken action on this request
                    const hasTakenAction = await checkIfAdminHasTakenAction(adminData.admin_id);

                    console.log('UI Update Decision:', {
                        adminId: adminData.admin_id,
                        hasTakenAction: hasTakenAction,
                        isHeadAdmin: isHeadAdmin,
                        isApprovingOfficer: isApprovingOfficer,
                        isVPA: isVPA
                    });

                    // Remove any existing action taken message or dynamic buttons
                    const existingMessage = actionPanel?.querySelector('.action-taken-message');
                    if (existingMessage) {
                        existingMessage.remove();
                    }

                    // Get current request status for Head Admin logic
                    const currentRequest = allRequests.find(req => req.request_id == requestId);
                    const currentStatusId = currentRequest ? currentRequest.form_details.status.id : null;
                    const currentStatusName = currentRequest ? currentRequest.form_details.status.name : 'Unknown';

                    // Reset all elements to default state first
                    if (moreActionsDropdown) moreActionsDropdown.style.display = 'block';
                    if (moreActionsBtn) moreActionsBtn.style.display = 'block';
                    if (approveBtn) {
                        approveBtn.style.display = 'block';
                        approveBtn.disabled = false;
                        approveBtn.classList.remove('disabled');
                    }
                    if (rejectBtn) {
                        rejectBtn.style.display = 'block';
                        rejectBtn.disabled = false;
                        rejectBtn.classList.remove('disabled');
                    }
                    if (finalizeBtn) {
                        finalizeBtn.style.display = 'none';
                        finalizeBtn.disabled = false;
                        finalizeBtn.classList.remove('disabled');
                    }

                    // Reset dropdown items visibility
                    const markScheduledOption = document.getElementById('statusScheduled');
                    const markOngoingOption = document.getElementById('statusOngoing');
                    const markLateOption = document.getElementById('statusLate');
                    if (markScheduledOption) markScheduledOption.style.display = 'block';
                    if (markOngoingOption) markOngoingOption.style.display = 'block';
                    if (markLateOption) markLateOption.style.display = 'block';

                    // Remove any dynamically created buttons
                    const dynamicMarkScheduledBtn = document.getElementById('markScheduledBtn');
                    const dynamicMarkOngoingBtn = document.getElementById('markOngoingBtn');
                    const dynamicUnmarkLateBtn = document.getElementById('unmarkLateBtn');
                    const dynamicCloseFormBtn = document.getElementById('closeFormBtn');
                    if (dynamicMarkScheduledBtn) dynamicMarkScheduledBtn.remove();
                    if (dynamicMarkOngoingBtn) dynamicMarkOngoingBtn.remove();
                    if (dynamicUnmarkLateBtn) dynamicUnmarkLateBtn.remove();
                    if (dynamicCloseFormBtn) dynamicCloseFormBtn.remove();

                    // If admin has already taken action, show message and hide appropriate buttons
                    if (hasTakenAction) {
                        console.log('Hiding buttons - admin has already taken action');

                        // For Approving Officers and VPA, hide approve/reject buttons and show message
                        if (isApprovingOfficer || isVPA) {
                            if (approveBtn) approveBtn.style.display = 'none';
                            if (rejectBtn) rejectBtn.style.display = 'none';
                            if (moreActionsDropdown) moreActionsDropdown.style.display = 'none';
                            if (moreActionsBtn) moreActionsBtn.style.display = 'none';

                            // Show action taken message
                            if (actionPanel) {
                                const actionMessage = document.createElement('div');
                                actionMessage.className = 'action-taken-message text-center p-3';
                                actionMessage.innerHTML = `
                                                <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-2"></i>
                                                <p class="mb-0 small text-muted">You have already taken action for this request.</p>
                                            `;
                                actionPanel.appendChild(actionMessage);
                            }
                        }
                        // For Head Admin who has taken action, still allow status management but not approve/reject
                        else if (isHeadAdmin) {
                            if (approveBtn) approveBtn.style.display = 'none';
                            if (rejectBtn) rejectBtn.style.display = 'none';
                            // Head Admin can still use finalize and more actions even if they've taken action
                        }

                        return; // Exit early since admin has taken action
                    }

                    // Apply role-based rules if admin hasn't taken action yet
                    if (isApprovingOfficer || isVPA) {
                        // Hide More dropdown for Approving Officer and VPA
                        if (moreActionsDropdown) moreActionsDropdown.style.display = 'none';
                        if (moreActionsBtn) moreActionsBtn.style.display = 'none';
                        console.log('Hiding More dropdown for Approving Officer/VPA');
                    }

                    if (isHeadAdmin) {
                        // Hide Approve/Reject buttons for Head Admin
                        if (approveBtn) approveBtn.style.display = 'none';
                        if (rejectBtn) rejectBtn.style.display = 'none';

                        // Show Finalize button for Head Admin (unless status requires changes)
                        if (finalizeBtn) finalizeBtn.style.display = 'block';

                        // Ensure More dropdown is visible for Head Admin (unless status requires changes)
                        if (moreActionsDropdown) moreActionsDropdown.style.display = 'block';
                        if (moreActionsBtn) moreActionsBtn.style.display = 'block';
                        console.log('Showing Finalize button and More dropdown for Head Admin');

                        // Apply Head Admin status-based UI updates
                        console.log('Applying Head Admin status-based UI for status:', {
                            statusId: currentStatusId,
                            statusName: currentStatusName
                        });

                        // Use the actual status IDs from your system
                        switch (currentStatusId) {
                            case 1: // Pending Approval (status ID 1)
                                console.log('Status: Pending Approval - Replacing More dropdown with Close Form button');

                                // Hide More dropdown
                                if (moreActionsDropdown) {
                                    moreActionsDropdown.style.display = 'none';
                                }

                                // Create Close Form button
                                const closeFormBtnPending = document.createElement('button');
                                closeFormBtnPending.id = 'closeFormBtn';
                                closeFormBtnPending.className = 'btn btn-light-danger w-100 mb-2';
                                closeFormBtnPending.innerHTML = '<i class="bi bi-x-circle me-1"></i> Close Form';
                                closeFormBtnPending.addEventListener('click', function () {
                                    closeForm();
                                });

                                // Add button to action panel
                                if (actionPanel) {
                                    actionPanel.appendChild(closeFormBtnPending);
                                }
                                break;

                            case 2: // Awaiting Payment (status ID 2)
                                console.log('Status: Awaiting Payment - Replacing Finalize with Mark Scheduled, replacing More with Close Form');

                                // Replace Finalize button with Mark Scheduled
                                if (finalizeBtn) {
                                    finalizeBtn.style.display = 'none';
                                }

                                // Replace More dropdown with Close Form button
                                if (moreActionsDropdown) {
                                    moreActionsDropdown.style.display = 'none';
                                }

                                // Create Mark Scheduled button
                                const markScheduledBtn = document.createElement('button');
                                markScheduledBtn.id = 'markScheduledBtn';
                                markScheduledBtn.className = 'btn btn-primary w-100 mb-2';
                                markScheduledBtn.innerHTML = '<i class="bi bi-calendar-event me-1"></i> Mark Scheduled';
                                markScheduledBtn.addEventListener('click', function () {
                                    handleMarkScheduled(); // Use the custom handler instead of handleStatusAction
                                });

                                // Create Close Form button
                                const closeFormBtnAwaiting = document.createElement('button');
                                closeFormBtnAwaiting.id = 'closeFormBtn';
                                closeFormBtnAwaiting.className = 'btn btn-light-danger w-100 mb-2';
                                closeFormBtnAwaiting.innerHTML = '<i class="bi bi-x-circle me-1"></i> Close Form';
                                closeFormBtnAwaiting.addEventListener('click', function () {
                                    closeForm();
                                });

                                // Add buttons to action panel
                                if (actionPanel) {
                                    actionPanel.appendChild(markScheduledBtn);
                                    actionPanel.appendChild(closeFormBtnAwaiting);
                                }
                                break;

                            case 3: // Scheduled (status ID 3)
                                console.log('Status: Scheduled - Replacing Finalize with Mark Ongoing, replacing More with Close Form');

                                // Replace Finalize button with Mark Ongoing
                                if (finalizeBtn) {
                                    finalizeBtn.style.display = 'none';
                                }

                                // Replace More dropdown with Close Form button
                                if (moreActionsDropdown) {
                                    moreActionsDropdown.style.display = 'none';
                                }

                                // Create Mark Ongoing button
                                const markOngoingBtn = document.createElement('button');
                                markOngoingBtn.id = 'markOngoingBtn';
                                markOngoingBtn.className = 'btn btn-primary w-100 mb-2';
                                markOngoingBtn.innerHTML = '<i class="bi bi-play-circle me-1"></i> Mark Ongoing';
                                markOngoingBtn.addEventListener('click', function () {
                                    handleStatusAction('Ongoing');
                                });

                                // Create Close Form button
                                const closeFormBtnScheduled = document.createElement('button');
                                closeFormBtnScheduled.id = 'closeFormBtn';
                                closeFormBtnScheduled.className = 'btn btn-light-danger w-100 mb-2';
                                closeFormBtnScheduled.innerHTML = '<i class="bi bi-x-circle me-1"></i> Close Form';
                                closeFormBtnScheduled.addEventListener('click', function () {
                                    closeForm();
                                });

                                // Add buttons to action panel
                                if (actionPanel) {
                                    actionPanel.appendChild(markOngoingBtn);
                                    actionPanel.appendChild(closeFormBtnScheduled);
                                }
                                break;

                            case 4: // Ongoing (status ID 4)
                                console.log('Status: Ongoing - Replacing Finalize with Mark Late, replacing More with Close Form');

                                // Hide existing buttons
                                if (finalizeBtn) {
                                    finalizeBtn.style.display = 'none';
                                }
                                if (moreActionsDropdown) {
                                    moreActionsDropdown.style.display = 'none';
                                }

                                // Only create Mark Late button if it doesn't exist
                                if (!document.getElementById('markLateBtn')) {
                                    const markLateBtn = document.createElement('button');
                                    markLateBtn.id = 'markLateBtn';
                                    markLateBtn.className = 'btn btn-primary w-100 mb-2';
                                    markLateBtn.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Mark Late';
                                    markLateBtn.addEventListener('click', function () {
                                        handleStatusAction('Late');
                                    });
                                    actionPanel.appendChild(markLateBtn);
                                }

                                // Only create Close Form button if it doesn't exist
                                if (!document.getElementById('closeFormBtn')) {
                                    const closeFormBtnOngoing = document.createElement('button');
                                    closeFormBtnOngoing.id = 'closeFormBtn';
                                    closeFormBtnOngoing.className = 'btn btn-light-danger w-100 mb-2';
                                    closeFormBtnOngoing.innerHTML = '<i class="bi bi-x-circle me-1"></i> Close Form';
                                    closeFormBtnOngoing.addEventListener('click', function () {
                                        closeForm();
                                    });
                                    actionPanel.appendChild(closeFormBtnOngoing);
                                }
                                break;


                            case 5: // Late (status ID 5)
                                console.log('Status: Late - Replacing Finalize with Unmark Late, replacing More with Close Form');

                                // Replace Finalize button with Unmark Late
                                if (finalizeBtn) {
                                    finalizeBtn.style.display = 'none';
                                }

                                // Replace More dropdown with Close Form button
                                if (moreActionsDropdown) {
                                    moreActionsDropdown.style.display = 'none';
                                }

                                // Create Unmark Late button
                                const unmarkLateBtn = document.createElement('button');
                                unmarkLateBtn.id = 'unmarkLateBtn';
                                unmarkLateBtn.className = 'btn btn-primary w-100 mb-2';
                                unmarkLateBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i> Unmark Late';
                                unmarkLateBtn.addEventListener('click', function () {
                                    handleUnmarkLate();
                                });

                                // Create Close Form button
                                const closeFormBtnLate = document.createElement('button');
                                closeFormBtnLate.id = 'closeFormBtn';
                                closeFormBtnLate.className = 'btn btn-light-danger w-100 mb-2';
                                closeFormBtnLate.innerHTML = '<i class="bi bi-x-circle me-1"></i> Close Form';
                                closeFormBtnLate.addEventListener('click', function () {
                                    closeForm();
                                });

                                // Add buttons to action panel
                                if (actionPanel) {
                                    actionPanel.appendChild(unmarkLateBtn);
                                    actionPanel.appendChild(closeFormBtnLate);
                                }
                                break;

                            default:
                                console.log('Status: Other - Showing all options');
                                // For other statuses, ensure finalize button is enabled
                                if (finalizeBtn) {
                                    finalizeBtn.disabled = false;
                                    finalizeBtn.classList.remove('disabled');
                                }
                                break;
                        }
                    }

                } catch (error) {
                    console.error('Error checking admin role:', error);
                    // Fallback: show default UI if there's an error fetching role
                    console.log('Using default UI due to error fetching admin role');
                }
            }

            async function loadApprovalHistory() {
                try {
                    const response = await fetch(`/api/admin/requisition/${requestId}/approval-history`, {
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch approval history');

                    const approvalHistory = await response.json();

                    // Update approvals modal
                    const approvalsModalBody = document.querySelector('#approvalsModal .modal-body');
                    approvalsModalBody.innerHTML = generateApprovalHistoryHTML(approvalHistory.filter(item => item.action === 'approved'));

                    // Update rejections modal
                    const rejectionsModalBody = document.querySelector('#rejectionsModal .modal-body');
                    rejectionsModalBody.innerHTML = generateApprovalHistoryHTML(approvalHistory.filter(item => item.action === 'rejected'));

                } catch (error) {
                    console.error('Error loading approval history:', error);
                    const modalBodies = document.querySelectorAll('#approvalsModal .modal-body, #rejectionsModal .modal-body');
                    modalBodies.forEach(body => {
                        body.innerHTML = '<div class="text-center text-muted py-4">Failed to load history</div>';
                    });
                }
            }


            function generateApprovalHistoryHTML(history) {
                if (!history || history.length === 0) {
                    return '<div class="text-center text-muted py-4">No records found</div>';
                }

                return history.map(item => `
                                <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                    <div class="me-3 flex-shrink-0">
                                        ${item.admin_photo ?
                        `<img src="${item.admin_photo}" class="rounded-circle" width="45" height="45" alt="${item.admin_name}" style="object-fit: cover;">` :
                        `<div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" style="width: 45px; height: 45px;">
                                                ${item.admin_name.split(' ').map(n => n.charAt(0)).join('')}
                                            </div>`
                    }
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="d-block">${item.admin_name}</strong>
                                                <small class="text-muted">
                                                    <i class="fa ${item.action_icon} ${item.action_class} me-1"></i>
                                                    ${item.action} this request
                                                </small>
                                                ${item.remarks ? `<div class="mt-1 small text-muted">"${item.remarks}"</div>` : ''}
                                            </div>
                                            <small class="text-muted text-end">${item.formatted_date}</small>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
            }



            // Function to check if the current admin has already taken action on this request
            async function checkIfAdminHasTakenAction(adminId) {
                try {
                    const adminToken = localStorage.getItem('adminToken');
                    const requestId = window.location.pathname.split('/').pop();

                    // Use the approval history endpoint we created
                    const response = await fetch(`/api/admin/requisition/${requestId}/approval-history`, {
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        console.error('Failed to fetch approval history');
                        return false;
                    }

                    const approvalHistory = await response.json();

                    console.log('Approval history for request:', {
                        requestId: requestId,
                        adminId: adminId,
                        approvalHistory: approvalHistory
                    });

                    // Check if current admin appears in the approval history by admin_id
                    const hasTakenAction = approvalHistory.some(record => {
                        return record.admin_id === adminId;
                    });

                    console.log('Admin action check result:', {
                        adminId: adminId,
                        hasTakenAction: hasTakenAction,
                        approvalHistoryLength: approvalHistory.length,
                        matchingRecords: approvalHistory.filter(record => record.admin_id === adminId)
                    });

                    return hasTakenAction;

                } catch (error) {
                    console.error('Error checking admin action:', error);
                    return false;
                }
            }

            // Add event listener for Finalize button (AFTER the function definition)
            const finalizeBtn = document.getElementById('finalizeBtn');
            if (finalizeBtn) {
                finalizeBtn.addEventListener('click', function () {
                    finalizeModal.show();
                });
            }



            // Check admin role and update UI (AFTER event listeners are set up)
            checkAdminRoleAndUpdateUI();


            // Handle status option clicks
            statusOptions.forEach(option => {
                option.addEventListener('click', function () {
                    const action = this.dataset.value;
                    handleStatusAction(action);
                });
            });

            document.getElementById('closeForm').addEventListener('click', function () {
                closeForm();
            });

            // Add event listeners for status options in the More Actions dropdown

            document.querySelectorAll('#moreActionsBtn + .dropdown-menu .status-option').forEach(option => {
                option.addEventListener('click', function (e) {
                    e.preventDefault();
                    const action = this.dataset.value;

                    // SPECIAL CASE: Handle Scheduled with custom modal
                    if (action === 'Scheduled') {
                        handleMarkScheduled();
                        return;
                    }

                    // Check if we're dealing with Late status and current status is already Late
                    const statusBadge = document.getElementById('statusBadge');
                    const currentStatusName = statusBadge ? statusBadge.textContent.trim() : '';

                    if (action === 'Late' && currentStatusName === 'Late') {
                        handleUnmarkLate();
                    } else {
                        handleStatusAction(action);
                    }
                });
            });


            // Function to handle different status actions
            function handleStatusAction(action) {
                const statusBadge = document.getElementById('statusBadge');
                const currentStatusName = statusBadge ? statusBadge.textContent.trim() : '';

                // Get current status ID from the request data
                const currentRequest = allRequests.find(req => req.request_id == requestId);
                const currentStatusId = currentRequest ? currentRequest.form_details.status.id : null;

                // Check if user is Head Admin (you'll need to implement this check)
                const isHeadAdmin = checkIfHeadAdmin();

                function checkIfHeadAdmin() {
                    try {
                        // Get admin data from localStorage or make an API call
                        const adminData = JSON.parse(localStorage.getItem('adminProfile') || '{}');
                        return adminData.role?.role_title === 'Head Admin';
                    } catch (error) {
                        console.error('Error checking Head Admin status:', error);
                        return false;
                    }
                }

                if (isHeadAdmin) {
                    // Head Admin specific restrictions based on status_id
                    switch (currentStatusId) {
                        case 'Awaiting Payment': // Assuming status_id for Awaiting Payment
                            if (action === 'Ongoing' || action === 'Late') {
                                showToast('This action is not available for forms with Awaiting Payment status.', 'error');
                                return;
                            }
                            break;

                        case 'Scheduled': // Assuming status_id for Scheduled
                            if (action === 'Scheduled' || action === 'Late') {
                                showToast('This action is not available for forms with Scheduled status.', 'error');
                                return;
                            }
                            // Disable finalize button for Scheduled status
                            const finalizeBtn = document.getElementById('finalizeBtn');
                            if (finalizeBtn && action === 'Finalize') {
                                showToast('Cannot finalize a form that is already Scheduled.', 'error');
                                return;
                            }
                            break;

                        case 'Ongoing': // Assuming status_id for Ongoing
                            // For Ongoing status, replace Finalize with Mark Late
                            if (action === 'Finalize') {
                                // Instead of finalizing, mark as late
                                handleStatusAction('Late');
                                return;
                            }
                            break;
                    }
                }

                // SPECIAL CASE: Handle Scheduled status with custom modal
                if (action === 'Scheduled') {
                    handleMarkScheduled();
                    return; // Exit early to prevent the generic status modal from showing
                }

                // For all other statuses, use the generic status update modal
                switch (action) {
                    case 'Ongoing':
                    case 'Late':
                        showStatusUpdateModal(action);
                        break;

                    case 'Finalize Form':
                        if (currentStatusName === 'Pending Approval') {
                            finalizeModal.show();
                        } else {
                            showToast('This form is already finalized.', 'error');
                        }
                        break;

                    case 'Set Penalty Fee':
                        // Prompt user for penalty amount
                        const penaltyAmount = prompt('Enter late penalty amount:');
                        if (penaltyAmount !== null && penaltyAmount !== '') {
                            const amount = parseFloat(penaltyAmount);
                            if (!isNaN(amount) && amount >= 0) { // allow 0
                                addLatePenalty(amount);
                            } else {
                                showToast('Please enter a valid penalty amount (0 or greater).', 'error');
                            }
                        }
                        break;

                    case 'Close':
                        closeForm();
                        break;

                    default:
                        console.log('Unknown action:', action);
                }
            }

            // Confirm mark scheduled
            document.getElementById('confirmMarkScheduled').addEventListener('click', async function () {
                const btn = this;
                const spinner = btn.querySelector('.spinner-border');
                const originalText = btn.innerHTML;

                const officialReceiptNum = document.getElementById('officialReceiptNum').value.trim();
                const calendarTitle = document.getElementById('scheduledCalendarTitle').value.trim();
                const calendarDescription = document.getElementById('scheduledCalendarDescription').value.trim();

                if (!officialReceiptNum) {
                    showToast('Please enter an official receipt number', 'error');
                    return;
                }

                // Show loading state
                btn.disabled = true;
                spinner.classList.remove('d-none');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

                try {
                    const response = await fetch(`/api/admin/requisition/${requestId}/mark-scheduled`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            official_receipt_num: officialReceiptNum,
                            calendar_title: calendarTitle || null,
                            calendar_description: calendarDescription || null
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
                            'Failed to mark as scheduled';
                        throw new Error(errorMessage);
                    }

                    showToast('Request marked as scheduled successfully! Official receipt generated.', 'success');
                    markScheduledModal.hide();

                    // Clear form
                    document.getElementById('officialReceiptNum').value = '';
                    document.getElementById('scheduledCalendarTitle').value = '';
                    document.getElementById('scheduledCalendarDescription').value = '';

                    // Refresh the page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);

                } catch (error) {
                    console.error('Error marking as scheduled:', error);
                    showToast('Error: ' + error.message, 'error');

                    // Reset button state on error
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    spinner.classList.add('d-none');
                }
            });

            // Update your loadComments function to load both comments and fees
            async function loadMixedActivity() {
                try {
                    const container = document.getElementById('formRemarks');
                    container.innerHTML = `
                                                        <div class="comment-loading">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading activity...</span>
                                                            </div>
                                                        </div>
                                                    `;

                    const commentsResponse = await fetch(`/api/admin/requisition/${requestId}/comments`, {
                        headers: { 'Authorization': `Bearer ${adminToken}`, 'Accept': 'application/json' }
                    });

                    const feesResponse = await fetch(`/api/admin/requisition/${requestId}/fees`, {
                        headers: { 'Authorization': `Bearer ${adminToken}`, 'Accept': 'application/json' }
                    });

                    const commentsResult = await commentsResponse.json();
                    const feesResult = await feesResponse.json();

                    currentComments = commentsResult.comments || [];
                    currentFees = feesResult || [];

                    document.getElementById('commentCount').textContent = currentComments.length;

                    // Initially show all
                    displayMixedActivity(currentComments, currentFees, 'all');
                    scrollToBottom();

                } catch (error) {
                    console.error('Error loading activity:', error);
                    container.innerHTML = `
                                                        <div class="empty-comments text-danger">
                                                            <i class="bi bi-exclamation-triangle"></i>
                                                            <p>Failed to load activity.</p>
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

            // Function to display mixed comments and actions
            function displayMixedActivity(comments, fees, filter = 'all') {
                const container = document.getElementById('formRemarks');

                if (comments.length === 0 && fees.length === 0) {
                    container.innerHTML = `
                                                        <div class="empty-comments">
                                                            <i class="bi bi-chat"></i>
                                                            <p>No action has been taken in this form yet.</p>
                                                        </div>
                                                    `;
                    return;
                }

                // Combine comments and fees
                let allActivities = [];

                if (filter === 'all' || filter === 'comment') {
                    comments.forEach(comment => {
                        allActivities.push({
                            type: 'comment',
                            data: comment,
                            timestamp: new Date(comment.created_at)
                        });
                    });
                }

                if (filter === 'all' || filter === 'fee') {
                    fees.forEach(fee => {
                        allActivities.push({
                            type: 'fee',
                            data: fee,
                            timestamp: new Date(fee.created_at)
                        });
                    });
                }

                // Sort by timestamp
                allActivities.sort((a, b) => a.timestamp - b.timestamp);

                // Render HTML
                container.innerHTML = allActivities.map(activity => {
                    return activity.type === 'comment'
                        ? generateCommentHTML(activity.data)
                        : generateFeeHTML(activity.data);
                }).join('');
            }

            document.getElementById('activityFilter').addEventListener('change', function () {
                const filter = this.value; // all / comment / fee
                displayMixedActivity(currentComments, currentFees, filter);
            });

            // Function to generate comment HTML
            function generateCommentHTML(comment) {
                return `
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
                                                                <strong class="me-2" style="font-size: 0.85rem;">${comment.admin.first_name} ${comment.admin.last_name}</strong>
                                                                <small class="text-muted">${formatTimeAgo(comment.created_at)}</small>
                                                            </div>
                                                            <div class="message-bubble bg-primary text-white p-3 rounded-3" style="max-width: 80%; border-bottom-left-radius: 4px !important;">
                                                                <p class="mb-0" style="white-space: pre-wrap; line-height: 1.4;">${escapeHtml(comment.comment)}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                `;
            }

            // Function to generate fee/action HTML
            function generateFeeHTML(fee) {
                const amount = parseFloat(fee.type === 'discount' ? fee.discount_amount : fee.fee_amount);
                const typeName = fee.type === 'discount' ? 'Discount' : 'Additional fee';
                const adminName = fee.added_by?.name || 'Admin';

                let message = '';
                let amountDisplay = '';

                if (fee.type === 'discount') {
                    if (fee.discount_type === 'Percentage') {
                        // Percentage discount: remove .00 and add % symbol
                        amountDisplay = `${parseFloat(amount)}%`;
                        message = `${adminName} added a discount - ${fee.label}: ${amountDisplay}`;
                    } else {
                        // Fixed discount: show with ₱ symbol
                        amountDisplay = `₱${parseFloat(amount).toFixed(2)}`;
                        message = `${adminName} added a discount - ${fee.label}: ${amountDisplay}`;
                    }
                } else if (fee.type === 'fee') {
                    // Regular fee
                    amountDisplay = `₱${parseFloat(amount).toFixed(2)}`;
                    message = `${adminName} added a fee - ${fee.label}: ${amountDisplay}`;
                } else if (fee.type === 'mixed') {
                    // Mixed fee and discount
                    const feePart = fee.fee_amount > 0 ? `₱${parseFloat(fee.fee_amount).toFixed(2)}` : '';
                    const discountPart = fee.discount_amount > 0 ?
                        (fee.discount_type === 'Percentage' ?
                            `${parseFloat(fee.discount_amount)}%` :
                            `₱${parseFloat(fee.discount_amount).toFixed(2)}`) : '';
                    amountDisplay = `${feePart} ${discountPart}`.trim();
                    message = `${adminName} added a mixed fee - ${fee.label}: ${amountDisplay}`;
                }

                return `
            <div class="comment mb-3">
                <div class="d-flex align-items-start">
                    <!-- Icon Circle -->
                    <div class="me-2 flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; font-size: 1rem; background-color: #d4edda; color: #28a745;">
                            <i class="fa fa-money-bill"></i>
                        </div>
                    </div>

                    <!-- Action Bubble -->
                    <div class="flex-grow-1">
                        <div class="message-bubble bg-info text-white p-3 rounded-3" 
                             style="max-width: 80%; border-bottom-left-radius: 4px !important;">
                            <p class="mb-1" style="white-space: normal; line-height: 1.4;">
                                ${message}
                            </p>
                            <small class="text-dark">
                                ${formatTimeAgo(fee.created_at)}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
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
            commentTextarea.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Send comment
            commentSendBtn.addEventListener('click', async function () {
                const commentText = commentTextarea.value.trim();

                if (!commentText) {
                    showToast('Please enter a comment', 'error');
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
                        loadMixedActivity();

                        // Show success message
                        showToast('Comment added successfully', 'success');
                    }

                } catch (error) {
                    console.error('Error adding comment:', error);
                    showToast('Failed to add comment: ' + error.message, 'error');
                }
            });

            // Allow sending with Enter key (but allow Shift+Enter for new lines)
            commentTextarea.addEventListener('keydown', function (e) {
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
            loadMixedActivity();

            // Function to refresh all fee displays including the total
async function refreshAllFeeDisplays() {
    try {
        // Fetch updated request data
        const response = await fetch(`/api/admin/requisition-forms/${requestId}`, {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const requestData = await response.json();
            
            // Update the total approved fee in both locations
            const totalFee = parseFloat(requestData.fees.approved_fee).toFixed(2);
            document.getElementById('totalApprovedFee').textContent = `₱${totalFee}`;
            document.getElementById('feeBreakdownTotal').textContent = `₱${totalFee}`;
            
            // Update additional fees display
            if (requestData.fees.requisition_fees) {
                updateAdditionalFees(requestData.fees.requisition_fees);
            }
            
            // Update base fees display
            if (requestData.requested_items) {
                updateBaseFees(requestData.requested_items, requestData.schedule);
            }
            
            // Refresh activity timeline
            await loadMixedActivity();
        }
    } catch (error) {
        console.error('Error refreshing fee displays:', error);
    }
}


            // Function to show status update modal
            function showStatusUpdateModal(status) {
                const modalContent = document.getElementById('statusModalContent');
                const statusBadge = document.getElementById('statusBadge');
                const currentStatusName = statusBadge ? statusBadge.textContent.trim() : '';

                if (currentStatusName === 'Pending Approval' && status !== 'Cancel Form') {
                    showToast('Finalize the form first', 'error');
                    return;
                }

                // Set modal content based on selected status
                switch (status) {
                    case 'Scheduled':
                        modalContent.innerHTML = `
                                                <div class="text-center">
                                                    <i class="fa fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                                    <p>Are you sure? This action cannot be undone.</p>
                                                    <p class="text-muted small">
                                                        This will set the form's status to <strong>Scheduled</strong>.
                                                        The request can still be cancelled if an emergency happens. 
                                                        If such a situation occurs, make sure to contact the requester about refund details and settle it in the business office on campus before closing the form.
                                                    </p>
                                                </div>
                                            `;
                        break;
                    case 'Ongoing':
                        modalContent.innerHTML = `
                                                <div class="text-center">
                                                    <i class="fa fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                                    <p>Are you sure? This action cannot be undone.</p>
                                                    <p class="text-muted small">
                                                        Sets the form status to <strong>Ongoing</strong>. Use this to manually set it if not already done. 
                                                    </p>
                                                </div>
                                            `;
                        break;
                    case 'Late':
                        modalContent.innerHTML = `
                                                <div class="text-center">
                                                    <i class="fa fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                                    <p>Are you sure? This action cannot be undone.</p>
                                                    <p class="text-muted small">
                                                        This will set the form's status to <strong>Late</strong> and mark it as overdue.
                                                    </p>
                                                </div>
                                                <div class="mt-4">
                                                    <div class="mb-3">
                                                        <label for="latePenaltyAmount" class="form-label">Late Penalty Amount (Optional)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₱</span>
                                                            <input type="number" class="form-control" id="latePenaltyAmount" 
                                                                   placeholder="Enter penalty amount" step="0.01" min="0" value="0">
                                                        </div>
                                                        <small class="text-muted">Enter the late penalty amount to be added to the fees (leave as 0 for no penalty).</small>
                                                    </div>
                                                </div>
                                            `;
                        break;
                    case 'Cancel Form':
                        modalContent.innerHTML = `
                                                <div class="text-center">
                                                    <i class="fa fa-exclamation-circle fa-3x text-danger mb-3"></i>
                                                    <p>Are you sure? This action cannot be undone.</p>
                                                    <p class="text-muted small">
                                                        This will <strong class="text-danger">cancel</strong> the form and set its status to <strong>Cancelled</strong>.
                                                        Note: This action cannot be undone. The requester will be notified about the cancellation.
                                                    </p>
                                                </div>
                                            `;
                        break;
                }

                selectedStatus = status;
                statusUpdateModal.show();
            }

            // Confirm status update
            document.getElementById('confirmStatusUpdate').addEventListener('click', async function () {
                if (!selectedStatus) return;

                const btn = this;
                const originalText = btn.innerHTML;
                const adminToken = localStorage.getItem('adminToken');

                try {
                    // Show loading state
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

                    // Prepare request data
                    const requestData = {
                        status_name: selectedStatus
                    };

                    // Add late penalty fee for Late status
                    if (selectedStatus === 'Late') {
                        const penaltyAmount = document.getElementById('latePenaltyAmount').value;
                        if (penaltyAmount && parseFloat(penaltyAmount) > 0) {
                            requestData.late_penalty_fee = parseFloat(penaltyAmount);
                        }
                    }

                    const statusResponse = await fetch(`/api/admin/requisition/${requestId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    });

                    const contentType = statusResponse.headers.get('content-type');
                    let statusResponseData;

                    if (contentType && contentType.includes('application/json')) {
                        statusResponseData = await statusResponse.json();
                    } else {
                        const textResponse = await statusResponse.text();
                        throw new Error(textResponse || 'Non-JSON response from server');
                    }

                    if (!statusResponse.ok) {
                        const errorMessage = statusResponseData.error ||
                            statusResponseData.message ||
                            JSON.stringify(statusResponseData) ||
                            'Failed to update status';
                        throw new Error(errorMessage);
                    }

                    showToast('Status updated successfully!', 'success');
                    statusUpdateModal.hide();
                    selectedStatus = '';

                    // Refresh the entire page to show updated status and UI
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);

                    // Refresh the page to show updated status and fees
                    fetchRequestDetails();

                    // Update the dropdown text after status change
                    updateStatusDropdownText();

                } catch (error) {
                    console.error('Error updating status:', error);
                    console.error('Status update details:', {
                        requestId: requestId,
                        status: selectedStatus,
                        error: error.message
                    });
                    showToast('Error: ' + error.message, 'error');
                } finally {
                    // Reset button state
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });


            // Update the closeForm function to use the modal
            async function closeForm() {
                closeFormModal.show();
            }

            function handleUnmarkLate() {
                const modalContent = document.getElementById('statusModalContent');

                modalContent.innerHTML = `
                                        <div class="text-center">
                                            <i class="fa fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                            <p>Are you sure you want to unmark this form as late?</p>
                                            <p class="text-muted small">
                                                This will set the form's status back to <strong>Ongoing</strong>, remove the late flag, 
                                                and reset any late penalty fees to zero.
                                            </p>
                                        </div>
                                    `;

                selectedStatus = 'Ongoing'; // Set to Ongoing to unmark late
                statusUpdateModal.show();
            }

            // Add event listener for the confirm button
            document.getElementById('confirmCloseForm').addEventListener('click', async function () {
                const btn = this;
                const spinner = btn.querySelector('.spinner-border');
                const originalText = btn.innerHTML;

                // Show loading state
                btn.disabled = true;
                spinner.classList.remove('d-none');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Closing...';

                const adminToken = localStorage.getItem('adminToken');

                try {
                    const response = await fetch(`/api/admin/requisition/${requestId}/close`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.details || 'Failed to close form');
                    }

                    closeFormModal.hide();
                    showToast('Form closed successfully! Redirecting...', 'success');

                    // Refresh the entire page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);

                } catch (error) {
                    console.error('Error closing form:', error);
                    showToast('Error: ' + error.message, 'error');

                    // Reset button state on error
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    spinner.classList.add('d-none');
                }
            });

            // Also update the event listener for the close form option in the dropdown
            document.getElementById('closeForm').addEventListener('click', function (e) {
                e.preventDefault();
                closeForm();
            });

            // Function to add late penalty
            async function addLatePenalty(penaltyAmount) {
                const adminToken = localStorage.getItem('adminToken');

                try {
                    const response = await fetch(`/api/admin/requisition/${requestId}/late-penalty`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            penalty_amount: penaltyAmount
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.details || 'Failed to add late penalty');
                    }

                    showToast('Late penalty added successfully!', 'success');
                    // Refresh the page to show updated fees
                    fetchRequestDetails();

                } catch (error) {
                    console.error('Error adding late penalty:', error);
                    showToast('Error: ' + error.message, 'error');
                }
            }

            document.getElementById('confirmFinalize').addEventListener('click', async function (e) {
                e.preventDefault();

                const btn = document.getElementById('confirmFinalize');

                const calendarTitle = document.getElementById('calendarTitle').value.trim();
                let calendarDescription = document.getElementById('calendarDescription').value.trim();
                const adminToken = localStorage.getItem('adminToken');

                // Title validation
                if (!calendarTitle || calendarTitle.length > 50) {
                    showToast('Calendar Title is required and must not exceed 50 characters.', 'error');
                    document.getElementById('calendarTitle').focus();
                    return;
                }

                // Description nullable
                if (!calendarDescription) {
                    calendarDescription = null;
                } else if (calendarDescription.length > 100) {
                    showToast('Calendar Description must not exceed 100 characters.', 'error');
                    document.getElementById('calendarDescription').focus();
                    return;
                }

                // Disable button and show spinner - USING THE SAME PATTERN AS checkAvailability
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Finalizing...';
                btn.disabled = true;

                try {
                    const response = await fetch(`/api/admin/requisition/${requestId}/finalize`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            calendar_title: calendarTitle,
                            calendar_description: calendarDescription
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
                            (responseData.details ? JSON.stringify(responseData.details) : 'Failed to finalize request');
                        throw new Error(errorMessage);
                    }

                    // Successful finalization
                    finalizeModal.hide();
                    document.getElementById('calendarTitle').value = '';
                    document.getElementById('calendarDescription').value = '';
                    fetchRequestDetails();
                    showToast('Form finalized successfully!', 'success');

                } catch (error) {
                    console.error('Error finalizing request:', error);
                    console.error('Finalize request details:', {
                        requestId: requestId,
                        calendarTitle: calendarTitle,
                        calendarDescription: calendarDescription,
                        error: error.message
                    });
                    showToast('Error: ' + error.message, 'error');
                } finally {
                    // Re-enable button and restore original text - USING THE SAME PATTERN AS checkAvailability
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });




// Handle individual waiver checkbox changes
async function handleWaiverChange(checkbox) {
    const type = checkbox.dataset.type;
    const id = parseInt(checkbox.dataset.id);
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

    // Collect ALL waived items (both checked ones)
    const waivedFacilities = [];
    const waivedEquipment = [];

    document.querySelectorAll('.waiver-checkbox').forEach(cb => {
        const itemId = parseInt(cb.dataset.id);
        const itemType = cb.dataset.type;

        if (cb.checked) {
            if (itemType === 'facility') {
                waivedFacilities.push(itemId);
            } else if (itemType === 'equipment') {
                waivedEquipment.push(itemId);
            }
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

        // Refresh ALL fee displays including the total
        await refreshAllFeeDisplays();

        // Show appropriate success message based on action
        if (type === 'facility') {
            const facilityName = itemRow ? itemRow.querySelector('.item-name').textContent : 'Facility';
            if (isWaived) {
                showToast(`${facilityName} waived successfully.`, 'success');
            } else {
                showToast(`${facilityName} waiver removed.`, 'success');
            }
        } else if (type === 'equipment') {
            const equipmentName = itemRow ? itemRow.querySelector('.item-name').textContent : 'Equipment';
            if (isWaived) {
                showToast(`${equipmentName} waived successfully.`, 'success');
            } else {
                showToast(`${equipmentName} waiver removed.`, 'success');
            }
        }

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
        showToast('Failed to update waiver: ' + error.message, 'error');
    }
}
            document.getElementById('waiveAllSwitch').addEventListener('change', function () {
                handleWaiveAll(this);
            });

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

        // Refresh ALL fee displays including the total
        await refreshAllFeeDisplays();

        // Show success message
        if (waiveAll) {
            showToast('All items waived successfully.', 'success');
        } else {
            showToast('All waivers removed.', 'success');
        }

    } catch (error) {
        console.error('Error updating waive all:', error);
        // Revert switch state on error
        switchElement.checked = !waiveAll;
        showToast('Failed to update waive all: ' + error.message, 'error');
    }
}

            // A map to get user-friendly names for fee types
            const feeTypeNames = {
                additional: 'Additional fee',
                discount: 'Discount',
            };



            // Remove the togglePlaceholder function entirely and replace it with:
            function updateFeesDisplay() {
                const feesContainer = document.getElementById('additionalFeesContainer');
                // This function can be used to update the fees display if needed
                // But we don't need the placeholder logic anymore
            }

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
        showToast("Please fill all required fields.", "success");
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
        showToast('Fee/discount added successfully', 'success');

        // Refresh ALL fee displays including the total
        await refreshAllFeeDisplays();

    } catch (error) {
        console.error('Error adding fee/discount:', error);
        console.error('Fee addition details:', {
            type: type,
            value: value,
            label: label,
            discountType: discountType,
            error: error.message
        });
        showToast('Failed to add fee/discount: ' + error.message, 'error');
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

                // Get current request's facility and equipment IDs
                const currentFacilityIds = currentRequest.requested_items.facilities.map(f => f.requested_facility_id);
                const currentEquipmentIds = currentRequest.requested_items.equipment.flatMap(e => e.requested_equipment_ids || []);

                console.log('Current request items:', {
                    requestId: requestId,
                    facilities: currentFacilityIds,
                    equipment: currentEquipmentIds
                });

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    initialDate: currentRequest.schedule.start_date,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    buttonText: {
                        today: 'Today',
                        month: 'Month',
                        week: 'Week',
                        day: 'Day'
                    },
                    titleFormat: {
                        year: 'numeric',
                        month: 'short'
                    },
                    height: '100%',
                    handleWindowResize: true,
                    windowResizeDelay: 200,
                    aspectRatio: null,
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
                    slotMinTime: '00:00:00',
                    slotMaxTime: '24:00:00',
                    allDaySlot: false,
                    nowIndicator: true,
                    navLinks: true,
                    dayHeaderFormat: { weekday: 'long', month: 'short', day: 'numeric' },
                    views: {
                        dayGridMonth: {
                            dayHeaderFormat: {
                                weekday: 'short'
                            }
                        }
                    },
                    eventDisplay: 'block'
                });

                calendar.render();

                // Update calendar with filtered events
                if (calendar) {
                    calendar.removeAllEvents();

                    // Filter requests that share at least one requested item with current request
                    const filteredRequests = allRequests.filter(req => {
                        // Skip the current request itself
                        if (req.request_id == requestId) return false;

                        // Get the other request's facility and equipment IDs
                        const otherFacilityIds = req.requested_items.facilities.map(f => f.requested_facility_id);
                        const otherEquipmentIds = req.requested_items.equipment.flatMap(e => e.requested_equipment_ids || []);

                        // Check if they share any facilities (by ID)
                        const shareFacilities = otherFacilityIds.some(id => currentFacilityIds.includes(id));

                        // Check if they share any equipment (by ID)
                        const shareEquipment = otherEquipmentIds.some(id => currentEquipmentIds.includes(id));

                        console.log(`Comparing request ${req.request_id} with current ${requestId}:`, {
                            otherFacilityIds: otherFacilityIds,
                            otherEquipmentIds: otherEquipmentIds,
                            currentFacilityIds: currentFacilityIds,
                            currentEquipmentIds: currentEquipmentIds,
                            shareFacilities: shareFacilities,
                            shareEquipment: shareEquipment,
                            shouldInclude: shareFacilities || shareEquipment
                        });

                        return shareFacilities || shareEquipment;
                    });

                    console.log('Filtered calendar events:', {
                        totalRequests: allRequests.length,
                        filteredRequests: filteredRequests.length,
                        filteredRequestIds: filteredRequests.map(r => r.request_id)
                    });

                    // Add filtered events to calendar
                    filteredRequests.forEach(req => {
                        // Handle empty calendar title for filtered requests
                        const calendarTitle = req.form_details.calendar_info.title || 'No Calendar Title';

                        calendar.addEvent({
                            title: calendarTitle,
                            start: `${req.schedule.start_date}T${req.schedule.start_time}`,
                            end: `${req.schedule.end_date}T${req.schedule.end_time}`,
                            extendedProps: {
                                status: req.form_details.status.name,
                                requestId: req.request_id
                            },
                            description: req.form_details.calendar_info.description
                        });
                    });

                    // Highlight current request with different styling
                    const currentCalendarTitle = currentRequest.form_details.calendar_info.title || 'No Calendar Title';

                    calendar.addEvent({
                        title: 'Current Request',
                        start: `${currentRequest.schedule.start_date}T${currentRequest.schedule.start_time}`,
                        end: `${currentRequest.schedule.end_date}T${currentRequest.schedule.end_time}`,
                        extendedProps: {
                            status: currentRequest.form_details.status.name,
                            requestId: currentRequest.request_id,
                            isCurrent: true
                        },
                        color: '#FF6B35', // Different color for current request
                        textColor: '#FFFFFF'
                    });
                }
            }

            // Function to show event details in modal
            function showEventModal(request) {
                // Format request ID with leading zeros
                const formattedRequestId = String(request.request_id).padStart(4, '0');
                // Handle empty calendar title
                const calendarTitle = request.form_details.calendar_info.title || 'No Calendar Title';
                // Update modal title with request ID and calendar title
                document.getElementById('eventModalTitle').textContent =
                    `Request ID #${formattedRequestId} (${request.form_details.calendar_info.title})`;

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

                // Format requested items for the right column
                let itemsHtml = '';

                if (request.requested_items.facilities.length > 0) {
                    itemsHtml += '<div class="fw-bold small mb-1">Facilities:</div>';
                    itemsHtml += request.requested_items.facilities.map(f =>
                        `<div class="mb-1 small">• ${f.name} | ₱${f.fee}${f.rate_type === 'Per Hour' ? '/hour' : '/event'}${f.is_waived ? ' <span class="text-muted">(Waived)</span>' : ''}</div>`
                    ).join('');
                }

                if (request.requested_items.equipment.length > 0) {
                    itemsHtml += '<div class="fw-bold small mt-2 mb-1">Equipment:</div>';
                    itemsHtml += request.requested_items.equipment.map(e =>
                        `<div class="mb-1 small">• ${e.name} × ${e.quantity || 1} | ₱${e.fee}${e.rate_type === 'Per Hour' ? '/hour' : '/event'}${e.is_waived ? ' <span class="text-muted">(Waived)</span>' : ''}</div>`
                    ).join('');
                }

                document.getElementById('modalItems').innerHTML = itemsHtml || '<p class="text-muted small">No items requested</p>';

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
                    requisitionFees.forEach((fee, index) => {
                        const feeElement = document.createElement('div');
                        feeElement.className = 'fee-item d-flex justify-content-between align-items-center mb-1';

                        let amountText = '';
                        if (fee.type === 'fee') {
                            amountText = `₱${parseFloat(fee.fee_amount).toFixed(2)}`;
                        } else if (fee.type === 'discount') {
                            if (fee.discount_type === 'Percentage') {
                                // Remove .00 for percentage discounts
                                amountText = `${parseFloat(fee.discount_amount)}%`;
                            } else {
                                amountText = `-₱${parseFloat(fee.discount_amount).toFixed(2)}`;
                            }
                        } else if (fee.type === 'mixed') {
                            const feePart = fee.fee_amount > 0 ? `₱${parseFloat(fee.fee_amount).toFixed(2)}` : '';
                            const discountPart = fee.discount_amount > 0 ?
                                (fee.discount_type === 'Percentage' ?
                                    `-${parseFloat(fee.discount_amount)}%` :
                                    `-₱${parseFloat(fee.discount_amount).toFixed(2)}`) : '';
                            amountText = `${feePart} ${discountPart}`.trim();
                        }

                        feeElement.innerHTML = `
                    <span class="item-name">${fee.label}</span>
                    <span class="d-flex align-items-center">
                        <span class="item-price me-2">${amountText}</span>
                        <button class="btn btn-sm btn-danger delete-fee-btn" data-fee-id="${fee.fee_id}" data-fee-type="${fee.type}">
                            <i class="fa fa-times"></i>
                        </button>
                    </span>
                `;

                        additionalFeesContainer.appendChild(feeElement);
                    });

                    // Handle delete click
                    additionalFeesContainer.querySelectorAll('.delete-fee-btn').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            const feeId = e.currentTarget.dataset.feeId;
                            const feeType = e.currentTarget.dataset.feeType;

                            if (!feeId) {
                                console.error('No fee ID found for deletion');
                                showToast('Cannot delete fee that does not exist.', 'error');
                                return;
                            }

                            try {
                                const response = await fetch(`/api/admin/requisition/${requestId}/fee/${feeId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Authorization': `Bearer ${adminToken}`,
                                        'Accept': 'application/json'
                                    }
                                });

                                if (!response.ok) {
                                    const errorData = await response.json();
                                    throw new Error(errorData.details || 'Failed to delete fee');
                                }

                                const result = await response.json();

                                // Show success message
                                showToast('Fee removed successfully', 'success');

                                // Refresh ALL fee displays including the total
                                await refreshAllFeeDisplays();

                            } catch (error) {
                                console.error('Error removing fee:', error);
                                console.error('Fee deletion details:', {
                                    feeId: feeId,
                                    feeType: feeType,
                                    requestId: requestId,
                                    error: error.message
                                });
                                showToast('Failed to remove fee: ' + error.message, 'error');
                            }
                        });
                    });
                } else {
                    // Show empty message
                    additionalFeesContainer.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fa fa-coins fa-2x d-block mb-2"></i>
                    <p class="mb-0">No additional fees or discounts</p>
                </div>
            `;
                }
            }

            // Update the dropdown menu text dynamically based on current status
            function updateStatusDropdownText() {
                const statusBadge = document.getElementById('statusBadge');
                const currentStatusName = statusBadge ? statusBadge.textContent.trim() : '';
                const lateOption = document.getElementById('statusLate');

                if (lateOption) {
                    if (currentStatusName === 'Late') {
                        lateOption.innerHTML = '<i class="bi bi-check-circle me-2"></i> Unmark Late';
                        lateOption.dataset.value = 'Late'; // Keep the same data value
                    } else {
                        lateOption.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i> Mark Late';
                        lateOption.dataset.value = 'Late';
                    }
                }
            }



            async function fetchRequestDetails() {
                try {
                    // Ensure loading state is visible and content is hidden
                    document.getElementById('loadingState').style.display = 'block';
                    document.getElementById('contentState').style.display = 'none';

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

                    // Update request ID in title
                    document.getElementById('requestIdTitle').textContent = 'RID #' + String(requestId).padStart(4, '0');


                    // Update status badge
                    const statusBadge = document.getElementById('statusBadge');
                    statusBadge.textContent = request.form_details.status.name;
                    statusBadge.style.backgroundColor = request.form_details.status.color;

                    // Update fees display
                    document.getElementById('totalApprovedFee').textContent = `₱${parseFloat(request.fees.approved_fee).toFixed(2)}`;
                    document.getElementById('feeBreakdownTotal').textContent = `₱${parseFloat(request.fees.approved_fee).toFixed(2)}`;

                    // Contact information
                    document.getElementById('formDetails').innerHTML = `
                                <table class="table table-borderless mb-0 small text-start align-top">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div><strong>Requester</strong></div>
                                                <div>${request.user_details.first_name} ${request.user_details.last_name}</div>
                                            </td>
                                            <td>
                                                <div><strong>School ID</strong></div>
                                                <div>${request.user_details.school_id || 'N/A'}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div><strong>Email</strong></div>
                                                <div>${request.user_details.email}</div>
                                            </td>
                                            <td>
                                                <div><strong>Organization</strong></div>
                                                <div>${request.user_details.organization_name || 'N/A'}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div><strong>User Type</strong></div>
                                                <div>${request.user_details.user_type}</div>
                                            </td>
                                            <td>
                                                <div><strong>Contact Number</strong></div>
                                                <div>${request.user_details.contact_number || 'N/A'}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            `;

               // Event details
document.getElementById('eventDetails').innerHTML = `
    <table class="table table-borderless mb-0 small text-start align-top">
        <tbody>
            <tr>
                <td>
                    <div><strong>Endorser</strong></div>
                    <div>${request.documents.endorser || 'N/A'}</div>
                </td>
                <td>
                    <div><strong>Date Endorsed</strong></div>
                    <div>${formatDateEndorsed(request.documents.date_endorsed) || 'N/A'}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><strong>Rental Purpose</strong></div>
                    <div>${request.form_details.purpose}</div>
                </td>
                <td>
                    <div><strong>Participants</strong></div>
                    <div>${request.form_details.num_participants}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div><strong>Additional Requests</strong></div>
                    <div>${request.form_details.additional_requests || 'No additional requests.'}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><strong>Start Schedule</strong></div>
                    <div>${formatStartDateTime(request.schedule)}</div>
                </td>
                <td>
                    <div><strong>End Schedule</strong></div>
                    <div>${formatEndDateTime(request.schedule)}</div>
                </td>
            </tr>
        </tbody>
    </table>
`;
                    // Update requested items with fee breakdown
                    document.getElementById('requestedItems').innerHTML = `
                                <div class="mb-3">
                                    ${request.requested_items.facilities.length > 0 ? `
                                        <h6 class="fw-bold d-flex justify-content-between align-items-center mb-2" style="font-size:0.85rem; padding:0 0.5rem;">Facilities:</h6>
                                        ${request.requested_items.facilities.map(f =>
                        `<div class="d-flex align-items-center mb-2 item-row" style="padding:0 0.5rem;">
                                                <span class="item-name">${f.name}</span>
                                                <span style="flex:1; border-bottom: 1px dashed #ccc; margin: 0 0.5rem;"></span>
                                                <span class="item-price">₱${f.fee}${f.rate_type === 'Per Hour' ? '/hour' : '/event'}</span>
                                            </div>`
                    ).join('')}
                                    ` : ''}

                                    ${request.requested_items.equipment.length > 0 ? `
                                        <h6 class="fw-bold d-flex justify-content-between align-items-center mt-3 mb-2" style="font-size:0.85rem; padding:0 0.5rem;">Equipment:</h6>
                                        ${request.requested_items.equipment.map(e =>
                        `<div class="d-flex align-items-center mb-2 item-row" style="padding:0 0.5rem;">
                                                <span class="item-name">${e.name} × ${e.quantity || 1}</span>
                                                <span style="flex:1; border-bottom: 1px dashed #ccc; margin: 0 0.5rem;"></span>
                                                <span class="item-price">₱${e.fee}${e.rate_type === 'Per Hour' ? '/hour' : '/event'}</span>
                                            </div>`
                    ).join('')}
                                    ` : ''}
                                </div>
                            `;

                    // Update status cards
                    document.getElementById('approvalsCount').textContent = request.approval_info.approval_count;
                    document.getElementById('rejectionsCount').textContent = request.approval_info.rejection_count;
                    document.getElementById('isLateStatus').textContent = request.status_tracking.is_late ? 'Late' : 'Not Late';

                    // Update document cards
                    document.getElementById('formalLetterDocument').innerHTML = request.documents.formal_letter.url ?
                        `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                    data-document-url="${request.documents.formal_letter.url}" data-document-title="Formal Letter">
                                    Uploaded
                                </button>` :
                        '<span class="badge bg-secondary">Not uploaded</span>';

                    document.getElementById('facilityLayoutDocument').innerHTML = request.documents.facility_layout.url ?
                        `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                    data-document-url="${request.documents.facility_layout.url}" data-document-title="Facility Setup">
                                    Uploaded
                                </button>` :
                        '<span class="badge bg-secondary">Not uploaded</span>';

                    document.getElementById('proofOfPaymentDocument').innerHTML = request.documents.proof_of_payment.url ?
                        `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                    data-document-url="${request.documents.proof_of_payment.url}" data-document-title="Proof of Payment">
                                    Uploaded
                                </button>` :
                        '<span class="badge bg-secondary">Not uploaded</span>';

                    // Official Receipt - Check both uploaded document AND generated receipt number
                    const officialReceiptContainer = document.getElementById('officialReceiptDocument');
                    if (request.documents.official_receipt.url) {
                        // If official receipt document is uploaded
                        officialReceiptContainer.innerHTML =
                            `<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" 
                            data-document-url="${request.documents.official_receipt.url}" data-document-title="Official Receipt">
                            Uploaded
                        </button>`;
                    } else if (request.form_details.official_receipt_num) {
                        // If official receipt number exists (form is scheduled), show the generate receipt button
                        const receiptNum = request.form_details.official_receipt_num;
                        officialReceiptContainer.innerHTML =
                            `<a href="/official-receipt/${requestId}" target="_blank" class="btn btn-sm btn-success">
                            <i class="fas fa-receipt me-1"></i> View Receipt
                        </a>
                        <small class="text-muted d-block mt-1">OR: ${receiptNum}</small>`;
                    } else {
                        // No official receipt available
                        officialReceiptContainer.innerHTML = '<span class="badge bg-secondary">Not available</span>';
                    }

                    // Update document icons
                    updateDocumentIcons(request.documents);

                    // Load additional data and wait for completion
                    await Promise.all([
                        loadMixedActivity(),
                        loadApprovalHistory(),
                        updateBaseFees(request.requested_items, request.schedule)
                    ]);

                    // Ensure misc fees are updated
                    if (request.fees && request.fees.requisition_fees) {
                        updateAdditionalFees(request.fees.requisition_fees);
                    }

                    // Update dropdown text
                    updateStatusDropdownText();

                    // Check admin role and update UI
                    await checkAdminRoleAndUpdateUI();

                    // Initialize calendar
                    setTimeout(() => {
                        initializeCalendar();
                    }, 100);

                    // ONLY NOW show content after everything is properly loaded
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('contentState').style.display = 'block';

                    // Now that container is visible, initialize calendar
                    initializeCalendar(request.schedule);


                } catch (error) {
                    console.error('Error:', error);
                    showToast('Failed to load request details', 'error');

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

            // Add this helper function for document icons
            function updateDocumentIcons(documents) {
                // Formal Letter
                const formalLetterIcon = document.getElementById('formalLetterIcon');
                if (documents.formal_letter.url) {
                    formalLetterIcon.classList.remove('text-muted');
                    formalLetterIcon.classList.add('text-primary');
                }

                // Facility Layout
                const facilityLayoutIcon = document.getElementById('facilityLayoutIcon');
                if (documents.facility_layout.url) {
                    facilityLayoutIcon.classList.remove('text-muted');
                    facilityLayoutIcon.classList.add('text-primary');
                }

                // Proof of Payment
                const proofOfPaymentIcon = document.getElementById('proofOfPaymentIcon');
                if (documents.proof_of_payment.url) {
                    proofOfPaymentIcon.classList.remove('text-muted');
                    proofOfPaymentIcon.classList.add('text-primary');
                }

                // Official Receipt
                const officialReceiptIcon = document.getElementById('officialReceiptIcon');
                if (documents.official_receipt.url) {
                    officialReceiptIcon.classList.remove('text-muted');
                    officialReceiptIcon.classList.add('text-primary');
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

                // Add facilities with proper rate type logic and waiver checkboxes
                if (requestedItems.facilities && requestedItems.facilities.length > 0) {
                    requestedItems.facilities.forEach(facility => {
                        const facilityElement = document.createElement('div');
                        facilityElement.className = `fee-item d-flex justify-content-between align-items-center mb-2 p-2 rounded ${facility.is_waived ? 'waived' : ''}`;

                        let feeAmount = parseFloat(facility.fee);
                        let itemTotal = 0;
                        let rateDescription = '';

                        if (facility.rate_type === 'Per Hour' && durationHours > 0) {
                            itemTotal = feeAmount * durationHours;
                            rateDescription = `₱${feeAmount.toLocaleString()}/hr × ${durationHours.toFixed(1)} hrs`;
                            if (!facility.is_waived) totalBaseFees += itemTotal;
                        } else {
                            itemTotal = feeAmount;
                            rateDescription = `₱${feeAmount.toLocaleString()}/event`;
                            if (!facility.is_waived) totalBaseFees += itemTotal;
                        }

                        facilityElement.innerHTML = `
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <div class="form-check me-2">
                                                                                                                                        <input class="form-check-input waiver-checkbox" type="checkbox" 
                                                                                                                                            data-type="facility" 
                                                                                                                                            data-id="${facility.requested_facility_id}"
                                                                                                                                            ${facility.is_waived ? 'checked' : ''}>
                                                                                                                                    </div>
                                                                                                                                    <span class="item-name">${facility.name}</span>
                                                                                                                                </div>
                                                                                                                                <div class="text-end">
                                                                                                                                    <small>${rateDescription}</small>
                                                                                                                                    <div><strong>₱${itemTotal.toLocaleString()}</strong></div>
                                                                                                                                </div>
                                                                                                                            `;
                        facilitiesContainer.appendChild(facilityElement);
                    });
                }

                // Add equipment with proper rate type logic and waiver checkboxes
                if (requestedItems.equipment && requestedItems.equipment.length > 0) {
                    requestedItems.equipment.forEach(equipment => {
                        const equipmentElement = document.createElement('div');
                        equipmentElement.className = `fee-item d-flex justify-content-between align-items-center mb-2 p-2 rounded ${equipment.is_waived ? 'waived' : ''}`;

                        let unitFee = parseFloat(equipment.fee);
                        const quantity = equipment.quantity || 1;
                        let itemTotal = 0;
                        let rateDescription = '';

                        if (equipment.rate_type === 'Per Hour' && durationHours > 0) {
                            itemTotal = (unitFee * durationHours) * quantity;
                            rateDescription = `₱${unitFee.toLocaleString()}/hr × ${durationHours.toFixed(1)} hrs × ${quantity}`;
                            if (!equipment.is_waived) totalBaseFees += itemTotal;
                        } else {
                            itemTotal = unitFee * quantity;
                            rateDescription = `₱${unitFee.toLocaleString()}/event × ${quantity}`;
                            if (!equipment.is_waived) totalBaseFees += itemTotal;
                        }

                        equipmentElement.innerHTML = `
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <div class="form-check me-2">
                                                                                                                                        <input class="form-check-input waiver-checkbox" type="checkbox" 
                                                                                                                                            data-type="equipment" 
                                                                                                                                            data-id="${equipment.requested_equipment_id}"
                                                                                                                                            ${equipment.is_waived ? 'checked' : ''}>
                                                                                                                                    </div>
                                                                                                                                    <span class="item-name">
                                                                                                                                        ${equipment.name} ${quantity > 1 ? `(×${quantity})` : ''}
                                                                                                                                    </span>
                                                                                                                                </div>
                                                                                                                                <div class="text-end">
                                                                                                                                    <small>${rateDescription}</small>
                                                                                                                                    <div><strong>₱${itemTotal.toLocaleString()}</strong></div>
                                                                                                                                </div>
                                                                                                                            `;
                        equipmentContainer.appendChild(equipmentElement);
                    });
                }

                // Add event listeners to waiver checkboxes in the Fee Breakdown
                document.querySelectorAll('#baseFeesContainer .waiver-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        handleWaiverChange(this);
                    });
                });

                // Debug logging for backend troubleshooting
                console.log('Base Fees Update:', {
                    facilities: requestedItems.facilities?.map(f => ({
                        id: f.requested_facility_id,
                        name: f.name,
                        fee: f.fee,
                        rate_type: f.rate_type,
                        is_waived: f.is_waived
                    })),
                    equipment: requestedItems.equipment?.map(e => ({
                        id: e.requested_equipment_id,
                        name: e.name,
                        fee: e.fee,
                        rate_type: e.rate_type,
                        quantity: e.quantity,
                        is_waived: e.is_waived
                    })),
                    durationHours: durationHours,
                    totalBaseFees: totalBaseFees
                });
            }

            function formatDateEndorsed(dateString) {
    if (!dateString || dateString === 'N/A') return 'N/A';
    
    try {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    } catch (error) {
        console.error('Error formatting date endorsed:', error);
        return dateString; // Return original if formatting fails
    }
}

            function formatStartDateTime(schedule) {
                const startDate = new Date(schedule.start_date + 'T' + schedule.start_time);
                const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' };
                const timeOptions = { hour: 'numeric', minute: '2-digit', hour12: true };

                return `${startDate.toLocaleDateString('en-US', dateOptions)} | ${startDate.toLocaleTimeString('en-US', timeOptions)}`;
            }

            function formatEndDateTime(schedule) {
                const endDate = new Date(schedule.end_date + 'T' + schedule.end_time);
                const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' };
                const timeOptions = { hour: 'numeric', minute: '2-digit', hour12: true };

                return `${endDate.toLocaleDateString('en-US', dateOptions)} | ${endDate.toLocaleTimeString('en-US', timeOptions)}`;
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
                        // No need for placeholder anymore since we're using mixed activity display
                        return;
                    }

                    // Add each fee to the container
                    for (const fee of fees) {
                        const feeItem = await createFeeItem(fee);
                        feesContainer.appendChild(feeItem);
                    }

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
                        // Remove this line: togglePlaceholder();
                        fetchRequestDetails(); // Refresh fee breakdown

                    } catch (error) {
                        console.error('Error removing fee:', error);
                        showToast('Failed to remove fee: ' + error.message, 'error');
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

            // Confirm approve action
            document.getElementById('confirmApprove').addEventListener('click', async function () {
                const remarks = document.getElementById('approveRemarks').value;
                const adminToken = localStorage.getItem('adminToken');

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

                    showToast('Request approved successfully!', 'success');
                    approveModal.hide();

                    // Refresh everything
                    fetchRequestDetails();
                    loadMixedActivity(); // Refresh activity timeline
                    loadApprovalHistory(); // Refresh approval history
                    checkAdminRoleAndUpdateUI(); // Update UI to hide buttons

                } catch (error) {
                    console.error('Error approving request:', error);
                    showToast('Error: ' + error.message, 'error');
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

                    showToast('Request rejected successfully!', 'success');
                    rejectModal.hide();

                    // Refresh everything
                    fetchRequestDetails();
                    loadMixedActivity(); // Refresh activity timeline
                    loadApprovalHistory(); // Refresh approval history
                    checkAdminRoleAndUpdateUI(); // Update UI to hide buttons
                    localStorage.setItem('adminProfile', JSON.stringify(adminData));

                } catch (error) {
                    console.error('Error rejecting request:', error);
                    showToast('Error: ' + error.message, 'error');
                }
            });

            fetchRequestDetails();

        });
    </script>
@endsection