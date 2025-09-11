@extends('layouts.admin')

@section('title', 'Edit Facility')

@section('content')
<style>
    /* Toast notification styles */
    .toast {
        z-index: 1100;
        bottom: 0;
        left: 0;
        margin: 1rem;
        opacity: 0;
        transform: translateY(20px);
        transition: transform 0.4s ease, opacity 0.4s ease;
        min-width: 250px;
        border-radius: 0.3rem;
    }

    .toast .loading-bar {
        height: 3px;
        background: rgba(255, 255, 255, 0.7);
        width: 100%;
        transition: width 3000ms linear;
    }

    input[readonly],
    textarea[readonly] {
        pointer-events: none;
        /* disables clicking/selection */
        user-select: none;
        /* prevents highlighting */
        background-color: #fff;
        /* optional: removes gray "disabled" look */
        cursor: default;
        /* arrow cursor instead of I-beam */
    }

    /* Add this to your existing styles */
    #photosPreview {
        min-height: 110px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .photo-preview {
        position: relative;
        width: 100px;
        height: 100px;
    }

    /* Add sharp edges to all elements */
    * {
        border-radius: 0 !important;
    }

    /* Layout fixes */
    .dropzone {
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .card-body {
        padding: 1.5rem;
    }

    #photosPreview {
        min-height: 110px;
    }

    .form-control,
    .form-select {
        padding: 0.5rem 0.75rem;
    }

    textarea.form-control {
        min-height: 100px;
    }

    .row.mb-4 {
        margin-bottom: 1.5rem !important;
    }

    #itemsContainer {
        min-height: 100px;
    }

    .amenity-item,
    .facility-item {
        margin-bottom: 0.75rem;
    }

    .modal-body .dropzone {
        min-height: 150px;
    }

    .photo-container {
        position: relative;
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
    }

    .change-photo-btn {
        z-index: 1;
    }

    .facility-item img {
        max-width: 100px;
        /* Restrict photo width */
        max-height: 100px;
        /* Restrict photo height */
        object-fit: cover;
        /* Ensure photo fits within bounds */
    }

    .facility-item .card-body {
        display: flex;
        align-items: center;
        gap: 1rem;
        /* Add spacing between elements */
    }

    .facility-item .flex-grow-1 {
        flex: 1;
        /* Allow details section to take remaining space */
    }
</style>
<div class="container-fluid px-4">
    <!-- Main Layout -->
    <div id="layout">
        <!-- Main Content -->
        <main id="main">
            <!-- Edit Facility Page -->

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Edit Facility</h1>
                <a href="{{ url('/admin/manage-facilities') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Facilities
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="editFacilityForm">
                        <input type="hidden" id="facilityId" value="{{ request()->get('id') }}">

                        <!-- Facility Photos and Details Section -->
                        <div class="row mb-4">
                            <!-- Facility Photos Card -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center"
                                        style="height: 56px;">
                                        <h5 class="fw-bold mb-0">Facility Photos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="photo-section">
                                            <div class="dropzone border p-4 text-center" id="facilityPhotosDropzone"
                                                style="cursor: pointer;">
                                                <i class="bi bi-images fs-1 text-muted"></i>
                                                <p class="mt-2">Drag & drop facility photos here or click to browse</p>
                                                <input type="file" id="facilityPhotos" class="d-none" multiple
                                                    accept="image/*">
                                            </div>
                                            <small class="text-muted mt-2 d-block">Upload at least one photo of the
                                                facility (max 5
                                                photos)</small>
                                            <div id="photosPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Image Deletion Confirmation Modal -->
                            <div class="modal fade" id="deleteImageModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Image Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this photo? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-danger"
                                                id="confirmDeleteImageBtn">Delete Photo</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                              <!-- Facility Details Section -->
                        <div class="col-md-6">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="fw-bold mb-0">Facility Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="details-section">
                                            <!-- Basic Information Section -->
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label for="facilityName"
                                                        class="form-label fw-bold d-flex align-items-center">
                                                        Facility Name
                                                        <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                                            data-field="facilityName" style="cursor: pointer;"></i>
                                                        <div class="edit-actions ms-2 d-none" data-field="facilityName">
                                                            <button type="button"
                                                                class="btn btn-sm btn-success me-1 save-btn">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger cancel-btn">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </label>
                                                    <input type="text" class="form-control text-secondary"
                                                        id="facilityName" value="" readonly>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="buildingCode"
                                                        class="form-label fw-bold d-flex align-items-center">
                                                        Building Code
                                                        <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                                            data-field="buildingCode" style="cursor: pointer;"></i>
                                                        <div class="edit-actions ms-2 d-none" data-field="buildingCode">
                                                            <button type="button"
                                                                class="btn btn-sm btn-success me-1 save-btn">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger cancel-btn">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </label>
                                                    <input type="text" class="form-control text-secondary"
                                                        id="buildingCode" value="" readonly>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-12 position-relative">
                                                    <label for="description"
                                                        class="form-label fw-bold d-flex align-items-center">
                                                        Description
                                                        <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                                            data-field="description" style="cursor: pointer;"></i>
                                                        <div class="edit-actions ms-2 d-none" data-field="description">
                                                            <button type="button"
                                                                class="btn btn-sm btn-success me-1 save-btn">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger cancel-btn">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </label>
                                                    <textarea class="form-control text-secondary" id="description"
                                                        rows="3" readonly></textarea>
                                                    <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1"
                                                        id="descriptionWordCount">0/250 characters</small>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <label for="locationNote"
                                                        class="form-label fw-bold d-flex align-items-center">
                                                        Location Note
                                                        <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                                            data-field="locationNote" style="cursor: pointer;"></i>
                                                        <div class="edit-actions ms-2 d-none" data-field="locationNote">
                                                            <button type="button"
                                                                class="btn btn-sm btn-success me-1 save-btn">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger cancel-btn">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </label>
                                                    <input type="text" class="form-control text-secondary"
                                                        id="locationNote" value="" readonly>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="category" class="form-label fw-bold">Category</label>
                                                    <select class="form-select" id="category" required>
                                                        <option value="">Select Category</option>
                                                        <!-- Categories will be populated dynamically -->
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="subcategory"
                                                        class="form-label fw-bold">Subcategory</label>
                                                    <select class="form-select" id="subcategory">
                                                        <option value="">Select Subcategory</option>
                                                        <!-- Subcategories will be populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Capacity & Location Section -->
                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <label for="capacity" class="form-label fw-bold">Capacity</label>
                                                    <input type="number" class="form-control" id="capacity" min="1"
                                                        value="1" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="locationType" class="form-label fw-bold">Location
                                                        Type</label>
                                                    <select class="form-select" id="locationType" required>
                                                        <option value="Indoors">Indoors</option>
                                                        <option value="Outdoors">Outdoors</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="floorLevel" class="form-label fw-bold">Floor
                                                        Level</label>
                                                    <input type="number" class="form-control" id="floorLevel" min="1"
                                                        placeholder="Floor level">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="roomCode" class="form-label fw-bold">Room Code</label>
                                                    <input type="text" class="form-control" id="roomCode"
                                                        placeholder="Room code">
                                                </div>
                                            </div>

                                            <!-- Pricing Section -->
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <label for="internalFee" class="form-label fw-bold">Internal Fee
                                                        (₱)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">₱</span>
                                                        <input type="number" class="form-control" id="internalFee"
                                                            min="0" step="0.01" required placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="externalFee" class="form-label fw-bold">External Fee
                                                        (₱)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">₱</span>
                                                        <input type="number" class="form-control" id="externalFee"
                                                            min="0" step="0.01" required placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="rateType" class="form-label fw-bold">Rate Type</label>
                                                    <select class="form-select" id="rateType" required>
                                                        <option value="Per Hour">Per Hour</option>
                                                        <option value="Per Event">Per Event</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Department & Availability Section -->
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <label for="department" class="form-label fw-bold">Owning
                                                        Department</label>
                                                    <select class="form-select" id="department" required>
                                                        <!-- Departments will be populated dynamically -->
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="maximumRentalHour" class="form-label fw-bold">Maximum
                                                        Rental Duration (hours)</label>
                                                    <input type="number" class="form-control" id="maximumRentalHour"
                                                        min="1" placeholder="Maximum rental hours">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="availabilityStatus"
                                                        class="form-label fw-bold">Availability
                                                        Status</label>
                                                    <select class="form-select" id="availabilityStatus" required>
                                                        <!-- Statuses will be populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Building Details Section -->
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <label for="totalLevels" class="form-label fw-bold">Total
                                                        Levels</label>
                                                    <input type="number" class="form-control" id="totalLevels" min="1"
                                                        placeholder="Total building levels">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="totalRooms" class="form-label fw-bold">Total
                                                        Rooms</label>
                                                    <input type="number" class="form-control" id="totalRooms" min="1"
                                                        placeholder="Total rooms in building">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="parentFacility" class="form-label fw-bold">Parent
                                                        Facility</label>
                                                    <select class="form-select" id="parentFacility">
                                                        <option value="">None</option>
                                                        <!-- Parent facilities will be populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Facility</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Title:</strong> <span id="eventTitle"></span>
                        </li>
                        <li class="list-group-item">
                            <strong>Date:</strong> <span id="eventDate"></span>
                        </li>
                        <li class="list-group-item">
                            <strong>Time:</strong> <span id="eventTime">10:00 AM - 12:00 PM</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Description:</strong> <span id="eventDescription"></span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Reset Confirmation Modal -->
    <div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Discard Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel? Unsaved changes will be lost.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Authentication check
                const token = localStorage.getItem('adminToken');
                if (!token) {
                    window.location.href = '/admin/admin-login';
                    return;
                }

                // Global helper function for toast notifications
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

                // Initialize the delete confirmation modal
                const deleteImageModal = new bootstrap.Modal('#deleteImageModal', {
                    backdrop: 'static',
                    keyboard: false
                });

                // Variables to track the image to be deleted
                let currentDeletePhotoId = null;
                let currentDeletePublicId = null;
                let currentDeletePreviewElement = null;

                // Function to handle image deletion with confirmation
                async function handleImageDeletion(photoId, publicId, previewElement) {
                    currentDeletePhotoId = photoId;
                    currentDeletePublicId = publicId;
                    currentDeletePreviewElement = previewElement;

                    // Show the confirmation modal
                    deleteImageModal.show();
                }

                // Handle confirm delete button click
                document.getElementById('confirmDeleteImageBtn').addEventListener('click', async function () {
                    try {
                        const facilityId = document.getElementById('facilityId').value;

                        // Delete from Cloudinary first if public ID exists
                        if (currentDeletePublicId) {
                            await deleteImageFromCloudinary(currentDeletePublicId);
                            showToast('Image deleted from storage successfully', 'success');
                        }

                        // Then delete from database if it's a saved image (has photoId)
                        if (currentDeletePhotoId && typeof currentDeletePhotoId === 'number') {
                            await deleteImage(facilityId, currentDeletePhotoId);
                            showToast('Image reference removed from database', 'success');
                        }

                        // Remove the preview element
                        if (currentDeletePreviewElement) {
                            currentDeletePreviewElement.remove();
                        }

                        // Update the uploadedPhotos array
                        uploadedPhotos = uploadedPhotos.filter(photo => photo.id !== currentDeletePhotoId);

                        // Hide the modal
                        deleteImageModal.hide();

                    } catch (error) {
                        console.error('Error deleting image:', error);
                        showToast('Failed to delete image: ' + error.message, 'error');
                    }
                });

                async function handleFacilityFiles(files) {
                    const facilityId = document.getElementById('facilityId').value;
                    const photosPreview = document.getElementById('photosPreview');

                    for (const file of files) {
                        // Check if file is an image
                        if (!file.type.startsWith('image/')) {
                            showToast('Please upload only image files', 'error');
                            continue;
                        }

                        // Check if we've reached the maximum of 5 photos
                        if (uploadedPhotos.length >= 5) {
                            showToast('Maximum of 5 photos allowed', 'error');
                            break;
                        }

                        try {
                            const previewId = Date.now(); // Define previewId here so it's accessible

                            // Create a preview
                            const reader = new FileReader();
                            reader.onload = async (e) => { // Make this async
                                const preview = document.createElement('div');
                                preview.className = 'photo-preview';
                                preview.dataset.id = previewId;

                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'img-thumbnail h-100 w-100 object-fit-cover';

                                const removeBtn = document.createElement('button');
                                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                                removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                                removeBtn.onclick = function () {
                                    preview.remove();
                                    uploadedPhotos = uploadedPhotos.filter(photo => photo.previewId !== previewId);
                                };

                                preview.appendChild(img);
                                preview.appendChild(removeBtn);
                                photosPreview.appendChild(preview);

                                // Store file and preview info
                                uploadedPhotos.push({
                                    file: file,
                                    previewId: previewId,
                                    previewElement: preview
                                });

                                try {
                                    // Upload to Cloudinary
                                    const result = await uploadToCloudinary(file, facilityId);

                                    // Update the preview with the actual image from Cloudinary
                                    if (result && result.secure_url) {
                                        img.src = result.secure_url;

                                        // Update the uploadedPhotos array with the Cloudinary data
                                        const photoIndex = uploadedPhotos.findIndex(photo => photo.previewId === previewId);
                                        if (photoIndex !== -1) {
                                            uploadedPhotos[photoIndex] = {
                                                ...uploadedPhotos[photoIndex],
                                                id: result.public_id,
                                                url: result.secure_url
                                            };
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error uploading to Cloudinary:', error);
                                    showToast('Upload failed: ' + error.message, 'error');
                                    // Remove the preview if upload fails
                                    preview.remove();
                                    uploadedPhotos = uploadedPhotos.filter(photo => photo.previewId !== previewId);
                                }
                            };
                            reader.readAsDataURL(file);

                        } catch (error) {
                            console.error('Error processing file:', error);
                            showToast('Failed to process file: ' + error.message, 'error');
                        }
                    }
                }

                // Cloudinary direct upload implementation
                async function uploadToCloudinary(file, facilityId) {
                    const CLOUD_NAME = 'dn98ntlkd'; // Your Cloudinary cloud name
                    const UPLOAD_PRESET = 'facility-photos'; // Your unsigned upload preset

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('upload_preset', UPLOAD_PRESET);
                    formData.append('folder', `facility-photos/${facilityId}`);
                    formData.append('tags', `facility_${facilityId}`);

                    try {
                        showToast('Uploading image to Cloudinary...', 'info', 3000);

                        const response = await fetch(`https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`, {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.error?.message || 'Upload failed');
                        }

                        const data = await response.json();
                        console.log('Cloudinary upload successful:', data);

                        // Now save the image reference to your database
                        await saveImageToDatabase(facilityId, data.secure_url, data.public_id);

                        showToast('Image uploaded to Cloudinary successfully!', 'success');
                        return data;

                    } catch (error) {
                        console.error('Cloudinary upload error:', error);
                        showToast('Cloudinary upload failed: ' + error.message, 'error');
                        throw error;
                    }
                }

                // Function to save image reference to your database
                async function saveImageToDatabase(facilityId, imageUrl, publicId) {
                    try {
                        const response = await fetch(`http://127.0.0.1:8000/api/admin/facilities/${facilityId}/images/save`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                image_url: imageUrl,
                                cloudinary_public_id: publicId,
                                description: 'Facility photo'
                            })
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Database save failed:', response.status, errorText);
                            throw new Error(`Failed to save image to database: ${response.status} ${errorText}`);
                        }

                        const result = await response.json();
                        console.log('Image saved to database:', result);
                        return result;

                    } catch (error) {
                        console.error('Error saving image to database:', error);
                        showToast('Warning: Image uploaded but database save failed', 'warning');
                        throw error; // Re-throw to handle in the calling function
                    }
                }

                async function deleteImageFromCloudinary(publicId) {
                    try {
                        const formData = new FormData();
                        formData.append('public_id', publicId);

                        const token = localStorage.getItem('adminToken');

                        // Updated endpoint to match your new route
                        const response = await fetch(`http://127.0.0.1:8000/api/admin/cloudinary/delete`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            throw new Error('Failed to delete image from Cloudinary');
                        }

                        showToast('Image deleted from storage', 'success');
                        return await response.json();
                    } catch (error) {
                        console.error('Error deleting image from Cloudinary:', error);
                        showToast('Failed to delete from storage: ' + error.message, 'error');
                        throw error;
                    }
                }

                async function deleteImage(facilityId, imageId, cloudinaryPublicId) {
                    try {
                        const token = localStorage.getItem('adminToken');

                        // 1. Delete from Cloudinary via your simple backend endpoint
                        if (cloudinaryPublicId) {
                            await fetch(`http://127.0.0.1:8000/api/admin/cloudinary/delete`, {
                                method: 'POST',
                                headers: {
                                    'Authorization': `Bearer ${token}`,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ public_id: cloudinaryPublicId })
                            });
                            showToast('Image deleted from storage', 'success');
                        }

                        // 2. Delete from your database
                        const response = await fetch(`http://127.0.0.1:8000/api/admin/facilities/${facilityId}/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to delete image from database');
                        }

                        showToast('Image reference deleted', 'success');

                    } catch (error) {
                        console.error('Error deleting image:', error);
                        showToast('Failed to delete image: ' + error.message, 'error');
                        throw error;
                    }
                }

                // Generic edit functionality for all fields
                document.querySelectorAll('.edit-icon').forEach(icon => {
                    const fieldId = icon.getAttribute('data-field');
                    const inputField = document.getElementById(fieldId);
                    const editActions = document.querySelector(`.edit-actions[data-field="${fieldId}"]`);
                    const saveBtn = editActions.querySelector('.save-btn');
                    const cancelBtn = editActions.querySelector('.cancel-btn');

                    let originalValue = inputField.value;
                    let originalSelectValue = inputField.tagName === 'SELECT' ? inputField.value : null;

                    // Enter edit mode
                    icon.addEventListener('click', () => {
                        inputField.removeAttribute('readonly');
                        inputField.classList.remove('text-secondary');
                        icon.classList.add('d-none');
                        editActions.classList.remove('d-none');

                        if (inputField.tagName === 'SELECT') {
                            originalSelectValue = inputField.value;
                            inputField.classList.remove('text-secondary');
                        } else {
                            originalValue = inputField.value;
                        }

                        inputField.focus();
                    });

                    // Save changes
                    saveBtn.addEventListener('click', () => {
                        inputField.setAttribute('readonly', true);
                        inputField.classList.add('text-secondary');
                        icon.classList.remove('d-none');
                        editActions.classList.add('d-none');

                        // TODO: send new value to backend here
                        console.log(`Saved ${fieldId}:`, inputField.value);
                    });

                    // Cancel changes
                    cancelBtn.addEventListener('click', () => {
                        if (inputField.tagName === 'SELECT') {
                            inputField.value = originalSelectValue;
                        } else {
                            inputField.value = originalValue;
                        }

                        inputField.setAttribute('readonly', true);
                        inputField.classList.add('text-secondary');
                        icon.classList.remove('d-none');
                        editActions.classList.add('d-none');
                    });
                });

                const facilityId = document.getElementById('facilityId').value;

                if (!facilityId) {
                    alert('No facility ID provided');
                    window.location.href = '/admin/manage-facilities';
                    return;
                }

                // Initialize the cancel confirmation modal
                const cancelConfirmationModal = new bootstrap.Modal('#cancelConfirmationModal', {
                    backdrop: 'static',
                    keyboard: false
                });

                // Handle cancel button click
                document.getElementById('cancelBtn').addEventListener('click', function (e) {
                    e.preventDefault();
                    cancelConfirmationModal.show();
                });

                // Handle confirm cancel button click
                document.getElementById('confirmCancelBtn').addEventListener('click', function () {
                    // Hide the modal first
                    cancelConfirmationModal.hide();

                    // Then redirect to manage-facilities
                    window.location.href = '/admin/manage-facilities';
                });

                // Facility Photos Section
                const facilityDropzone = document.getElementById('facilityPhotosDropzone');
                const facilityFileInput = document.getElementById('facilityPhotos');
                const photosPreview = document.getElementById('photosPreview');
                let uploadedPhotos = [];
                if (facilityDropzone && facilityFileInput) {
                    facilityDropzone.addEventListener('click', function () {
                        facilityFileInput.click();
                    });

                    facilityFileInput.addEventListener('change', function () {
                        handleFacilityFiles(this.files);
                        this.value = '';
                    });

                    facilityDropzone.addEventListener('dragover', function (e) {
                        e.preventDefault();
                        this.classList.add('border-primary');
                    });

                    facilityDropzone.addEventListener('dragleave', function () {
                        this.classList.remove('border-primary');
                    });

                    facilityDropzone.addEventListener('drop', function (e) {
                        e.preventDefault();
                        this.classList.remove('border-primary');
                        if (e.dataTransfer.files.length) {
                            handleFacilityFiles(e.dataTransfer.files);
                        }
                    });
                }

                // Word count limiter for Description textbox
                const description = document.getElementById('description');
                const descriptionWordCount = document.getElementById('descriptionWordCount');
                if (description && descriptionWordCount) {
                    description.addEventListener('input', function () {
                        const currentLength = this.value.length;
                        descriptionWordCount.textContent = `${currentLength}/250 characters`;
                        if (currentLength > 250) {
                            this.value = this.value.substring(0, 250);
                            descriptionWordCount.textContent = '250/250 characters';
                        }
                    });
                }

                // Fetch facility data
                async function fetchFacilityData() {
                    try {
                        const response = await fetch(`http://127.0.0.1:8000/api/admin/facilities/${facilityId}`, {
                            method: 'GET',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch facility data');
                        }

                        const result = await response.json();
                        const facility = result.data;

                        // Populate form fields
                        document.getElementById('facilityName').value = facility.facility_name || '';
                        document.getElementById('buildingCode').value = facility.building_code || '';
                        document.getElementById('description').value = facility.description || '';
                        document.getElementById('locationNote').value = facility.location_note || '';
                        document.getElementById('capacity').value = facility.capacity || 1;
                        document.getElementById('locationType').value = facility.location_type || 'Indoors';
                        document.getElementById('floorLevel').value = facility.floor_level || '';
                        document.getElementById('roomCode').value = facility.room_code || '';
                        document.getElementById('internalFee').value = facility.internal_fee || '0.00';
                        document.getElementById('externalFee').value = facility.external_fee || '0.00';
                        document.getElementById('rateType').value = facility.rate_type || 'Per Hour';
                        document.getElementById('maximumRentalHour').value = facility.maximum_rental_hour || '';
                        document.getElementById('totalLevels').value = facility.total_levels || '';
                        document.getElementById('totalRooms').value = facility.total_rooms || '';

                        // Update word count display
                        if (descriptionWordCount) {
                            descriptionWordCount.textContent = `${facility.description?.length || 0}/250 characters`;
                        }

                        // Populate details card
                        document.getElementById('categoryDisplay').textContent = facility.category?.category_name || 'N/A';
                        document.getElementById('subcategoryDisplay').textContent = facility.subcategory?.subcategory_name || 'N/A';
                        document.getElementById('capacityDisplay').textContent = facility.capacity || 'N/A';
                        document.getElementById('locationTypeDisplay').textContent = facility.location_type || 'N/A';
                        document.getElementById('locationNoteDisplay').textContent = facility.location_note || 'N/A';

                        // Load existing images
                        if (facility.images && facility.images.length > 0) {
                            facility.images.forEach(image => {
                                const preview = document.createElement('div');
                                preview.className = 'photo-preview';
                                preview.dataset.id = image.image_id;

                                const img = document.createElement('img');
                                img.src = image.image_url;
                                img.className = 'img-thumbnail h-100 w-100 object-fit-cover';

                                const removeBtn = document.createElement('button');
                                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                                removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                                removeBtn.onclick = () => handleImageDeletion(image.image_id, image.cloudinary_public_id, preview);

                                preview.appendChild(img);
                                preview.appendChild(removeBtn);
                                photosPreview.appendChild(preview);

                                uploadedPhotos.push({
                                    id: image.image_id,
                                    url: image.image_url,
                                    publicId: image.cloudinary_public_id
                                });
                            });
                        }

                        // Fetch and populate dropdowns
                        await fetchCategories();
                        await fetchSubcategories(facility.category_id);
                        await fetchDepartments();
                        await fetchStatuses();
                        await fetchParentFacilities();

                        // Set dropdown values
                        if (facility.category_id) document.getElementById('category').value = facility.category_id;
                        if (facility.subcategory_id) document.getElementById('subcategory').value = facility.subcategory_id;
                        if (facility.department_id) document.getElementById('department').value = facility.department_id;
                        if (facility.status_id) document.getElementById('availabilityStatus').value = facility.status_id;
                        if (facility.parent_facility_id) document.getElementById('parentFacility').value = facility.parent_facility_id;

                    } catch (error) {
                        console.error('Error fetching facility data:', error);
                        showToast('Failed to load facility data: ' + error.message, 'error');
                    }
                }

                // Fetch categories
                async function fetchCategories() {
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/facility-categories/index', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch categories');
                        }

                        const result = await response.json();
                        const categorySelect = document.getElementById('category');
                        categorySelect.innerHTML = '<option value="">Select Category</option>';

                        result.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.category_id;
                            option.textContent = category.category_name;
                            categorySelect.appendChild(option);
                        });

                        return result; // Return categories for subcategory handling
                    } catch (error) {
                        console.error('Error fetching categories:', error);
                        showToast('Failed to load categories: ' + error.message, 'error');
                    }
                }


                // Fetch subcategories based on selected category
                async function fetchSubcategories(categoryId = null, categoriesData = null) {
                    try {
                        const subcategorySelect = document.getElementById('subcategory');
                        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';

                        if (!categoryId || !categoriesData) {
                            return;
                        }

                        // Find the selected category in the categories data
                        const selectedCategory = categoriesData.find(cat => cat.category_id == categoryId);

                        if (selectedCategory && selectedCategory.subcategories) {
                            selectedCategory.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.subcategory_id;
                                option.textContent = subcategory.subcategory_name;
                                subcategorySelect.appendChild(option);
                            });
                        }
                    } catch (error) {
                        console.error('Error fetching subcategories:', error);
                        showToast('Failed to load subcategories: ' + error.message, 'error');
                    }
                }

                // Fetch departments
                async function fetchDepartments() {
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/departments', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch departments');
                        }

                        const result = await response.json();
                        const departmentSelect = document.getElementById('department');
                        departmentSelect.innerHTML = '<option value="">Select Department</option>';

                        result.forEach(department => {
                            const option = document.createElement('option');
                            option.value = department.department_id;
                            option.textContent = department.department_name;
                            departmentSelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error fetching departments:', error);
                        showToast('Failed to load departments: ' + error.message, 'error');
                    }
                }

                // Fetch statuses
                async function fetchStatuses() {
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/availability-statuses', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch statuses');
                        }

                        const result = await response.json();
                        const statusSelect = document.getElementById('availabilityStatus');
                        statusSelect.innerHTML = '<option value="">Select Status</option>';

                        result.forEach(status => {
                            const option = document.createElement('option');
                            option.value = status.status_id;
                            option.textContent = status.status_name;
                            statusSelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error fetching statuses:', error);
                        showToast('Failed to load statuses: ' + error.message, 'error');
                    }
                }
                // Fetch parent facilities
                async function fetchParentFacilities() {
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/facilities', {
                            method: 'GET',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch facilities');
                        }

                        const result = await response.json();
                        const parentSelect = document.getElementById('parentFacility');
                        parentSelect.innerHTML = '<option value="">None</option>';

                        result.data.forEach(facility => {
                            // Don't include the current facility as a parent option
                            if (facility.facility_id != facilityId) {
                                const option = document.createElement('option');
                                option.value = facility.facility_id;
                                option.textContent = facility.facility_name;
                                parentSelect.appendChild(option);
                            }
                        });
                    } catch (error) {
                        console.error('Error fetching facilities:', error);
                        showToast('Failed to load facilities: ' + error.message, 'error');
                    }
                }

                // Category change event
                document.getElementById('category').addEventListener('change', function () {
                    const categoryId = this.value;
                    fetchSubcategories(categoryId);
                });

                // Form submission
                document.getElementById('editFacilityForm').addEventListener('submit', async function (e) {
                    e.preventDefault();

                    try {
                        const formData = {
                            facility_name: document.getElementById('facilityName').value,
                            building_code: document.getElementById('buildingCode').value,
                            description: document.getElementById('description').value,
                            location_note: document.getElementById('locationNote').value,
                            category_id: document.getElementById('category').value,
                            subcategory_id: document.getElementById('subcategory').value,
                            capacity: document.getElementById('capacity').value,
                            location_type: document.getElementById('locationType').value,
                            floor_level: document.getElementById('floorLevel').value || null,
                            room_code: document.getElementById('roomCode').value || null,
                            internal_fee: document.getElementById('internalFee').value,
                            external_fee: document.getElementById('externalFee').value,
                            rate_type: document.getElementById('rateType').value,
                            department_id: document.getElementById('department').value,
                            maximum_rental_hour: document.getElementById('maximumRentalHour').value || null,
                            status_id: document.getElementById('availabilityStatus').value,
                            total_levels: document.getElementById('totalLevels').value || null,
                            total_rooms: document.getElementById('totalRooms').value || null,
                            parent_facility_id: document.getElementById('parentFacility').value || null
                        };

                        // Validate required fields
                        if (!formData.facility_name || !formData.building_code || !formData.category_id ||
                            !formData.capacity || !formData.location_type || !formData.internal_fee ||
                            !formData.external_fee || !formData.rate_type || !formData.department_id ||
                            !formData.status_id) {
                            showToast('Please fill in all required fields', 'error');
                            return;
                        }

                        const response = await fetch(`http://127.0.0.1:8000/api/admin/facilities/${facilityId}`, {
                            method: 'PUT',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Failed to update facility');
                        }

                        const result = await response.json();
                        showToast('Facility updated successfully!', 'success');

                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = '/admin/manage-facilities';
                        }, 1500);

                    } catch (error) {
                        console.error('Error updating facility:', error);
                        showToast('Failed to update facility: ' + error.message, 'error');
                    }
                });

                // Initialize the page
                fetchFacilityData();
            });
        </script>
    @endsection