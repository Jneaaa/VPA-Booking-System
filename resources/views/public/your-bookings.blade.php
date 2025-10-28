@extends('layouts.app')

@section('title', 'Booking Catalog - Facilities & Equipment')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
    <style>
        .main-content {
            min-height: 100vh;
            background-image: url('{{ asset('assets/homepage.jpg') }}');
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
            padding: 2rem 0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content-wrapper {
            position: relative;
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .1);
            max-width: 1000px;
            width: 90%;
            margin: 0 auto;
        }

        .content-wrapper h2 {
            color: #333;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .lookup-form .input-group {
            margin-bottom: 1.5rem;
        }

        .lookup-form .form-control {
            border-radius: 0.25rem 0 0 0.25rem;
            height: calc(2.25rem + 2px);
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .lookup-form .btn-primary {
            background-color: #041A4B;
            border-color: #041A4B;
            color: white;
            font-weight: bold;
            border-radius: 0 0.25rem 0.25rem
        }

        .lookup-form .btn-primary:hover {
            background-color: #002c6b;
            border-color: #002c6b;
        }

        .no-requisition-message {
            color: #777;
            text-align: center;
            font-style: italic;
            margin-top: 1.5rem;
        }

        .requisition-list {
            margin-top: 2rem;
        }

  /* Custom Status Badges */
.status-badge {
    padding: 0.2em 0.8em;
    border-radius: 0.5rem;
    font-weight: bold;
    font-size: 0.85rem;
    min-width: 80px;
    text-align: center;
    border: 1px solid rgba(0, 0, 0, 0.1);
    letter-spacing: 0.5px;
}

.status-badge.pending-approval {
    background-color: #707485;
    color: #ffffff;
    border-color: #5a5e6e;
}

.status-badge.awaiting-payment {
    background-color: #1c5b8f;
    color: #ffffff;
    border-color: #164a7a;
}

.status-badge.scheduled {
    background-color: #1e7941;
    color: #ffffff;
    border-color: #186235;
}

.status-badge.ongoing {
    background-color: #ac7a0f;
    color: #ffffff;
    border-color: #8c640c;
}

.status-badge.late {
    background-color: #8f2a2a;
    color: #ffffff;
    border-color: #752222;
}

.status-badge.returned,
.status-badge.late-return,
.status-badge.completed,
.status-badge.rejected,
.status-badge.cancelled {
    background-color: #3e5568;
    color: #ffffff;
    border-color: #324556;
}


        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            padding: 0.75rem 1.25rem;
        }

        .request-id {
            font-weight: bold;
            color: #041A4B;
        }

        .no-items {
            font-style: italic;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .card-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            background-color: #f8f9fa;
            padding: 0.75rem 1.25rem;
        }

        .card-body {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .left-column {
            flex: 1;
            min-width: 300px;
            padding-right: 1rem;
        }

        .right-column {
            flex: 1;
            min-width: 300px;
            border-left: 1px solid #dee2e6;
            padding-left: 1rem;
        }


        /* Fee breakdown styles */
        .fee-breakdown {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
        }

        .fee-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .fee-item:last-child {
            border-bottom: none;
        }

        .fee-section {
            margin-bottom: 1rem;
        }

        .fee-section h6 {
            color: #041A4B;
            margin-bottom: 0.5rem;
        }

        .total-fee {
            font-weight: bold;
            font-size: 1.1rem;
            color: #198754;
            border-top: 2px solid #dee2e6;
            padding-top: 0.75rem;
            margin-top: 0.75rem;
        }

        @media (max-width: 768px) {
            .card-body {
                flex-direction: column;
            }

            .left-column,
            .right-column {
                padding: 0;
                border-left: none;
            }

            .right-column {
                border-top: 1px solid #dee2e6;
                padding-top: 1rem;
                margin-top: 1rem;
            }

            /* Bigger text size */
            .dropdown-menu .dropdown-item {
                font-size: 1rem;
            }

            /* Remove background outline */
            .dropdown-menu .dropdown-item:focus {
                outline: none;
                box-shadow: none;
            }

            /* Text + arrow color (default state) */
            .dropdown-toggle {
                color: #000;
                border: none;
                background: none;
            }

            /* Change arrow color to black */
            .dropdown-toggle::after {
                border-top-color: #000;
            }

            /* Hover state */
            .dropdown-menu .dropdown-item:hover {
                background-color: #f0f0f0;
                color: #000;
            }
        }
        /* Loading Spinner Styles */
.loading-spinner {
    display: none;
    text-align: center;
    padding: 2rem;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #041A4B;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: #041A4B;
    font-weight: bold;
}

/* Disable button during loading */
.btn-loading {
    opacity: 0.6;
    pointer-events: none;
}
    </style>

    <main class="main-content">
        <div class="content-wrapper">
            <h2>Requisition Form Lookup</h2>

<div id="lookupSection" class="lookup-form">
    <div class="input-group">
        <input type="text" class="form-control" id="referenceInput" placeholder="Enter reference code..."
            aria-label="Reference code">
        <button class="btn btn-primary" type="button" id="searchButton" onclick="showResults()">Search</button>
    </div>
    <div id="loadingSpinner" class="loading-spinner">
        <div class="spinner"></div>
        <p class="loading-text">Searching for your requisition...</p>
    </div>
    <p id="noResultsMessage" class="no-requisition-message">No requisition forms found. Please check your
        reference code and try again.</p>
</div>

<div id="resultsSection" style="display: none;">
    <div class="lookup-form">
        <div class="input-group">
            <input type="text" class="form-control" id="resultsReferenceInput" 
                   aria-label="Reference code" placeholder="Enter reference code...">
            <button class="btn btn-primary" type="button" id="resultsSearchButton" onclick="showResults()">Search</button>
        </div>
        <!-- Add loading spinner for results section too -->
        <div id="resultsLoadingSpinner" class="loading-spinner" style="display: none;">
            <div class="spinner"></div>
            <p class="loading-text">Searching for your requisition...</p>
        </div>
    </div>
</div>



                <div class="requisition-list">
                    <!-- Cards will be dynamically inserted here by JavaScript -->
                </div>
                <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to cancel this request? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep
                                    Request</button>
                                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel
                                    Request</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Receipt Modal -->
                <div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-labelledby="uploadReceiptModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadReceiptModalLabel">Upload Payment Receipt</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="uploadArea" class="border-dashed p-4 text-center"
                                    style="border: 2px dashed #ccc; border-radius: 5px; cursor: pointer;">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                    <p>Drag & drop your receipt here or click to browse</p>
                                    <p class="small text-muted">Supported formats: JPG, PNG, PDF (Max: 5MB)</p>
                                </div>
                                <input type="file" id="receiptFile" accept=".jpg,.jpeg,.png,.pdf" style="display: none;">

                                <div id="uploadPreview" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file"></i>
                                        <span id="fileName"></span>
                                        <button type="button" class="btn-close float-end"
                                            onclick="clearFileSelection()"></button>
                                    </div>
                                </div>

                                <div id="uploadProgress" class="progress mt-3" style="display: none;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                        aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>

                                <div id="uploadError" class="alert alert-danger mt-3" style="display: none;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmUploadBtn" disabled
                                    onclick="uploadReceipt()">Upload Receipt</button>
                            </div>
                        </div>
                    </div>
                </div>


    </main>

@endsection

@section('scripts')
<script>
    // Cloudinary configuration
    const cloudName = 'dn98ntlkd';
    const uploadPreset = 'payment-receipts';
    let selectedFile = null;
    let currentRequestId = null;

    // Initialize upload area
    document.addEventListener('DOMContentLoaded', function () {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('receiptFile');

        uploadArea.addEventListener('click', function () {
            fileInput.click();
        });

        uploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#007bff';
        });

        uploadArea.addEventListener('dragleave', function () {
            uploadArea.style.borderColor = '#ccc';
        });

        uploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#ccc';

            if (e.dataTransfer.files.length) {
                handleFileSelection(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', function () {
            if (this.files.length) {
                handleFileSelection(this.files[0]);
            }
        });
    });

    function handleFileSelection(file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            showUploadError('Invalid file type. Please upload JPG, PNG, or PDF files only.');
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showUploadError('File too large. Maximum size is 5MB.');
            return;
        }

        selectedFile = file;

        // Show file preview
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('uploadPreview').style.display = 'block';
        document.getElementById('confirmUploadBtn').disabled = false;
        document.getElementById('uploadError').style.display = 'none';
    }

    function clearFileSelection() {
        selectedFile = null;
        document.getElementById('receiptFile').value = '';
        document.getElementById('uploadPreview').style.display = 'none';
        document.getElementById('confirmUploadBtn').disabled = true;
    }

    function showUploadError(message) {
        const errorDiv = document.getElementById('uploadError');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
    }

    function showUploadModal(requestId) {
        currentRequestId = requestId;
        clearFileSelection();
        const modal = new bootstrap.Modal(document.getElementById('uploadReceiptModal'));
        modal.show();
    }

    function uploadReceipt() {
        if (!selectedFile || !currentRequestId) {
            showUploadError('Please select a file to upload.');
            return;
        }

        const progressBar = document.querySelector('#uploadProgress .progress-bar');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        document.getElementById('uploadProgress').style.display = 'block';
        document.getElementById('confirmUploadBtn').disabled = true;

        // Create form data for Cloudinary upload
        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('upload_preset', uploadPreset);
        formData.append('cloud_name', cloudName);

        // Upload to Cloudinary
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressBar.textContent = percent + '%';
            }
        });

        xhr.addEventListener('load', function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                // Send to our server to save to database
                fetch(`/api/requester/requisition/${currentRequestId}/upload-receipt`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        receipt_url: response.secure_url,
                        public_id: response.public_id
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal and refresh page
                            bootstrap.Modal.getInstance(document.getElementById('uploadReceiptModal')).hide();
                            alert('Receipt uploaded successfully!');
                            location.reload();
                        } else {
                            showUploadError('Failed to save receipt details: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error saving receipt:', error);
                        showUploadError('Failed to save receipt details. Please try again.');
                    });
            } else {
                showUploadError('Upload failed. Please try again.');
            }

            document.getElementById('uploadProgress').style.display = 'none';
        });

        xhr.addEventListener('error', function () {
            showUploadError('Upload failed. Please check your connection and try again.');
            document.getElementById('uploadProgress').style.display = 'none';
        });

        xhr.open('POST', `https://api.cloudinary.com/v1_1/${cloudName}/auto/upload`);
        xhr.send(formData);
    }

    let allForms = [];
    let statuses = [];

    // Fetch statuses from API
    async function fetchStatuses() {
        try {
            const response = await fetch('http://127.0.0.1:8000/api/form-statuses');
            const data = await response.json();

            // Filter out unwanted statuses
            statuses = data.filter(status =>
                status.status_name !== 'Returned' &&
                status.status_name !== 'Late Return'
            );

            // Update dropdown menu
            const dropdownMenu = document.querySelector('#resultsSection .dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.innerHTML = `
                                <li><a class="dropdown-item" href="#" data-status="all">All</a></li>
                                ${statuses.map(status => `
                                    <li><a class="dropdown-item" href="#" data-status="${status.status_name.toLowerCase().replace(/\s+/g, '-')}">${status.status_name}</a></li>
                                `).join('')}
                            `;
            }

        } catch (error) {
            console.error('Failed to fetch statuses:', error);
        }
    }

function showResults() {
    // Get the active search input (whichever is visible or has value)
    let searchInput;
    
    const resultsSection = document.getElementById('resultsSection');
    if (resultsSection && resultsSection.style.display !== 'none') {
        // Results are showing, use the results section input
        searchInput = document.querySelector('#resultsSection .form-control');
    } else {
        // Initial lookup, use the original input
        searchInput = document.getElementById('referenceInput');
    }
    
    if (!searchInput || !searchInput.value.trim()) {
        alert('Please enter a reference code');
        return;
    }

    const referenceInput = searchInput.value.trim();
    
    // Show loading animation
    showLoading();
    
    fetchFormByAccessCode(referenceInput);
}

function showLoading() {
    const resultsSection = document.getElementById('resultsSection');
    const isResultsVisible = resultsSection && resultsSection.style.display !== 'none';
    
    // Clear previous results immediately when starting new search
    const requisitionList = document.querySelector('.requisition-list');
    if (requisitionList) {
        requisitionList.innerHTML = '';
    }
    
    if (isResultsVisible) {
        // Results section is visible
        const loadingSpinner = document.getElementById('resultsLoadingSpinner');
        const searchButton = document.getElementById('resultsSearchButton');
        
        if (loadingSpinner) loadingSpinner.style.display = 'block';
        if (searchButton) {
            searchButton.classList.add('btn-loading');
            searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
        }
    } else {
        // Initial lookup section
        const loadingSpinner = document.getElementById('loadingSpinner');
        const searchButton = document.getElementById('searchButton');
        const noResultsMessage = document.getElementById('noResultsMessage');
        
        if (loadingSpinner) loadingSpinner.style.display = 'block';
        if (searchButton) {
            searchButton.classList.add('btn-loading');
            searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
        }
        if (noResultsMessage) noResultsMessage.style.display = 'none';
    }
}

function hideLoading() {
    // Hide both loading spinners and reset both buttons
    const loadingSpinner1 = document.getElementById('loadingSpinner');
    const loadingSpinner2 = document.getElementById('resultsLoadingSpinner');
    const searchButton1 = document.getElementById('searchButton');
    const searchButton2 = document.getElementById('resultsSearchButton');
    
    if (loadingSpinner1) loadingSpinner1.style.display = 'none';
    if (loadingSpinner2) loadingSpinner2.style.display = 'none';
    
    if (searchButton1) {
        searchButton1.classList.remove('btn-loading');
        searchButton1.innerHTML = 'Search';
    }
    if (searchButton2) {
        searchButton2.classList.remove('btn-loading');
        searchButton2.innerHTML = 'Search';
    }
}

  async function fetchFormByAccessCode(accessCode) {
    try {
        const response = await fetch(`/api/requester/form/${accessCode}`);
        if (!response.ok) throw new Error('Form not found');

        const form = await response.json();

        // Check if the response structure is correct
        if (!form.form_status || !form.purpose) {
            console.error('Invalid form structure:', form);
            throw new Error('Invalid form data structure');
        }

        allForms = [form];
        displayForms();

        document.getElementById('lookupSection').style.display = 'none';
        document.getElementById('resultsSection').style.display = 'block';
        document.getElementById('noResultsMessage').style.display = 'none';

    } catch (error) {
        console.error('Error fetching form:', error);
        document.getElementById('noResultsMessage').style.display = 'block';
    } finally {
        // Always hide loading animation whether successful or not
        hideLoading();
    }
}

    async function fetchFormsByEmail(email) {
        try {
            const response = await fetch(`/api/requester/forms/${email}`);
            if (!response.ok) throw new Error('No forms found');

            const forms = await response.json();
            if (forms.length === 0) throw new Error('No forms found');

            // Fetch details for each form
            const formDetails = [];
            for (const form of forms) {
                try {
                    const detailResponse = await fetch(`/api/requester/form/${form.access_code}`);
                    if (detailResponse.ok) {
                        const detail = await detailResponse.json();
                        // Validate the detail structure
                        if (detail.form_details && detail.form_details.status) {
                            formDetails.push(detail);
                        } else {
                            console.warn('Skipping invalid form detail:', detail);
                        }
                    }
                } catch (detailError) {
                    console.warn('Failed to fetch form detail:', detailError);
                }
            }

            if (formDetails.length === 0) throw new Error('No valid forms found');

            allForms = formDetails;
            displayForms();

            document.getElementById('lookupSection').style.display = 'none';
            document.getElementById('resultsSection').style.display = 'block';
            document.getElementById('noResultsMessage').style.display = 'none';

        } catch (error) {
            console.error('Error fetching forms:', error);
            document.getElementById('noResultsMessage').style.display = 'block';
        }
    }



    // Function to calculate total fee based on form data
    function calculateTotalFee(form) {
        console.log('Form data for fee calculation:', form); // Debug log

        // First priority: Use total_fee from requester API
        if (form.total_fee && parseFloat(form.total_fee) > 0) {
            return parseFloat(form.total_fee);
        }

        // Second priority: Use approved_fee from admin API structure
        if (form.fees && form.fees.approved_fee) {
            return parseFloat(form.fees.approved_fee);
        }

        // Fallback: Calculate manually (this won't work for requester API since fee data is missing)
        let totalFee = 0;

        // Calculate facility fees - this requires external_fee data which isn't in requester API
        if (form.requested_facilities && form.requested_facilities.length > 0) {
            console.warn('Cannot calculate facility fees - external_fee data missing');
        }

        // Calculate equipment fees - this requires external_fee data which isn't in requester API
        if (form.requested_equipment && form.requested_equipment.length > 0) {
            console.warn('Cannot calculate equipment fees - external_fee data missing');
        }

        // Add late penalty if applicable
        if (form.is_late && form.late_penalty_fee) {
            totalFee += parseFloat(form.late_penalty_fee);
        }

        return totalFee;
    }


    // Function to calculate duration in hours
    function calculateDurationHours(startDate, startTime, endDate, endTime) {
        const startDateTime = new Date(`${startDate}T${convertTo24Hour(startTime)}:00`);
        const endDateTime = new Date(`${endDate}T${convertTo24Hour(endTime)}:00`);
        const durationHours = (endDateTime - startDateTime) / (1000 * 60 * 60);
        return Math.max(0, durationHours);
    }

    // Function to convert 12-hour time to 24-hour time (same as in reservation form)
    function convertTo24Hour(time12h) {
        if (!time12h) return '';

        if (time12h.includes(':')) {
            const [timePart, modifier] = time12h.split(' ');
            if (!modifier) return timePart;

            let [hours, minutes] = timePart.split(':');
            hours = parseInt(hours, 10);

            if (modifier === 'PM' && hours !== 12) {
                hours += 12;
            } else if (modifier === 'AM' && hours === 12) {
                hours = 0;
            }

            return `${hours.toString().padStart(2, '0')}:${minutes}`;
        }

        return time12h;
    }

    // Function to generate fee breakdown HTML
    function generateFeeBreakdown(form) {
        const durationHours = calculateDurationHours(form.start_date, form.start_time, form.end_date, form.end_time);

        let facilityTotal = 0;
        let equipmentTotal = 0;
        let htmlContent = '';

        // Facilities breakdown
        const facilityItems = form.requested_facilities || [];
        if (facilityItems.length > 0) {
            htmlContent += '<div class="fee-section"><h6 class="text-primary">Facilities</h6>';
            facilityItems.forEach(item => {
                let fee = parseFloat(item.external_fee || 0);
                if (item.rate_type === 'Per Hour' && durationHours > 0) {
                    fee = fee * durationHours;
                    htmlContent += `
                            <div class="fee-item">
                                <span>${item.facility_name} (${durationHours.toFixed(1)} hrs)</span>
                                <div class="text-end">
                                    <small>₱${parseFloat(item.external_fee).toLocaleString()}/hr</small>
                                    <div><strong>₱${fee.toLocaleString()}</strong></div>
                                </div>
                            </div>
                        `;
                } else {
                    htmlContent += `
                            <div class="fee-item">
                                <span>${item.facility_name}</span>
                                <span>₱${fee.toLocaleString()}</span>
                            </div>
                        `;
                }
                facilityTotal += fee;
            });
            htmlContent += `
                    <div class="fee-item subtotal">
                        <strong>Subtotal</strong>
                        <strong>₱${facilityTotal.toLocaleString()}</strong>
                    </div>
                </div>`;
        }

        // Equipment breakdown
        const equipmentItems = form.requested_equipment || [];
        if (equipmentItems.length > 0) {
            htmlContent += '<div class="fee-section mt-3"><h6 class="text-primary">Equipment</h6>';
            equipmentItems.forEach(item => {
                let unitFee = parseFloat(item.external_fee || 0);
                const quantity = item.quantity || 1;
                let itemTotal = unitFee * quantity;
                if (item.rate_type === 'Per Hour' && durationHours > 0) {
                    itemTotal = itemTotal * durationHours;
                    htmlContent += `
                            <div class="fee-item">
                                <span>${item.equipment_name} ${quantity > 1 ? `(x${quantity})` : ''} (${durationHours.toFixed(1)} hrs)</span>
                                <div class="text-end">
                                    <small>₱${unitFee.toLocaleString()}/hr × ${quantity}</small>
                                    <div><strong>₱${itemTotal.toLocaleString()}</strong></div>
                                </div>
                            </div>
                        `;
                } else {
                    htmlContent += `
                            <div class="fee-item">
                                <span>${item.equipment_name} ${quantity > 1 ? `(x${quantity})` : ''}</span>
                                <div class="text-end">
                                    <div>₱${unitFee.toLocaleString()} × ${quantity}</div>
                                    <strong>₱${itemTotal.toLocaleString()}</strong>
                                </div>
                            </div>
                        `;
                }
                equipmentTotal += itemTotal;
            });
            htmlContent += `
                    <div class="fee-item subtotal">
                        <strong>Subtotal</strong>
                        <strong>₱${equipmentTotal.toLocaleString()}</strong>
                    </div>
                </div>`;
        }

        // Total
        const total = facilityTotal + equipmentTotal;
        if (total > 0) {
            htmlContent += `
                    <div class="fee-item total-fee">
                        <strong>Total Amount</strong>
                        <strong>₱${total.toLocaleString()}</strong>
                    </div>
                `;
        }

        return {
            html: htmlContent,
            total: total
        };
    }

    function displayForms() {
        const requisitionList = document.querySelector('.requisition-list');
        if (!requisitionList) return;

        requisitionList.innerHTML = '';

        allForms.forEach(form => {
            // Update the safety check to match the new structure
            if (!form.form_status || !form.purpose) {
                console.error('Invalid form structure:', form);
                return;
            }

            const statusClass = form.form_status.status_name.toLowerCase().replace(/\s+/g, '-');
            const statusName = form.form_status.status_name;

            // Calculate total fee
            const totalFee = calculateTotalFee(form);

            let footerButtons = '';

            if (['Pending Approval', 'Scheduled'].includes(statusName)) {
                footerButtons = `
                        <div class="card-footer">
                            <button class="btn btn-sm btn-danger" onclick="showCancelModal(${form.request_id})">Cancel Request</button>
                        </div>
                    `;
            } else if (statusName === 'Awaiting Payment') {
                footerButtons = `
                        <div class="card-footer">
                            <button class="btn btn-sm btn-success" onclick="showUploadModal(${form.request_id})">Upload Receipt</button>
                            <button class="btn btn-sm btn-danger" onclick="showCancelModal(${form.request_id})">Cancel Request</button>
                        </div>
                    `;
            } else {
                footerButtons = '<div class="card-footer"></div>';
            }

            const facilitiesList = form.requested_facilities && form.requested_facilities.length > 0
                ? form.requested_facilities.map(f => `<li>${f.facility_name}</li>`).join('')
                : '<p class="no-items mb-0">No facilities requested</p>';

            const equipmentList = form.requested_equipment && form.requested_equipment.length > 0
                ? form.requested_equipment.map(e => `<li>${e.equipment_name}</li>`).join('')
                : '<p class="no-items mb-0">No equipment requested</p>';

            const purpose = form.purpose.purpose_name || 'No purpose specified';

            // Format dates properly
            const formatDate = (dateString) => {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            };

            // Format time properly (remove seconds if present)
            const formatTime = (timeString) => {
                return timeString.substring(0, 5); // Get only HH:MM
            };

            const card = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <span class="request-id">Request ID #${form.request_id.toString().padStart(4, '0')}</span>
                            <span class="status-badge ${statusClass}">${statusName}</span>
                        </div>
                        <div class="card-body">
<div class="left-column">
  <p class="mb-1"><strong>Purpose:</strong> ${purpose}</p>
  <p class="mb-1"><strong>Start Schedule:</strong> ${formatDate(form.start_date)}, ${formatTime(form.start_time)}</p>
  <p class="mb-1"><strong>End Schedule:</strong> ${formatDate(form.end_date)}, ${formatTime(form.end_time)}</p>
  <p class="mb-0"><strong>Total Fee:</strong> ₱${totalFee.toLocaleString('en-PH', { 
    minimumFractionDigits: 2, 
    maximumFractionDigits: 2 
  })}</p>
</div>
<div class="right-column">
  <h6 class="fw-bold">Request Details</h6>
  <p class="mb-1"><strong>Facilities:</strong></p>
  <ul class="mb-2">${facilitiesList}</ul>
  <p class="mb-1"><strong>Equipment:</strong></p>
  <ul class="mb-0">${equipmentList}</ul>
</div>

                        </div>
                        ${footerButtons}
                    </div>
                `;

            requisitionList.innerHTML += card;
        });
    }

    function showCancelModal(requestId) {
        currentRequestId = requestId;
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        cancelModal.show();
    }

    async function cancelRequest() {
        if (!currentRequestId) return;

        try {
            const response = await fetch(`/api/requester/requisition/${currentRequestId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (response.ok) {
                alert('Request cancelled successfully');
                location.reload();
            } else {
                throw new Error(result.details || 'Failed to cancel request');
            }
        } catch (error) {
            console.error('Error cancelling request:', error);
            alert('Error: ' + error.message);
        } finally {
            currentRequestId = null;
        }
    }

    function filterRequisitions(status) {
        const cards = document.querySelectorAll('.requisition-list .card.mb-3');

        cards.forEach(card => {
            if (status === 'all') {
                card.style.display = 'block';
            } else {
                const statusBadge = card.querySelector('.status-badge');
                if (statusBadge) {
                    const badgeStatus = statusBadge.textContent.toLowerCase().replace(/\s+/g, '-');
                    if (badgeStatus === status.toLowerCase()) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        fetchStatuses();

        // Set up filter dropdown - use more specific selector
        const resultsSection = document.getElementById('resultsSection');
        if (resultsSection) {
            resultsSection.addEventListener('click', function (e) {
                if (e.target.classList.contains('dropdown-item')) {
                    e.preventDefault();
                    const status = e.target.getAttribute('data-status');
                    filterRequisitions(status);

                    const dropdownBtn = resultsSection.querySelector('.dropdown-toggle');
                    if (dropdownBtn && status === 'all') {
                        dropdownBtn.textContent = 'Filter by';
                    } else if (dropdownBtn) {
                        dropdownBtn.textContent = 'Filter: ' + e.target.textContent;
                    }
                }
            });
        }

        // Set up cancel confirmation button
        const confirmCancelBtn = document.getElementById('confirmCancelBtn');
        if (confirmCancelBtn) {
            confirmCancelBtn.addEventListener('click', cancelRequest);
        }
    });
</script>
@endsection