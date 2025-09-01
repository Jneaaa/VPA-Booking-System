@extends('layouts.admin')

@section('title', 'Edit Equipment')

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
    .equipment-item {
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

    .equipment-item img {
        max-width: 100px;
        /* Restrict photo width */
        max-height: 100px;
        /* Restrict photo height */
        object-fit: cover;
        /* Ensure photo fits within bounds */
    }

    .equipment-item .card-body {
        display: flex;
        align-items: center;
        gap: 1rem;
        /* Add spacing between elements */
    }

    .equipment-item .flex-grow-1 {
        flex: 1;
        /* Allow details section to take remaining space */
    }
</style>
<div class="container-fluid px-4">
    <!-- Main Layout -->
    <div id="layout">
        <!-- Main Content -->
        <main id="main">
            <!-- Edit Equipment Page -->

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Edit Equipment</h1>
                <a href="{{ url('/admin/manage-equipment') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Equipment
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="editEquipmentForm">
                        <input type="hidden" id="equipmentId" value="{{ request()->get('id') }}">

                        <!-- Equipment Photos and Inventory Items Section -->
                        <div class="row mb-4">
                            <!-- Equipment Photos Card -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center"
                                        style="height: 56px;">
                                        <h5 class="fw-bold mb-0">Equipment Photos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="photo-section">
                                            <div class="dropzone border p-4 text-center" id="equipmentPhotosDropzone"
                                                style="cursor: pointer;">
                                                <i class="bi bi-images fs-1 text-muted"></i>
                                                <p class="mt-2">Drag & drop equipment photos here or click to browse</p>
                                                <input type="file" id="equipmentPhotos" class="d-none" multiple
                                                    accept="image/*">
                                            </div>
                                            <small class="text-muted mt-2 d-block">Upload at least one photo of the
                                                equipment (max 5
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


                            <!-- Inventory Items Card -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center"
                                        style="height: 56px;">
                                        <h5 class="fw-bold mb-0">Inventory Items</h5>
                                        <button type="button" class="btn btn-sm btn-secondary" id="addItemBtn">
                                            <i class="bi bi-plus me-1"></i>Add Item
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="itemsContainer">
                                            <p class="text-muted">No items added yet. Click "Add Item" to track
                                                individual equipment pieces.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Details Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="fw-bold mb-0">Equipment Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="details-section">
                                            <!-- Basic Information Section -->
                                            <div class="row mb-4">


                                                <div class="col-md-6">
                                                    <label for="equipmentName"
                                                        class="form-label fw-bold d-flex align-items-center">
                                                        Equipment Name

                                                        <!-- edit icon (default) -->
                                                        <i class="bi bi-pencil text-secondary ms-2"
                                                            id="editEquipmentName" style="cursor: pointer;"></i>

                                                        <!-- save + cancel buttons (hidden at first) -->
                                                        <div id="editActions" class="ms-2 d-none">
                                                            <button type="button" class="btn btn-sm btn-success me-1"
                                                                id="saveEquipmentName">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                id="cancelEquipmentName">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </label>

                                                    <input type="text" class="form-control text-secondary"
                                                        id="equipmentName" value="HD Projector" readonly>
                                                </div>


                                                <div class="col-md-6">
                                                    <label for="brand"
                                                        class="form-label fw-bold d-flex align-items-center">
                                                        Brand
                                                        <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                                            data-field="brand" style="cursor: pointer;"></i>
                                                        <div class="edit-actions ms-2 d-none" data-field="brand">
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
                                                    <input type="text" class="form-control text-secondary" id="brand"
                                                        value="" readonly>
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
                                                        id="descriptionWordCount">0/255 characters</small>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <label for="storageLocation"
                                                        class="form-label fw-bold d-flex align-items-center">
                                                        Storage Location
                                                        <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                                            data-field="storageLocation" style="cursor: pointer;"></i>
                                                        <div class="edit-actions ms-2 d-none"
                                                            data-field="storageLocation">
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
                                                        id="storageLocation" value="" readonly>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="category" class="form-label fw-bold">Category</label>
                                                    <select class="form-select" id="category" required>
                                                        <option value="">Select Category</option>
                                                        <!-- Categories will be populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Quantity & Pricing Section -->
                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <label for="totalQuantity" class="form-label fw-bold">Total
                                                        Quantity</label>
                                                    <input type="number" class="form-control" id="totalQuantity" min="1"
                                                        value="1" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="rentalFee" class="form-label fw-bold">Rental Fee
                                                        (₱)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">₱</span>
                                                        <input type="number" class="form-control" id="rentalFee" min="0"
                                                            step="0.01" required placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="companyFee" class="form-label fw-bold">Company Fee
                                                        (₱)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">₱</span>
                                                        <input type="number" class="form-control" id="companyFee"
                                                            min="0" step="0.01" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="rateType" class="form-label fw-bold">Rate Type</label>
                                                    <select class="form-select" id="rateType" required>
                                                        <!-- Rate types will be populated dynamically -->
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
                                                    <label for="minRentalHours" class="form-label fw-bold">Minimum
                                                        Rental
                                                        Duration (hours)</label>
                                                    <input type="number" class="form-control" id="minRentalHours"
                                                        min="1" value="1" required>
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                            <button type="submit" class="btn btn-primary">Update Equipment</button>
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
    <div class="modal fade" id="resetConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Reset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to reset the form? This will clear all uploaded photos and inventory items.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmResetBtn">Reset</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Inventory Item Modal -->
    <div class="modal fade" id="inventoryItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Inventory Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="itemForm">
                        <!-- Item Photo -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label fw-bold">Item Photo</label>
                                <button class="btn btn-sm btn-danger d-none" id="removePhotoBtn">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                            <div class="dropzone border p-4 text-center" id="itemPhotoDropzone"
                                style="cursor: pointer;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                                <p class="mt-2">Click to upload item photo</p>
                                <input type="file" id="itemPhoto" class="d-none" accept="image/*">
                            </div>
                            <div id="itemPhotoPreview" class="mt-3 text-center"></div>
                        </div>

                        <!-- Item Name -->
                        <div class="mb-3">
                            <label for="itemName" class="form-label fw-bold">Item Name</label>
                            <input type="text" class="form-control" id="itemName" placeholder="Enter item name">
                        </div>

                        <!-- Condition -->
                        <div class="mb-3">
                            <label for="itemCondition" class="form-label fw-bold">Condition</label>
                            <select class="form-select" id="itemCondition" required>
                                <option value="">Select Condition</option>
                                <!-- Conditions will be populated dynamically -->
                            </select>
                        </div>

                        <!-- Barcode -->
                        <div class="mb-3">
                            <label for="barcode" class="form-label fw-bold">Barcode Number</label>
                            <input type="text" class="form-control" id="barcode" placeholder="Scan or enter barcode">
                        </div>

                        <!-- Notes -->
                        <div class="mb-3 position-relative">
                            <label for="itemNotes" class="form-label fw-bold">Item Notes</label>
                            <textarea class="form-control" id="itemNotes" rows="3"
                                placeholder="Additional notes about this item"></textarea>
                            <small class="text-muted position-absolute bottom-0 end-0 me-2 mb-1"
                                id="notesWordCount">0/80
                                words</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveItemBtn">Save Item</button>
                </div>
            </div>
        </div>
    </div>

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
                        const equipmentId = document.getElementById('equipmentId').value;

                        // Delete from Cloudinary first if public ID exists
                        if (currentDeletePublicId) {
                            await deleteImageFromCloudinary(currentDeletePublicId);
                            showToast('Image deleted from storage successfully', 'success');
                        }

                        // Then delete from database if it's a saved image (has photoId)
                        if (currentDeletePhotoId && typeof currentDeletePhotoId === 'number') {
                            await deleteImage(equipmentId, currentDeletePhotoId);
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

                async function handleEquipmentFiles(files) {
    const equipmentId = document.getElementById('equipmentId').value;
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
            // Create a preview
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewId = Date.now();
                const preview = document.createElement('div');
                preview.className = 'photo-preview';
                preview.dataset.id = previewId;
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail h-100 w-100 object-fit-cover';
                
                const removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                removeBtn.onclick = function() {
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
            };
            reader.readAsDataURL(file);
            
            // Upload to Cloudinary
            const result = await uploadToCloudinary(file, equipmentId);
            
            // Update the preview with the actual image from Cloudinary
            if (result && result.secure_url) {
                const preview = photosPreview.querySelector(`[data-id="${previewId}"]`);
                if (preview) {
                    const img = preview.querySelector('img');
                    img.src = result.secure_url;
                }
                
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
            console.error('Error processing file:', error);
            showToast('Failed to process file: ' + error.message, 'error');
        }
    }
}

               // Cloudinary direct upload implementation
async function uploadToCloudinary(file, equipmentId) {
    const CLOUD_NAME = 'dn98ntlkd'; // Your Cloudinary cloud name
    const UPLOAD_PRESET = 'equipment-photos'; // Your unsigned upload preset

    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', UPLOAD_PRESET);
    formData.append('folder', `equipment-photos/${equipmentId}`);
    formData.append('tags', `equipment_${equipmentId}`);

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
        await saveImageToDatabase(equipmentId, data.secure_url, data.public_id);

        showToast('Image uploaded to Cloudinary successfully!', 'success');
        return data;

    } catch (error) {
        console.error('Cloudinary upload error:', error);
        showToast('Cloudinary upload failed: ' + error.message, 'error');
        throw error;
    }
}

// Function to save image reference to your database
async function saveImageToDatabase(equipmentId, imageUrl, publicId) {
    try {
        const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/images/save`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                image_url: imageUrl,
                cloudinary_public_id: publicId,
                description: 'Equipment photo'
            })
        });

        if (!response.ok) {
            throw new Error('Failed to save image to database');
        }

        const result = await response.json();
        console.log('Image saved to database:', result);
        return result;

    } catch (error) {
        console.error('Error saving image to database:', error);
        // Don't throw here - we still want to keep the Cloudinary upload
        showToast('Warning: Image uploaded but database save failed', 'warning');
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

                async function deleteImage(equipmentId, imageId, publicId = null) {
                    try {
                        const token = localStorage.getItem('adminToken');

                        // Updated endpoint to match your new route
                        const response = await fetch(`http://127.0.0.1:8000/api/admin/${imageId}/delete-photo`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to delete image from database');
                        }

                        showToast('Image deleted successfully', 'success');
                        return await response.json();
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

                const equipmentId = document.getElementById('equipmentId').value;
                if (!equipmentId) {
                    alert('No equipment ID provided');
                    window.location.href = '/admin/manage-equipment';
                    return;
                }

                // Initialize the reset confirmation modal
                const resetConfirmationModal = new bootstrap.Modal('#resetConfirmationModal', {
                    backdrop: 'static',
                    keyboard: false
                });

                // Handle reset button click
                document.getElementById('resetBtn').addEventListener('click', function (e) {
                    e.preventDefault();
                    resetConfirmationModal.show();
                });

                // Handle confirm reset button click
                document.getElementById('confirmResetBtn').addEventListener('click', function () {
                    // Reset to original equipment data
                    loadEquipmentData(equipmentId);

                    // Hide the modal
                    resetConfirmationModal.hide();
                });

                // Equipment Photos Section
                const equipmentDropzone = document.getElementById('equipmentPhotosDropzone');
                const equipmentFileInput = document.getElementById('equipmentPhotos');
                const photosPreview = document.getElementById('photosPreview');
                let uploadedPhotos = [];
                if (equipmentDropzone && equipmentFileInput) {
                    equipmentDropzone.addEventListener('click', function () {
                        equipmentFileInput.click();
                    });

                    equipmentFileInput.addEventListener('change', function () {
                        handleEquipmentFiles(this.files);
                        this.value = '';
                    });

                    equipmentDropzone.addEventListener('dragover', function (e) {
                        e.preventDefault();
                        this.classList.add('border-primary');
                    });

                    equipmentDropzone.addEventListener('dragleave', function () {
                        this.classList.remove('border-primary');
                    });

                    equipmentDropzone.addEventListener('drop', function (e) {
                        e.preventDefault();
                        this.classList.remove('border-primary');
                        if (e.dataTransfer.files.length) {
                            handleEquipmentFiles(e.dataTransfer.files);
                        }
                    });
                }

                // Word count limiter for Description textbox
                const description = document.getElementById('description');
                const descriptionWordCount = document.getElementById('descriptionWordCount');
                const descriptionMaxChars = 255;

                if (description && descriptionWordCount) {
                    description.addEventListener('input', function () {
                        if (this.value.length > descriptionMaxChars) {
                            this.value = this.value.substring(0, descriptionMaxChars);
                        }

                        const charCount = this.value.length;
                        descriptionWordCount.textContent = `${charCount}/${descriptionMaxChars} characters`;
                        descriptionWordCount.classList.toggle('text-danger', charCount >= descriptionMaxChars);
                    });

                    description.addEventListener('paste', function (e) {
                        e.preventDefault();
                        const pasteText = (e.clipboardData || window.clipboardData).getData('text');
                        const newText = this.value.substring(0, this.selectionStart) +
                            pasteText +
                            this.value.substring(this.selectionEnd);

                        const remainingChars = descriptionMaxChars - this.value.length + (this.selectionEnd - this.selectionStart);
                        if (remainingChars > 0) {
                            const pasteToInsert = pasteText.substring(0, remainingChars);
                            document.execCommand('insertText', false, pasteToInsert);
                        }
                    });
                }

                // Initialize Inventory Item Modal
                const addItemBtn = document.getElementById('addItemBtn');
                if (addItemBtn) {
                    const inventoryItemModal = new bootstrap.Modal('#inventoryItemModal');
                    const itemPhotoInput = document.getElementById('itemPhoto');
                    const itemPhotoPreview = document.getElementById('itemPhotoPreview');
                    const itemNotes = document.getElementById('itemNotes');
                    const notesWordCount = document.getElementById('notesWordCount');
                    const saveItemBtn = document.getElementById('saveItemBtn');
                    const itemsContainer = document.getElementById('itemsContainer');

                    addItemBtn.addEventListener('click', () => {
                        document.getElementById('itemForm').reset();
                        if (itemPhotoPreview) itemPhotoPreview.innerHTML = '';
                        inventoryItemModal.show();
                    });

                    if (itemPhotoInput) {
                        itemPhotoInput.addEventListener('change', function () {
                            if (this.files?.[0]) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    const itemPhotoDropzone = document.getElementById('itemPhotoDropzone');
                                    if (itemPhotoDropzone) itemPhotoDropzone.style.display = 'none';
                                    const removePhotoBtn = document.getElementById('removePhotoBtn');
                                    if (removePhotoBtn) removePhotoBtn.classList.remove('d-none');
                                    if (itemPhotoPreview) {
                                        itemPhotoPreview.innerHTML = `
                                                                                                                                                                            <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                                                                                                                                                                          `;
                                    }
                                };
                                reader.readAsDataURL(this.files[0]);
                            }
                        });
                    }

                    const removePhotoBtn = document.getElementById('removePhotoBtn');
                    if (removePhotoBtn) {
                        removePhotoBtn.addEventListener('click', function () {
                            if (itemPhotoPreview) itemPhotoPreview.innerHTML = '';
                            const itemPhotoDropzone = document.getElementById('itemPhotoDropzone');
                            if (itemPhotoDropzone) itemPhotoDropzone.style.display = 'block';
                            const itemPhotoInput = document.getElementById('itemPhoto');
                            if (itemPhotoInput) itemPhotoInput.value = '';
                            this.classList.add('d-none');
                        });
                    }

                    const itemPhotoDropzone = document.getElementById('itemPhotoDropzone');
                    if (itemPhotoDropzone) {
                        itemPhotoDropzone.addEventListener('click', function () {
                            const itemPhotoInput = document.getElementById('itemPhoto');
                            if (itemPhotoInput) itemPhotoInput.click();
                        });
                    }

                    if (itemNotes && notesWordCount) {
                        itemNotes.addEventListener('input', function () {
                            const words = this.value.trim() ? this.value.trim().split(/\s+/) : [];
                            const wordCount = words.length;

                            notesWordCount.textContent = `${wordCount}/80 words`;
                            notesWordCount.classList.toggle('text-danger', wordCount >= 80);

                            if (wordCount > 80) {
                                const allowedWords = words.slice(0, 80).join(' ');
                                const cursorPos = this.selectionStart;
                                this.value = allowedWords;

                                if (cursorPos <= allowedWords.length) {
                                    this.setSelectionRange(cursorPos, cursorPos);
                                }
                            }
                        });

                        itemNotes.addEventListener('keydown', function (e) {
                            const words = this.value.trim() ? this.value.trim().split(/\s+/) : [];
                            const allowedKeys = [8, 46, 37, 38, 39, 40, 16, 17, 91, 9];

                            if (words.length >= 80 && !allowedKeys.includes(e.keyCode)) {
                                if (e.key.length === 1 || e.keyCode === 32) {
                                    e.preventDefault();
                                }
                            }
                        });

                        itemNotes.addEventListener('paste', function (e) {
                            e.preventDefault();
                            const pasteText = (e.clipboardData || window.clipboardData).getData('text');
                            const currentText = this.value;
                            const selectionStart = this.selectionStart;
                            const selectionEnd = this.selectionEnd;

                            const newText = currentText.substring(0, selectionStart) +
                                pasteText +
                                currentText.substring(selectionEnd);

                            const currentWords = currentText.trim() ? currentText.trim().split(/\s+/) : [];
                            const pasteWords = pasteText.trim() ? pasteText.trim().split(/\s+/) : [];
                            const selectedWords = currentText.substring(selectionStart, selectionEnd).trim() ?
                                currentText.substring(selectionStart, selectionEnd).trim().split(/\s+/) : [];

                            const newWordCount = currentWords.length - selectedWords.length + pasteWords.length;

                            if (newWordCount <= 80) {
                                document.execCommand('insertText', false, pasteText);
                            } else {
                                const remainingWords = 80 - (currentWords.length - selectedWords.length);
                                if (remainingWords > 0) {
                                    const wordsToPaste = pasteWords.slice(0, remainingWords).join(' ');
                                    document.execCommand('insertText', false, wordsToPaste);
                                }
                            }
                        });
                    }

                    if (saveItemBtn) {
                        saveItemBtn.addEventListener('click', function () {
                            const condition = document.getElementById('itemCondition')?.value;
                            if (!condition) {
                                alert('Please select the item condition');
                                return;
                            }

                            const itemId = Date.now();
                            const barcode = document.getElementById('barcode')?.value || '';
                            const notes = document.getElementById('itemNotes')?.value || '';
                            const itemPhoto = itemPhotoPreview?.querySelector('img')?.src || '';

                            const conditionColors = {
                                "New": "bg-success text-white",
                                "Good": "bg-primary text-white",
                                "Fair": "bg-warning text-dark",
                                "Needs Maintenance": "bg-danger text-white",
                                "Damaged": "bg-dark text-white"
                            };

                            const itemCard = document.createElement('div');
                            itemCard.className = 'card equipment-item';
                            itemCard.innerHTML = `
                                                                                                                                                                      <div class="card-body">
                                                                                                                                                                        <div class="photo-container">
                                                                                                                                                                          ${itemPhoto ? `<img src="${itemPhoto}" class="img-thumbnail">` : ''}
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="flex-grow-1">
                                                                                                                                                                          <h6 class="card-title">Item #${itemId}</h6>
                                                                                                                                                                          <div class="d-flex flex-wrap gap-3">
                                                                                                                                                                            <span class="badge ${conditionColors[condition]}">${condition}</span>
                                                                                                                                                                          </div>
                                                                                                                                                                          ${barcode ? `<div class="mt-2"><strong>Barcode:</strong> ${barcode}</div>` : ''}
                                                                                                                                                                          ${notes ? `<p class="mt-2 mb-0"><strong>Notes:</strong> ${notes.substring(0, 50)}${notes.length > 50 ? '...' : ''}</p>` : ''}
                                                                                                                                                                        </div>
                                                                                                                                                                        <button class="btn btn-sm btn-danger align-self-start" onclick="this.closest('.equipment-item').remove()">
                                                                                                                                                                          <i class="bi bi-trash"></i>
                                                                                                                                                                        </button>
                                                                                                                                                                      </div>
                                                                                                                                                                    `;

                            if (itemsContainer) {
                                if (itemsContainer.querySelector('p.text-muted')) {
                                    itemsContainer.innerHTML = '';
                                }
                                itemsContainer.appendChild(itemCard);
                            }

                            inventoryItemModal.hide();
                        });
                    }
                }

                // Add 'required' class to labels with required fields
                document.querySelectorAll('label[for]').forEach(label => {
                    const input = document.getElementById(label.getAttribute('for'));
                    if (input && input.hasAttribute('required')) {
                        label.classList.add('required');
                    }
                });

                // Load equipment data
                loadEquipmentData(equipmentId);
            });

            async function loadEquipmentData(equipmentId) {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/equipment/${equipmentId}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch equipment data');
                    }

                    const data = await response.json();
                    const equipment = data.data;

                    // Populate form fields
                    document.getElementById('equipmentName').value = equipment.equipment_name;
                    document.getElementById('description').value = equipment.description || '';
                    document.getElementById('brand').value = equipment.brand || '';
                    document.getElementById('storageLocation').value = equipment.storage_location;
                    document.getElementById('totalQuantity').value = equipment.total_quantity;
                    document.getElementById('rentalFee').value = equipment.external_fee;
                    document.getElementById('companyFee').value = equipment.internal_fee;
                    document.getElementById('minRentalHours').value = equipment.maximum_rental_hour || 1;

                    // Update word count
                    const descriptionWordCount = document.getElementById('descriptionWordCount');
                    if (descriptionWordCount) {
                        descriptionWordCount.textContent = `${equipment.description?.length || 0}/255 characters`;
                    }

                    // Load images
                    if (equipment.images && equipment.images.length > 0) {
                        const photosPreview = document.getElementById('photosPreview');
                        photosPreview.innerHTML = '';

                        equipment.images.forEach(image => {
                            const preview = document.createElement('div');
                            preview.className = 'photo-preview';
                            preview.dataset.id = image.image_id;

                            const img = document.createElement('img');
                            img.src = image.image_url;
                            img.className = 'img-thumbnail h-100 w-100 object-fit-cover';

                            const removeBtn = document.createElement('button');
                            removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                            removeBtn.onclick = function () {
                                deleteImage(equipmentId, image.image_id);
                                preview.remove();
                            };

                            preview.appendChild(img);
                            preview.appendChild(removeBtn);
                            photosPreview.appendChild(preview);
                        });
                    }

                    // Fetch dropdown data
                    await fetchDropdownData(equipment);

                } catch (error) {
                    console.error('Error loading equipment data:', error);
                    alert('Failed to load equipment data: ' + error.message);
                }
            }

            async function fetchDropdownData(equipment) {
                try {
                    const token = localStorage.getItem('adminToken');
                    console.log('Equipment data:', equipment); // Debug log

                    // Fetch categories
                    const categoriesResponse = await fetch('http://127.0.0.1:8000/api/equipment-categories', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (categoriesResponse.ok) {
                        const categoriesData = await categoriesResponse.json();
                        console.log('Categories data:', categoriesData); // Debug log
                        // Remove the .data access - the response is the array directly
                        if (Array.isArray(categoriesData)) {
                            populateDropdown('category', categoriesData, equipment.category_id, 'category_id', 'category_name');
                        }
                    }

                    // Fetch statuses
                    const statusesResponse = await fetch('http://127.0.0.1:8000/api/availability-statuses', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (statusesResponse.ok) {
                        const statusesData = await statusesResponse.json();
                        console.log('Statuses data:', statusesData); // Debug log
                        // Remove the .data access - the response is the array directly
                        if (Array.isArray(statusesData)) {
                            populateDropdown('availabilityStatus', statusesData, equipment.status_id, 'status_id', 'status_name');
                        }
                    }

                    // Fetch departments
                    const departmentsResponse = await fetch('http://127.0.0.1:8000/api/departments', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (departmentsResponse.ok) {
                        const departmentsData = await departmentsResponse.json();
                        console.log('Departments data:', departmentsData); // Debug log
                        // Remove the .data access - the response is the array directly
                        if (Array.isArray(departmentsData)) {
                            populateDropdown('department', departmentsData, equipment.department_id, 'department_id', 'department_name');
                        }
                    }

                    // Populate rate type dropdown
                    const rateTypeDropdown = document.getElementById('rateType');
                    if (rateTypeDropdown) {
                        rateTypeDropdown.innerHTML = `
                                                                                            <option value="Per Hour" ${equipment.rate_type === 'Per Hour' ? 'selected' : ''}>Per Hour</option>
                                                                                            <option value="Per Event" ${equipment.rate_type === 'Per Event' ? 'selected' : ''}>Per Event</option>
                                                                                        `;
                    }

                    // Fetch conditions for inventory items
                    const conditionsResponse = await fetch('http://127.0.0.1:8000/api/conditions', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (conditionsResponse.ok) {
                        const conditionsData = await conditionsResponse.json();
                        // Remove the .data access - the response is the array directly
                        if (Array.isArray(conditionsData)) {
                            populateDropdown('itemCondition', conditionsData, null, 'condition_id', 'condition_name');
                        }
                    }

                } catch (error) {
                    console.error('Error fetching dropdown data:', error);
                }
            }

            function populateDropdown(elementId, data, selectedValue = null, idKey, nameKey) {
                const dropdown = document.getElementById(elementId);
                if (!dropdown) {
                    console.error('Dropdown element not found:', elementId);
                    return;
                }

                // Clear existing options except the first one
                while (dropdown.options.length > 1) {
                    dropdown.remove(1);
                }

                // Add new options
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[idKey];
                    option.textContent = item[nameKey];

                    if (selectedValue !== null && option.value == selectedValue) {
                        option.selected = true;
                    }

                    dropdown.appendChild(option);
                });

                // If no option was selected, try to select the first one
                if (selectedValue !== null && dropdown.value !== selectedValue) {
                    console.warn(`Could not find selected value ${selectedValue} in dropdown ${elementId}`);
                }
            }

            function populateDropdown(elementId, data, selectedValue = null, idKey, nameKey) {
                const dropdown = document.getElementById(elementId);
                if (!dropdown) return;

                // Clear existing options except the first one
                while (dropdown.options.length > 1) {
                    dropdown.remove(1);
                }

                // Add new options
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[idKey];
                    option.textContent = item[nameKey];

                    if (selectedValue !== null && option.value == selectedValue) {
                        option.selected = true;
                    }

                    dropdown.appendChild(option);
                });
            }

            async function deleteImage(equipmentId, imageId) {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('adminToken')}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to delete image');
                    }

                    console.log('Image deleted successfully');
                } catch (error) {
                    console.error('Error deleting image:', error);
                    alert('Failed to delete image: ' + error.message);
                }
            }

            // Form submission
            document.getElementById('editEquipmentForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const equipmentId = document.getElementById('equipmentId').value;
                const formData = {
                    equipment_name: document.getElementById('equipmentName').value,
                    description: document.getElementById('description').value,
                    brand: document.getElementById('brand').value,
                    storage_location: document.getElementById('storageLocation').value,
                    category_id: document.getElementById('category').value,
                    total_quantity: document.getElementById('totalQuantity').value,
                    internal_fee: document.getElementById('companyFee').value,
                    external_fee: document.getElementById('rentalFee').value,
                    rate_type: document.getElementById('rateType').value === '1' ? 'Per Hour' : 'Per Event',
                    status_id: document.getElementById('availabilityStatus').value,
                    department_id: document.getElementById('department').value,
                    maximum_rental_hour: document.getElementById('minRentalHours').value,
                };

                try {
                    const token = localStorage.getItem('adminToken');

                    const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    if (!response.ok) {
                        throw new Error('Failed to update equipment');
                    }

                    // Upload new images if any
                    await uploadNewImages(equipmentId);

                    alert('Equipment updated successfully!');
                    window.location.href = '/admin/manage-equipment';
                } catch (error) {
                    console.error('Error updating equipment:', error);
                    alert('Failed to update equipment: ' + error.message);
                }
            });

          async function uploadNewImages(equipmentId) {
    const filesToUpload = uploadedPhotos.filter(photo => photo.file).map(photo => photo.file);

    for (const file of filesToUpload) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('description', 'Equipment photo');
        formData.append('equipmentId', equipmentId); // Add equipmentId to form data

        try {
            const token = localStorage.getItem('adminToken');
            
            // Updated endpoint to match your new route
            const response = await fetch(`http://127.0.0.1:8000/api/admin/upload`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error('Failed to upload image');
            }

            const result = await response.json();
            console.log('Image uploaded successfully:', result);

        } catch (error) {
            console.error('Error uploading image:', error);
            showToast('Failed to upload image: ' + error.message, 'error');
        }
    }
}
        </script>
    @endsection