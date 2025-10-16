@extends('layouts.admin')

@section('title', 'Add Facility')

@section('content')
    <style>
label.required::after {
  content: " *";
  color: red;
  font-weight: bold;
  margin-left: 4px; /* ensures spacing is always visible */
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
    <main>
        <!-- Edit Facility Page -->

        <div class="card-body">
            <form id="addFacilityForm">
                <input type="hidden" id="facilityId" value="{{ request()->get('id') }}">

                <!-- Overall Row Container -->
                <div class="row mb-4 align-items-stretch">

                    <!-- Row 1: Photos and Basic Facility Details -->
                    <div class="row mb-4">
                        <!-- Facility Details Card (now containing photos section) -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center"
                                    style="height: 56px;">
                                    <h5 class="fw-bold mb-0">Facility Details</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Photos and Basic Information Side by Side -->
                                    <div class="row">
                                        <!-- Photos Section - Left Column -->
                                        <div class="col-md-6">
                                            <div class="photo-section">
                                                <div class="dropzone border p-4 text-center" id="facilityPhotosDropzone"
                                                    style="cursor: pointer;">
                                                    <i class="bi bi-images fs-1 text-muted"></i>
                                                    <p class="mt-2">Drag & drop facility photos here or click to browse</p>
                                                    <input type="file" id="facilityPhotos" class="d-none" multiple
                                                        accept="image/*">
                                                </div>
                                                <small class="text-muted mt-2 d-block">
                                                    Upload at least one photo of the facility (max 5 photos)
                                                </small>
                                                <div id="photosPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                                            </div>
                                        </div>

                                        <!-- Basic Information Section - Right Column -->

                                        <div class="col-md-6">
                                            <div class="details-section">
                                                <!-- Facility Information Header -->
                                                <div class="row mb-3">
                                                    <div class="col-12 d-flex align-items-center">
                                                        <div class="flex-grow-1 border-top"></div>
                                                        <h6 class="text-center mx-3 fw-bold text-primary mb-0">Main
                                                            Information</h6>
                                                        <div class="flex-grow-1 border-top"></div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="facilityName"
                                                            class="form-label fw-bold d-flex align-items-center">
                                                            Facility Name
                                                        </label>
                                                        <input type="text" class="form-control" required
                                                            id="facilityName" value="" placeholder="Facility Name">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="buildingCode"
                                                            class="form-label fw-bold d-flex align-items-center">
                                                            Facility Code
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            id="buildingCode" value="" placeholder="Facility Code">
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-12 position-relative">
                                                        <label for="description"
                                                            class="form-label fw-bold d-flex align-items-center">
                                                            Description
                                                        </label>
                                                        <textarea class="form-control" id="description"
                                                            rows="3"
                                                            placeholder="Write a description..."></textarea>
                                                        <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1"
                                                            id="descriptionWordCount">0/250 characters</small>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                   
                                                                                                <!-- Rental Fee -->
                                            <div class="col-md-6">
                                                <label for="rentalFee" class="form-label fw-bold d-flex align-items-center">
                                                    Rental Fee (₱)
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" class="form-control" id="rentalFee" min="0"
                                                        step="0.01" required placeholder="0.00">
                                                </div>
                                            </div>
                                             <!-- Rate Type -->
                                            <div class="col-md-6">
                                                <label for="rateType" class="form-label fw-bold d-flex align-items-center">
                                                    Rate Type
                                                </label>
                                                <select class="form-select" id="rateType" required>
                                                    <option value="Per Hour">Per Hour</option>
                                                    <option value="Per Event">Per Event</option>
                                                </select>
                                            </div>

                                                  

                                                    <!-- Category and Subcategory row -->
                                                    <div class="row mt-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label for="category"
                                                                class="form-label fw-bold d-flex align-items-center">
                                                                Category
                                                            </label>
                                                            <select class="form-select" id="category" required>
                                                                <option value="">Select Category</option>
                                                                <!-- Categories populated dynamically -->
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="subcategory"
                                                                class="form-label fw-bold d-flex align-items-center">
                                                                Subcategory
                                                            </label>
                                                            <select class="form-select" id="subcategory" required>
                                                                <option value="">Select Subcategory</option>
                                                                <!-- Subcategories populated dynamically -->
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Divider: More Details -->
                                    <div class="row my-1">
                                        <div class="col-12 d-flex align-items-center">
                                            <div class="flex-grow-1 border-top"></div>
                                            <h6 class="text-center text-primary mx-3 mb-0 fw-bold">Additional Details</h6>
                                            <div class="flex-grow-1 border-top"></div>
                                        </div>
                                    </div>

                                    <!-- New Row: Category, Subcategory, and Capacity Section Below -->
                                    <div class="row my-3">

                                        <!-- Capacity & Location Section -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="capacity" class="form-label fw-bold d-flex align-items-center">
                                                    Capacity
                                                </label>
                                                <input type="number" class="form-control" id="capacity" min="1" value="1"
                                                    required>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="floorLevel"
                                                    class="form-label fw-bold d-flex align-items-center">
                                                    Floor Level
                                                </label>
                                                <input type="number" class="form-control" id="floorLevel" min="1"
                                                    placeholder="Floor level">
                                            </div>
                                                                                    <!-- Building Details Section -->
                                            <div class="col-md-3">
                                                <label for="totalLevels"
                                                    class="form-label fw-bold d-flex align-items-center">
                                                    Total Levels
                                                </label>
                                                <input type="number" class="form-control" id="totalLevels" min="1"
                                                    placeholder="Total Levels">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="totalRooms"
                                                    class="form-label fw-bold d-flex align-items-center">
                                                    Total Rooms
                                                </label>
                                                <input type="number" class="form-control" id="totalRooms" min="1"
                                                    placeholder="Total Rooms">
                                            </div>
                                        </div>

                                        <!-- Pricing Section -->
                                        <div class="row mb-3">
                                             <!-- Location Note: wider (8 of 12 columns) -->
                                                    <div class="col-md-9">
                                                        <label for="locationNote"
                                                            class="form-label fw-bold d-flex align-items-center">
                                                            Location Note
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            id="locationNote" value="" placeholder="Write directions to facility...">
                                                    </div>
                                                      <!-- Location Type: narrower (4 of 12 columns) -->
                                                    <div class="col-md-3">
                                                        <label for="locationType"
                                                            class="form-label fw-bold d-flex align-items-center">
                                                            Location Type
                                                        </label>
                                                        <select class="form-select" id="locationType" required>
                                                            <option value="Indoors">Indoors</option>
                                                            <option value="Outdoors">Outdoors</option>
                                                        </select>
                                                    </div>


                                           
                                        </div>
                                        <div class="row mb-3">
                                            <!-- Department & Availability Section -->
                                            <div class="col-md-6">
                                                <label for="department"
                                                    class="form-label fw-bold d-flex align-items-center">
                                                    Owning Department
                                                </label>
                                                <select class="form-select" id="department" required>
                                                    <!-- Departments will be populated dynamically -->
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="availabilityStatus"
                                                    class="form-label fw-bold d-flex align-items-center">
                                                    Availability Status
                                                </label>
                                                <select class="form-select" id="availabilityStatus" required>
                                                    <!-- Statuses will be populated dynamically -->
                                                </select>
                                            </div>
                                        </div>
                                       <!-- Form Actions -->
<div class="d-flex justify-content-end mt-3 gap-2">
    <button type="button" class="btn btn-secondary" id="cancelBtn">Discard</button>
    <button type="submit" class="btn btn-primary">Add New Facility</button> 
</div>

                                    </div>
                                </div>
                                
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this photo? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">Delete
                                    Photo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
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
            window.updatedFields = {}; // Track field changes for visual updates
            
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

                    // Populate subcategories if category has them
                    if (selectedCategory && selectedCategory.subcategories && selectedCategory.subcategories.length > 0) {
                        selectedCategory.subcategories.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.subcategory_id;
                            option.textContent = subcategory.subcategory_name;
                            subcategoryDropdown.appendChild(option);
                        });
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

            // 8. Image deletion handler - only tracks for later
            function handleImageDeletion(photoId, publicId, previewElement) {
                currentDeletePhotoId = photoId;
                currentDeletePublicId = publicId;
                currentDeletePreviewElement = previewElement;

                // Show the confirmation modal
                deleteImageModal.show();
            }

            // 9. Handle confirm delete button click - only tracks deletion for later processing
            document.getElementById('confirmDeleteImageBtn').addEventListener('click', function () {
                // Add to pending deletions for processing during form submission
                if (currentDeletePhotoId || currentDeletePublicId) {
                    window.pendingImageDeletions.push({
                        photoId: currentDeletePhotoId,
                        publicId: currentDeletePublicId,
                        previewElement: currentDeletePreviewElement
                    });
                }

                // Remove the preview element immediately (UI only)
                if (currentDeletePreviewElement) {
                    currentDeletePreviewElement.remove();
                }

                // Update the uploadedPhotos array (UI only)
                window.uploadedPhotos = window.uploadedPhotos.filter(photo => 
                    photo.id !== currentDeletePhotoId && photo.publicId !== currentDeletePublicId
                );

                // Hide the modal
                deleteImageModal.hide();
                showToast('Image will be deleted after saving.', 'success');
            });

            // 10. Modified function to only track files for later upload
            async function handleFacilityFiles(files) {
                const photosPreview = document.getElementById('photosPreview');

                for (const file of files) {
                    // Check if file is an image
                    if (!file.type.startsWith('image/')) {
                        showToast('Please upload only image files', 'error');
                        continue;
                    }

                    // Check if we've reached the maximum of 5 photos (including pending)
                    const totalPhotos = (window.uploadedPhotos?.length || 0) + (window.pendingImageUploads?.length || 0);
                    if (totalPhotos >= 5) {
                        showToast('Maximum of 5 photos allowed', 'error');
                        break;
                    }

                    try {
                        const previewId = 'pending_' + Date.now();

                        // Create a preview only (no upload yet)
                        const reader = new FileReader();
                        reader.onload = (e) => {
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
                                // Remove from pending uploads
                                window.pendingImageUploads = window.pendingImageUploads.filter(
                                    upload => upload.previewId !== previewId
                                );
                            };

                            preview.appendChild(img);
                            preview.appendChild(removeBtn);
                            photosPreview.appendChild(preview);

                            // Store file and preview info for later processing
                            window.pendingImageUploads.push({
                                file: file,
                                previewId: previewId,
                                previewElement: preview
                            });
                        };
                        reader.readAsDataURL(file);

                        showToast('Image added!', 'success');

                    } catch (error) {
                        console.error('Error processing file:', error);
                        showToast('Failed to process file: ' + error.message, 'error');
                    }
                }
            }

            // 11. Cloudinary direct upload implementation (now only called during form submission)
            async function uploadToCloudinary(file, facilityId) {
                const CLOUD_NAME = 'dn98ntlkd';
                const UPLOAD_PRESET = 'facility-photos';

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
                    throw error;
                }
            }

            // 12. Function to save image reference to your database (now only called during form submission)
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
                    throw error;
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

                // Then redirect to manage-facilities
                window.location.href = '/admin/manage-facilities';
            });

            // 15. Facility Photos Section
            const facilityDropzone = document.getElementById('facilityPhotosDropzone');
            const facilityFileInput = document.getElementById('facilityPhotos');
            const photosPreview = document.getElementById('photosPreview');
            window.uploadedPhotos = []; // Track existing photos

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

            // 16. Word count limiter for Description textbox
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

            // 17. Initialize new facilities form
            async function initializeNewFacilityForm() {
                try {
                    // Fetch dropdown data
                    await fetchDropdownData();
                    // Fetch categories with subcategories
                    await fetchCategoriesWithSubcategories();
                    // Setup category change handler
                    setupCategoryChangeHandler();

                } catch (error) {
                    console.error('Error initializing new facility form:', error);
                    showToast('Failed to initialize form: ' + error.message, 'error');
                }
            }

            // 18. Add 'required' class to labels with required fields
            document.querySelectorAll('label[for]').forEach(label => {
                const input = document.getElementById(label.getAttribute('for'));
                if (input && input.hasAttribute('required')) {
                    label.classList.add('required');
                }
            });

            // 19. Fetch dropdown data function
            async function fetchDropdownData() {
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
                            populateDropdown('availabilityStatus', statusesData, null, 'status_id', 'status_name');
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
                            populateDropdown('department', departmentsData, null, 'department_id', 'department_name');
                        }
                    } else {
                        console.error('Failed to fetch departments:', departmentsResponse.status);
                    }

                } catch (error) {
                    console.error('Error fetching dropdown data:', error);
                }
            }

            // 20. Populate dropdown function
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
                    dropdown.appendChild(option);
                });
            }

// 21. Form submission handler - processes all pending changes when "Add Facility" is clicked
document.getElementById('addFacilityForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const token = localStorage.getItem('adminToken');
    const adminId = localStorage.getItem('adminId');

    try {
        showToast('Creating facility...', 'info');

        // Validate required fields (locationNote removed)
        const requiredFields = [
            'facilityName', 'capacity', 'category', 
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
        const buildingCode = document.getElementById('buildingCode').value;

        if (facilityName.length > 50) {
            showToast('Facility name must be 50 characters or less', 'error');
            return;
        }

        if (description.length > 250) {
            showToast('Description must be 250 characters or less', 'error');
            return;
        }

        if (buildingCode && buildingCode.length > 20) {
            showToast('Building code must be 20 characters or less', 'error');
            return;
        }

        // 1. First create the facility record
        const formData = {
            facility_name: facilityName,
            building_code: buildingCode || null,
            location_type: document.getElementById('locationType').value,
            category_id: document.getElementById('category').value,
            subcategory_id: document.getElementById('subcategory').value || null,
            capacity: parseInt(document.getElementById('capacity').value),
            floor_level: document.getElementById('floorLevel').value ? parseInt(document.getElementById('floorLevel').value) : null,
            external_fee: parseFloat(document.getElementById('rentalFee').value),
            rate_type: document.getElementById('rateType').value,
            total_levels: document.getElementById('totalLevels').value ? parseInt(document.getElementById('totalLevels').value) : null,
            total_rooms: document.getElementById('totalRooms').value ? parseInt(document.getElementById('totalRooms').value) : null,
            department_id: document.getElementById('department').value,
            status_id: document.getElementById('availabilityStatus').value,
            created_by: adminId,
            // Add default values for description and location_note
            description: document.getElementById('description').value.trim() || 'No description provided',
            location_note: document.getElementById('locationNote').value.trim() || 'No location details provided'
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
                formData: { ...formData, created_by: 'REDACTED' }
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

        // 2. Process facility image uploads with the new facility ID
        if (window.pendingImageUploads.length > 0) {
            for (const upload of window.pendingImageUploads) {
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
        window.pendingImageUploads = [];
        window.pendingImageDeletions = [];
        window.updatedFields = {};

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

            // 22. Initialize the form
            initializeNewFacilityForm();
        });
    </script>
@endsection