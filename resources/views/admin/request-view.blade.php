@extends('layouts.admin')
@section('title', 'Admin Dashboard • Manage Requisitions • View Request')
@section('content')
<style>
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

    .fc-header-toolbar {
        background-color: #ffffff;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 0;
    }

    .card {
        border-radius: var(--card-radius);
        border: none;
        box-shadow: var(--card-shadow);
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
</style>
<div class="container-fluid admin-container">
    <div class="card bg-transparent shadow-none">
        <div class="card-body p-4">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/manage-requests') }}">Requisitions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Request</li>
                </ol>
            </nav>
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
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Title + Status Badge -->
                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    <span>Requisition ID #<span id="requestIdTitle"></span>: Form Status</span>
                                    <span class="badge" id="statusBadge"></span>
                                </h5>
                                <div class="card-divider"></div>
                                <div id="formStatus"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-items-stretch">
                    <!-- Left column: Requester + Reservation -->
                    <div class="col-md-4 d-flex flex-column gap-2">
                        <!-- Requester Details -->
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5 class="card-title">Requester Details</h5>
                                <div class="card-divider"></div>
                                <div id="userDetails"></div>
                            </div>
                        </div>
                        <!-- Reservation Details -->
                        <div class="card flex-grow-1">
                            <div class="card-body">
                                <h5 class="card-title">Reservation Details</h5>
                                <div class="card-divider"></div>
                                <div id="formDetails"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Right column: Calendar -->
                    <div class="col-md-8 d-flex">
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Schedule Visualization</h5>
                                <div class="card-divider"></div>
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
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Requested Items</h5>
                                <div class="card-divider"></div>
                                <div id="requestedItems"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <link rel="stylesheet"
                        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
                    <div class="col-md-6">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Form Approval History</h5>
                                <button id="addFeeBtn" class="btn btn-sm btn-outline-primary">
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
                            <div class="card-footer p-2">
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
                                                <option value="late">Late Penalty Fee</option>
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
                                <div class="card-body">
                                    <h5 class="card-title d-flex justify-content-between align-items-center">
                                        <span>Fee Breakdown</span>
                                    </h5>
                                    <div class="card-divider"></div>
                                    <!-- Base Fees -->
                                    <div class="mb-3">
                                        <h6>Base Fees</h6>
                                        <div id="baseFeesContainer">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                    <hr style="color: black;">
                                    <!-- Additional Fees -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6>Additional Fees & Discounts</h6>
                                        </div>
                                        <div id="additionalFeesContainer">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                    <!-- Late Penalty -->
                                    <div class="mb-3" id="latePenaltySection" style="display: none;">
                                        <h6>Late Penalty</h6>
                                        <div id="latePenaltyContainer">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                    <!-- Total -->
                                    <div class="card-divider"></div>
                                    <div class="d-flex justify-content-between align-items-center fw-bold mt-2">
                                        <span>Total Approved Fee:</span>
                                        <span id="totalApprovedFee">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-success" id="approveBtn">Approve Request</button>
                                <button class="btn btn-danger" id="rejectBtn">Reject Request</button>
                            </div>
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
               <script>
    document.addEventListener("DOMContentLoaded", function() {
        const requestId = window.location.pathname.split('/').pop();
        const adminToken = localStorage.getItem('adminToken');
        let allRequests = [];
        
        // Initialize Bootstrap modal
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        const feeModal = new bootstrap.Modal(document.getElementById('feeModal'));
        
        // Fee management elements
        const feesContainer = document.getElementById("additionalFees");
        const placeholder = document.getElementById("feesPlaceholder");
        const addFeeBtn = document.getElementById("addFeeBtn");
        const saveFeeBtn = document.getElementById("saveFeeBtn");
        const feeTypeSelect = document.getElementById("feeType");
        const feeValueInput = document.getElementById("feeValue");
        const discountTypeSection = document.getElementById("discountTypeSection");
        
        // --- MOCK DATA (Replace with real data from your backend) ---
        const adminName = "Jane Doe"; // Example admin name
        const formStatus = "Is_Late"; // Example status: "Is_Late", "Approved", etc.

        // A map to get user-friendly names for fee types
        const feeTypeNames = {
            additional: 'Additional fee',
            discount: 'Discount',
            late: 'Late penalty fee'
        };

        // Disable "Late Penalty Fee" option if form status is not "Is_Late"
        if (formStatus !== "Is_Late") {
            const lateOption = feeTypeSelect.querySelector("option[value='late']");
            if (lateOption) lateOption.disabled = true;
        }

        // Function to toggle the placeholder text
        function togglePlaceholder() {
            placeholder.style.display = feesContainer.children.length === 0 ? "block" : "none";
        }
        togglePlaceholder(); // Initial check

        // Show the modal when "Add Fee" is clicked
        addFeeBtn.addEventListener("click", function() {
            feeModal.show();
        });

        // Handle fee type change to show/hide discount type
        feeTypeSelect.addEventListener('change', function() {
            discountTypeSection.style.display = this.value === 'discount' ? 'block' : 'none';
        });

        // Save Fee button logic
        saveFeeBtn.addEventListener("click", function() {
            const type = feeTypeSelect.value;
            const value = feeValueInput.value;
            const feeName = feeTypeNames[type] || 'Fee'; // Get user-friendly name

            if (!value) {
                alert("Please enter a value.");
                return;
            }

            // Create the improved fee item display
            const feeItem = document.createElement("div");
            feeItem.className = "fee-item d-flex align-items-start p-2 mb-2 rounded";

            // Get current timestamp
            const timestamp = new Date().toLocaleString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            // Generate the new inner HTML structure
            feeItem.innerHTML = `
                <i class="bi bi-person-circle fs-5 me-3 text-secondary"></i>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fst-italic">
                                ${feeName} of ${value} added by <strong>${adminName}</strong>
                            </small>
                        </div>
                        <button class="btn btn-sm btn-icon btn-light-danger remove-btn">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <small class="text-muted fst-italic">${timestamp}</small>
                </div>
            `;

            // Add remove functionality to the new button
            feeItem.querySelector(".remove-btn").addEventListener("click", function() {
                feeItem.remove();
                togglePlaceholder();
            });

            // Append fee to container
            feesContainer.appendChild(feeItem);
            togglePlaceholder();

            // Reset and close modal
            feeValueInput.value = "";
            feeTypeSelect.value = "";
            discountTypeSection.style.display = 'none';
            feeModal.hide();
        });

        // Document preview functionality - clean overlay for PDFs and images
        document.addEventListener('click', function(event) {
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
                overlay.onclick = function(event) {
                    if (event.target === overlay) {
                        closeOverlay();
                    }
                };

                // Close on ESC key
                const handleEscape = function(event) {
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

                    // Close button for images
                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '&times;';
                    closeButton.style.position = 'absolute';
                    closeButton.style.top = '-20px';
                    closeButton.style.right = '-43px';
                    closeButton.style.background = 'rgba(255, 255, 255, 0.2)';
                    closeButton.style.color = 'white';
                    closeButton.style.border = 'none';
                    closeButton.style.borderRadius = '50%';
                    closeButton.style.width = '40px';
                    closeButton.style.height = '40px';
                    closeButton.style.fontSize = '24px';
                    closeButton.style.cursor = 'pointer';
                    closeButton.style.transition = 'background 0.2s';
                    closeButton.onmouseover = function() {
                        this.style.background = 'rgba(255, 255, 255, 0.3)';
                    };
                    closeButton.onmouseout = function() {
                        this.style.background = 'rgba(255, 255, 255, 0.2)';
                    };
                    closeButton.onclick = function() {
                        closeOverlay();
                    };

                    imageContainer.appendChild(img);
                    imageContainer.appendChild(closeButton);
                    overlay.appendChild(imageContainer);

                } else {
                    // For other file types - download link
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
                    downloadLink.onclick = function() {
                        closeOverlay();
                    };

                    const closeButton = document.createElement('button');
                    closeButton.innerHTML = '&times;';
                    closeButton.style.position = 'absolute';
                    closeButton.style.top = '10px';
                    closeButton.style.right = '10px';
                    closeButton.style.background = 'rgba(0, 0, 0, 0.2)';
                    closeButton.style.color = 'black';
                    closeButton.style.border = 'none';
                    closeButton.style.borderRadius = '50%';
                    closeButton.style.width = '30px';
                    closeButton.style.height = '30px';
                    closeButton.style.fontSize = '18px';
                    closeButton.style.cursor = 'pointer';
                    closeButton.onclick = function() {
                        closeOverlay();
                    };

                    downloadContainer.appendChild(closeButton);
                    downloadContainer.appendChild(message);
                    downloadContainer.appendChild(downloadLink);
                    overlay.appendChild(downloadContainer);
                }

                // Add to document body
                document.body.appendChild(overlay);
            }
        });

        // Initialize compact calendar
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: 500,
            handleWindowResize: true,
            windowResizeDelay: 200,
            aspectRatio: 1.5,
            expandRows: true,
            events: [],
            eventClick: function(info) {
                const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);
                if (request) {
                    showEventModal(request);
                }
            },
            eventDidMount: function(info) {
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
            datesSet: function(info) {
                // Force refresh of calendar rendering
                calendar.updateSize();
            },
            viewDidMount: function(info) {
                // Ensure proper initial rendering
                setTimeout(() => calendar.updateSize(), 0);
            },
            eventTimeFormat: { // like '14:30:00'
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

        // Ensure calendar is properly rendered after DOM content is loaded
        setTimeout(() => {
            calendar.render();
            calendar.updateSize();
        }, 0);

        // Function to show event details in modal
        function showEventModal(request) {
            document.getElementById('eventModalTitle').textContent = request.form_details.calendar_info.title;
            document.getElementById('modalRequester').textContent = `${request.user_details.first_name} ${request.user_details.last_name}`;
            document.getElementById('modalPurpose').textContent = request.form_details.purpose;
            document.getElementById('modalParticipants').textContent = request.form_details.num_participants;
            document.getElementById('modalStatus').textContent = request.form_details.status.name;
            document.getElementById('modalFee').textContent = `₱${request.fees.tentative_fee}`;
            document.getElementById('modalApprovals').textContent = `${request.approvals.count}/3`;

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
            document.getElementById('modalViewDetails').onclick = function() {
                window.location.href = `/admin/requisition/${request.request_id}`;
            };

            eventModal.show();
        }

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

                // Update request ID in title
                document.getElementById('requestIdTitle').textContent = requestId;

                // Update calendar with all events
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
                                                ${f.is_waived ? 'checked' : ''}
                                                onchange="handleWaiverChange(this)">
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
                                                ${e.is_waived ? 'checked' : ''}
                                                onchange="handleWaiverChange(this)">
                                        </div>
                                        <span class="item-name">• ${e.name}</span>
                                    </div>
                                    <span class="item-price">₱${e.fee}</span>
                                </div>`
                            ).join('') : '<p>No equipment requested</p>'}
                    </div>
                    <div class="d-flex justify-content-end mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="waiveAllCheckbox" 
                                ${request.requested_items.facilities.every(f => f.is_waived) &&
                                request.requested_items.equipment.every(e => e.is_waived) ? 'checked' : ''}
                                onchange="handleWaiveAll(this)">
                            <label class="form-check-label" for="waiveAllCheckbox">
                                Waive All Fees
                            </label>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <strong>Tentative Fee:</strong>
                        <span>₱${request.fees.tentative_fee}</span>
                    </div>
                `;

                // Update form status
                document.getElementById('formStatus').innerHTML = `
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Approved Fee:</strong> ${request.fees.approved_fee ? `₱${request.fees.approved_fee}` : 'Pending'}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Late Penalty:</strong> ${request.fees.late_penalty_fee ? `₱${request.fees.late_penalty_fee}` : 'N/A'}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Approvals:</strong> ${request.approvals.count}/3</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Status:</strong> ${request.form_details.status.name}</p>
                        </div>
                    </div>
                `;

                // Clear additional fees section (leaving empty for now)
                document.getElementById('additionalFees').innerHTML = '';

                // Set up approve/reject buttons
                document.getElementById('approveBtn').addEventListener('click', () => approveRequest(requestId));
                document.getElementById('rejectBtn').addEventListener('click', () => rejectRequest(requestId));

                // After successful data fetch and updates, show content and hide loading state
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('contentState').style.display = 'block';

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

        function formatDateTime(schedule) {
            const startDate = new Date(schedule.start_date + 'T' + schedule.start_time);
            const endDate = new Date(schedule.end_date + 'T' + schedule.end_time);
            return `${startDate.toLocaleString()} to ${endDate.toLocaleString()}`;
        }

        function getStatusColor(status) {
            const colors = {
                'Pending Approval': 'warning',
                'In Review': 'info',
                'Awaiting Payment': 'primary',
                'Scheduled': 'success',
                'Rejected': 'danger',
                'Cancelled': 'secondary'
            };
            return colors[status] || 'secondary';
        }

        fetchRequestDetails();
    });
</script>
            @endsection