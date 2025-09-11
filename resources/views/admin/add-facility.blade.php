@extends('layouts.admin')

@section('title', 'Add Facility')

@section('content')
    <style>
        .subcategory-row {
            transition: all 0.3s ease;
        }

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
    <!-- Main Content -->
    <main id="main">
        <!-- Add Facility Page -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form id="addFacilityForm">
                    <input type="hidden" id="facilityId" value="{{ request()->get('id') }}">

                    <!-- Facility Photos and Details Section -->
                    <div class="row mb-4 align-items-stretch">
                        <!-- Left Column: Photos + New Card stacked -->
                        <div class="col-md-6 d-flex flex-column h-100">
                            <!-- Facility Photos Card -->
                            <div class="card flex-fill mb-4">
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
                                            <input type="file" id="facilityPhotos" class="d-none" multiple accept="image/*">
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            Upload at least one photo of the facility (max 5 photos)
                                        </small>
                                        <div id="photosPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                                    </div>
                                </div>
                            </div>


                            <!-- Facility Amenities -->
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="fw-bold mb-0">Amenities</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-0">No amenities added yet.</p>
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
                                        <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">Delete
                                            Photo</button>
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
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="facilityName" class="form-label fw-bold">
                                                        Facility Name
                                                    </label>
                                                    <input type="text" class="form-control text-secondary" id="facilityName"
                                                        placeholder="Facility Name">
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="buildingCode" class="form-label fw-bold">
                                                        Building Code
                                                    </label>
                                                    <input type="text" class="form-control text-secondary" id="buildingCode"
                                                        placeholder="Building Code">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-12 position-relative">
                                                    <label for="description" class="form-label fw-bold">
                                                        Description
                                                    </label>
                                                    <textarea class="form-control text-secondary" id="description" rows="3"
                                                    placeholder="Write a description..."></textarea>
                                                    <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1"
                                                        id="descriptionWordCount">0/250 characters</small>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <!-- Location Note: wider (8 of 12 columns) -->
                                                <div class="col-md-8">
                                                    <label for="locationNote" class="form-label fw-bold">
                                                        Location Note
                                                    </label>
                                                    <input type="text" class="form-control text-secondary" id="locationNote"
                                                        placeholder="Location Note">
                                                </div>

                                                <!-- Location Type: narrower (4 of 12 columns) -->
                                                <div class="col-md-4">
                                                    <label for="locationType" class="form-label fw-bold">Location
                                                        Type</label>
                                                    <select class="form-select" id="locationType" required>
                                                        <option value="Indoors">Indoors</option>
                                                        <option value="Outdoors">Outdoors</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Category row -->
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label for="category" class="form-label fw-bold">Category</label>
                                                    <select class="form-select w-100" id="category" required>
                                                        <option value="">Select Category</option>
                                                        <!-- Categories populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Subcategory row -->
                                            <div class="row mb-3 subcategory-row" style="display: none;">
                                                <div class="col-12">
                                                    <label for="subcategory" class="form-label fw-bold">Subcategory</label>
                                                    <select class="form-select w-100" id="subcategory">
                                                        <option value="">Select Subcategory</option>
                                                        <!-- Subcategories populated dynamically -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Capacity & Location Section -->
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="capacity" class="form-label fw-bold">Capacity</label>
                                                    <input type="number" class="form-control" id="capacity" min="1"
                                                        value="1" required>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="floorLevel" class="form-label fw-bold">Floor Level</label>
                                                    <input type="number" class="form-control" id="floorLevel" min="1"
                                                        placeholder="Floor level">
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="roomCode" class="form-label fw-bold">Room Code</label>
                                                    <input type="text" class="form-control" id="roomCode"
                                                        placeholder="Room code">
                                                </div>
                                            </div>

                                            <!-- Pricing Section -->
                                            <div class="row mb-3">
                                                <!-- Rental Fee -->
                                                <div class="col-md-6">
                                                    <label for="rentalFee" class="form-label fw-bold">Rental Fee (₱)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">₱</span>
                                                        <input type="number" class="form-control" id="rentalFee" min="0"
                                                            step="0.01" required placeholder="0.00">
                                                    </div>
                                                </div>

                                                <!-- Rate Type -->
                                                <div class="col-md-6">
                                                    <label for="rateType" class="form-label fw-bold">Rate Type</label>
                                                    <select class="form-select" id="rateType" required>
                                                        <option value="Per Hour">Per Hour</option>
                                                        <option value="Per Event">Per Event</option>
                                                    </select>
                                                </div>

                                                <!-- Building Details Section -->
                                                <div class="row mb-1 g-2">
                                                    <div class="col-md-6">
                                                        <label for="totalLevels" class="form-label fw-bold">Total
                                                            Levels</label>
                                                        <input type="number" class="form-control" id="totalLevels" min="1"
                                                            placeholder="Total building levels">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="totalRooms" class="form-label fw-bold">Total
                                                            Rooms</label>
                                                        <input type="number" class="form-control" id="totalRooms" min="1"
                                                            placeholder="Total rooms in building">
                                                    </div>
                                                </div>

                                                <!-- Department & Availability Section -->
                                                <div class="row mb-1 g-2">
                                                    <div class="col-md-6">
                                                        <label for="department" class="form-label fw-bold">Owning
                                                            Department</label>
                                                        <select class="form-select" id="department" required>
                                                            <!-- Departments will be populated dynamically -->
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="availabilityStatus"
                                                            class="form-label fw-bold">Availability Status</label>
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
                                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Facility</button>
                            </div>
                        </div>
                </form>
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
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // 1. Global variables first
                window.pendingImageUploads = []; // Track images to be uploaded
                window.pendingImageDeletions = []; // Track images to be deleted
                window.newFacilityId = null; // Will store the ID after facility is created
                window.facilityCategories = []; // Store categories with subcategories
                // 2. Authentication check
                const token = localStorage.getItem('adminToken');
                if (!token) {
                    window.location.href = '/admin/admin-login';
                    return;
                }


                // Toast notification function
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

                // 3. Fetch categories with subcategories
                async function fetchCategoriesWithSubcategories() {
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/facility-categories/index', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            const categoriesData = await response.json();
                            window.facilityCategories = categoriesData;
                            populateCategoryDropdown(categoriesData);
                        } else {
                            console.error('Failed to fetch categories with subcategories:', response.status);
                            showToast('Failed to load categories', 'error');
                        }
                    } catch (error) {
                        console.error('Error fetching categories:', error);
                        showToast('Error loading categories', 'error');
                    }
                }

                // 4. Populate category dropdown
                function populateCategoryDropdown(categories) {
                    const categoryDropdown = document.getElementById('category');
                    if (!categoryDropdown) return;

                    // Clear existing options except the first one
                    while (categoryDropdown.options.length > 1) {
                        categoryDropdown.remove(1);
                    }

                    // Add new options
                    categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.category_id;
                        option.textContent = category.category_name;
                        categoryDropdown.appendChild(option);
                    });
                }

                // 5. Handle category change to show/hide subcategory
                function setupCategoryChangeHandler() {
                    const categoryDropdown = document.getElementById('category');
                    const subcategoryDropdown = document.getElementById('subcategory');
                    const subcategoryRow = document.querySelector('.row.mb-3:has(#subcategory)');

                    if (!categoryDropdown || !subcategoryDropdown) return;

                    categoryDropdown.addEventListener('change', function () {
                        const categoryId = this.value;
                        const selectedCategory = window.facilityCategories.find(cat =>
                            cat.category_id.toString() === categoryId
                        );

                        // Clear subcategory dropdown
                        while (subcategoryDropdown.options.length > 1) {
                            subcategoryDropdown.remove(1);
                        }

                        // Show/hide subcategory based on whether category has subcategories
                        if (selectedCategory && selectedCategory.subcategories && selectedCategory.subcategories.length > 0) {
                            subcategoryRow.style.display = 'block';

                            // Populate subcategories
                            selectedCategory.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.subcategory_id;
                                option.textContent = subcategory.subcategory_name;
                                subcategoryDropdown.appendChild(option);
                            });
                        } else {
                            subcategoryRow.style.display = 'none';
                            subcategoryDropdown.value = '';
                        }
                    });
                }


                // 6. Delete image modal initialization
                const deleteImageModal = new bootstrap.Modal('#deleteImageModal', {
                    backdrop: 'static',
                    keyboard: false
                });

                // 7. Image deletion variables
                let currentDeletePhotoId = null;
                let currentDeletePublicId = null;
                let currentDeletePreviewElement = null;

                // 8. Image deletion handler
                async function handleImageDeletion(photoId, publicId, previewElement) {
                    currentDeletePhotoId = photoId;
                    currentDeletePublicId = publicId;
                    currentDeletePreviewElement = previewElement;

                    // Show the confirmation modal
                    deleteImageModal.show();
                }

                // 9. Cloudinary upload functions
                async function uploadToCloudinary(file, facilityId) {
                    const CLOUD_NAME = 'dn98ntlkd'; // Your Cloudinary cloud name
                    const UPLOAD_PRESET = 'facility-photos'; // Your unsigned upload preset

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('upload_preset', UPLOAD_PRESET);
                    formData.append('folder', `facility-photos/${facilityId}`);
                    formData.append('tags', `facility_${facilityId}`);

                    try {
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
                        return data;

                    } catch (error) {
                        console.error('Cloudinary upload error:', error);
                        showToast('Cloudinary upload failed: ' + error.message, 'error');
                        throw error;
                    }
                }

                // 10. Function to save image reference to your database
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
                        throw error;
                    }
                }

                // 11. Image deletion functions
                async function deleteImageFromCloudinary(publicId) {
                    try {
                        const token = localStorage.getItem('adminToken');

                        // Use FormData instead of JSON for Cloudinary deletion
                        const formData = new FormData();
                        formData.append('public_id', publicId);

                        const response = await fetch(`http://127.0.0.1:8000/api/admin/cloudinary/delete`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Cloudinary delete failed:', response.status, errorText);
                            throw new Error(`Failed to delete image from Cloudinary: ${response.status} ${errorText}`);
                        }

                        const result = await response.json();

                        // Check if the deletion was actually successful based on the actual response format
                        if (result.result && (result.result.deleted || result.result === 'ok' || result.result === 'success')) {
                            return result;
                        } else {
                            console.warn('Unexpected Cloudinary response format:', result);
                            return result;
                        }

                    } catch (error) {
                        console.error('Error deleting image from Cloudinary:', error);
                        showToast('Failed to delete from storage: ' + error.message, 'error');
                        throw error;
                    }
                }

                // 12. Facility file handling function
                async function handleFacilityFiles(files) {
                    for (const file of files) {
                        // Check if file is an image
                        if (!file.type.startsWith('image/')) {
                            showToast('Please upload only image files', 'error');
                            continue;
                        }

                        // Check if we've reached the maximum of 5 photos
                        const currentCount = document.querySelectorAll('#photosPreview .photo-preview').length;
                        if (currentCount + pendingImageUploads.length >= 5) {
                            showToast('Maximum of 5 photos allowed', 'error');
                            break;
                        }

                        try {
                            const previewId = 'temp-' + Date.now();

                            // Create a preview
                            const reader = new FileReader();
                            reader.onload = async (e) => {
                                const preview = document.createElement('div');
                                preview.className = 'photo-preview';
                                preview.dataset.previewId = previewId;

                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'img-thumbnail h-100 w-100 object-fit-cover';

                                const removeBtn = document.createElement('button');
                                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                                removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                                removeBtn.onclick = function () {
                                    // Remove from pending uploads
                                    const index = pendingImageUploads.findIndex(photo => photo.previewId === previewId);
                                    if (index !== -1) {
                                        pendingImageUploads.splice(index, 1);
                                    }
                                    preview.remove();
                                };

                                preview.appendChild(img);
                                preview.appendChild(removeBtn);
                                photosPreview.appendChild(preview);

                                // Add to pending uploads (will be processed on form submit)
                                pendingImageUploads.push({
                                    file: file,
                                    previewId: previewId,
                                    previewElement: preview
                                });
                            };
                            reader.readAsDataURL(file);

                        } catch (error) {
                            console.error('Error processing file:', error);
                            showToast('Failed to process file: ' + error.message, 'error');
                        }
                    }
                }

                // 13. Cancel confirmation modal
                const cancelConfirmationModal = new bootstrap.Modal('#cancelConfirmationModal', {
                    backdrop: 'static',
                    keyboard: false
                });

                // 14. Cancel button handlers
                document.getElementById('cancelBtn').addEventListener('click', function (e) {
                    e.preventDefault();
                    cancelConfirmationModal.show();
                });

                document.getElementById('confirmCancelBtn').addEventListener('click', function () {
                    // Hide the modal first
                    cancelConfirmationModal.hide();

                    // Reset all changes
                    resetAllChanges();
                    showToast('Changes discarded', 'info');
                });

                // Add this function to handle cancel operations
                function resetAllChanges() {
                    // Clear all pending changes
                    pendingImageUploads = [];
                    pendingImageDeletions = [];
                    pendingItemPhotoChanges.clear();

                    // Clear form fields
                    document.getElementById('facilityName').value = '';
                    document.getElementById('description').value = '';
                    document.getElementById('brand').value = '';
                    document.getElementById('storageLocation').value = '';
                    document.getElementById('totalQuantity').value = 1;
                    document.getElementById('rentalFee').value = '';
                    document.getElementById('companyFee').value = '';
                    document.getElementById('minRentalHours').value = 1;
                    document.getElementById('category').selectedIndex = 0;
                    document.getElementById('rateType').selectedIndex = 0;
                    document.getElementById('department').selectedIndex = 0;
                    document.getElementById('availabilityStatus').selectedIndex = 0;

                    // Clear any temporary UI elements
                    const photosPreview = document.getElementById('photosPreview');
                    if (photosPreview) {
                        photosPreview.innerHTML = '';
                    }

                    // Clear items
                    const itemsContainer = document.getElementById('itemsContainer');
                    if (itemsContainer) {
                        itemsContainer.innerHTML = '<p class="text-muted">No items added yet. Click "Add Item" to track individual facilities pieces.</p>';
                    }

                    facilityItems = [];
                }

                // 15. Facility Photos Section
                const facilitiesDropzone = document.getElementById('facilitiesPhotosDropzone');
                const facilitiesFileInput = document.getElementById('facilitiesPhotos');
                const photosPreview = document.getElementById('photosPreview');
                let uploadedPhotos = [];

                if (facilitiesDropzone && facilitiesFileInput) {
                    facilitiesDropzone.addEventListener('click', function () {
                        facilitiesFileInput.click();
                    });

                    facilitiesFileInput.addEventListener('change', function () {
                        handleFacilityFiles(this.files);
                        this.value = '';
                    });

                    facilitiesDropzone.addEventListener('dragover', function (e) {
                        e.preventDefault();
                        this.classList.add('border-primary');
                    });

                    facilitiesDropzone.addEventListener('dragleave', function () {
                        this.classList.remove('border-primary');
                    });

                    facilitiesDropzone.addEventListener('drop', function (e) {
                        e.preventDefault();
                        this.classList.remove('border-primary');
                        if (e.dataTransfer.files.length) {
                            handleFacilityFiles(e.dataTransfer.files);
                        }
                    });
                }

                // 20. Initialize new facilities form
                async function initializeNewFacilityForm() {
                    try {
                        // Fetch dropdown data
                        await fetchDropdownData(null);
                        // Fetch categories with subcategories
                        await fetchCategoriesWithSubcategories();
                        // Setup category change handler
                        setupCategoryChangeHandler();

                    } catch (error) {
                        console.error('Error initializing new facility form:', error);
                        showToast('Failed to initialize form: ' + error.message, 'error');
                    }
                }

                // 21. Add 'required' class to labels with required fields
                document.querySelectorAll('label[for]').forEach(label => {
                    const input = document.getElementById(label.getAttribute('for'));
                    if (input && input.hasAttribute('required')) {
                        label.classList.add('required');
                    }
                });

                // 23. Fetch dropdown data function
                async function fetchDropdownData(facility) {
                    try {
                        const token = localStorage.getItem('adminToken');

                        // Fetch statuses
                        const statusesResponse = await fetch('http://127.0.0.1:8000/api/availability-statuses', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (statusesResponse.ok) {
                            const statusesData = await statusesResponse.json();
                            if (Array.isArray(statusesData)) {
                                populateDropdown('availabilityStatus', statusesData, facility ? facility.status_id : 1, 'status_id', 'status_name');
                            }
                        } else {
                            console.error('Failed to fetch statuses:', statusesResponse.status);
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
                            if (Array.isArray(departmentsData)) {
                                populateDropdown('department', departmentsData, facility ? facility.department_id : null, 'department_id', 'department_name');
                            }
                        } else {
                            console.error('Failed to fetch departments:', departmentsResponse.status);
                        }

                    } catch (error) {
                        console.error('Error fetching dropdown data:', error);
                    }
                }

                // 24. Populate dropdown function
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

                        // Convert both values to string for comparison to handle number vs string mismatches
                        if (selectedValue !== null && option.value.toString() === selectedValue.toString()) {
                            option.selected = true;
                        }

                        dropdown.appendChild(option);
                    });
                }

                // 22. Initialize new facilities form
                initializeNewFacilityForm();

               // 25. Form submission handler - RESTRUCTURED FOR NEW FACILITY
// 25. Form submission handler - RESTRUCTURED FOR NEW FACILITY
document.getElementById('addFacilityForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const token = localStorage.getItem('adminToken');
    const adminId = localStorage.getItem('adminId');

    try {
        showToast('Creating facility...', 'info');

        // Validate required fields
        const requiredFields = [
            'facilityName', 'locationNote', 'capacity', 'category', 
            'department', 'locationType', 'rentalFee', 'rateType', 'availabilityStatus'
        ];
        
        for (const fieldId of requiredFields) {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                showToast(`Please fill in the ${fieldId.replace(/([A-Z])/g, ' $1').toLowerCase()} field`, 'error');
                field.focus();
                return;
            }
        }

        // Validate field lengths
        const facilityName = document.getElementById('facilityName').value;
        const description = document.getElementById('description').value;
        const locationNote = document.getElementById('locationNote').value;
        const buildingCode = document.getElementById('buildingCode').value;
        const roomCode = document.getElementById('roomCode').value;

        if (facilityName.length > 50) {
            showToast('Facility name must be 50 characters or less', 'error');
            return;
        }

        if (description.length > 250) {
            showToast('Description must be 250 characters or less', 'error');
            return;
        }

        if (locationNote.length > 200) {
            showToast('Location note must be 200 characters or less', 'error');
            return;
        }

        if (buildingCode && buildingCode.length > 20) {
            showToast('Building code must be 20 characters or less', 'error');
            return;
        }

        if (roomCode && roomCode.length > 50) {
            showToast('Room code must be 50 characters or less', 'error');
            return;
        }

        // 1. First create the facility record
        const formData = {
            facility_name: facilityName,
            building_code: buildingCode || null,
            description: description || null,
            location_note: locationNote,
            location_type: document.getElementById('locationType').value,
            category_id: document.getElementById('category').value,
            subcategory_id: document.getElementById('subcategory').value || null,
            capacity: parseInt(document.getElementById('capacity').value),
            floor_level: document.getElementById('floorLevel').value ? parseInt(document.getElementById('floorLevel').value) : null,
            room_code: roomCode || null,
            external_fee: parseFloat(document.getElementById('rentalFee').value),
            rate_type: document.getElementById('rateType').value,
            total_levels: document.getElementById('totalLevels').value ? parseInt(document.getElementById('totalLevels').value) : null,
            total_rooms: document.getElementById('totalRooms').value ? parseInt(document.getElementById('totalRooms').value) : null,
            department_id: document.getElementById('department').value,
            status_id: document.getElementById('availabilityStatus').value,
            created_by: adminId 
        };

        const facilityResponse = await fetch(`http://127.0.0.1:8000/api/admin/add-facility`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        if (!facilityResponse.ok) {
            const errorData = await facilityResponse.json();
            console.error('Facility creation failed:', {
                status: facilityResponse.status,
                error: errorData,
                formData: { ...formData, created_by: 'REDACTED' } // Redact sensitive data
            });
            throw new Error(errorData.message || `Failed to create facility: ${facilityResponse.status}`);
        }

        const facilityResult = await facilityResponse.json();
        window.newFacilityId = facilityResult.data?.facility_id || facilityResult.facility_id;
        
        if (!window.newFacilityId) {
            console.error('No facility ID returned from API:', {
                response: facilityResult,
                adminId: adminId
            });
            throw new Error('Failed to get facility ID from server response');
        }

        showToast('Facility created successfully! Processing images...', 'success');

        // 2. Process facility image uploads with the new facility ID
        if (pendingImageUploads.length > 0) {
            for (const upload of pendingImageUploads) {
                try {
                    const cloudinaryData = await uploadToCloudinary(upload.file, window.newFacilityId);
                    await saveImageToDatabase(window.newFacilityId, cloudinaryData.secure_url, cloudinaryData.public_id);
                } catch (error) {
                    console.error('Error uploading facility image:', {
                        facilityId: window.newFacilityId,
                        error: error.message,
                        adminId: adminId
                    });
                    showToast('Warning: Failed to upload some images', 'warning');
                }
            }
        }

        // Clear pending changes
        pendingImageUploads = [];
        pendingImageDeletions = [];

        showToast('Facility created successfully!', 'success');
        setTimeout(() => {
            window.location.href = '/admin/manage-facilities';
        }, 1500);

    } catch (error) {
        console.error('Error creating facility:', {
            error: error.message,
            stack: error.stack,
            adminId: adminId
        });
        showToast('Failed to create facility: ' + error.message, 'error');
    }
});
            });
        </script>
    @endsection