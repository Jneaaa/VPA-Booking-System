@extends('layouts.admin')

@section('title', 'Add Equipment')

@section('content')
    <style>
        label.required::after {
            content: " *";
            color: red;
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

        .barcode-container {
            margin-top: 10px;
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .barcode-preview {
            max-width: 100%;
            height: auto;
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .barcode-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            margin-top: 8px;
            font-size: 14px;
            letter-spacing: 1px;
            color: #333
        }

        #barcode {
            background-color: #f8f9fa;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        #generateBarcodeBtn {
            white-space: nowrap;
        }
    </style>
    <div class="container-fluid px-4">
        <!-- Main Layout -->
        <div id="layout">
            <!-- Main Content -->
            <main>
                    <div class="card-body">
                        <form id="addEquipmentForm">
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
                                                        <label for="equipmentName" class="form-label fw-bold">
                                                            Equipment Name
                                                        </label>
                                                        <input type="text" class="form-control" id="equipmentName"
                                                            placeholder="Equipment Name" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="brand" class="form-label fw-bold">
                                                            Brand
                                                        </label>
                                                        <input type="text" class="form-control" id="brand"
                                                            placeholder="Brand">
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <div class="col-12 position-relative">
                                                        <label for="description" class="form-label fw-bold">
                                                            Description
                                                        </label>
                                                        <textarea class="form-control" id="description" rows="3"
                                                            placeholder="Write a short description..."></textarea>
                                                        <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1"
                                                            id="descriptionWordCount">0/255 characters</small>
                                                    </div>
                                                </div>

                                               <div class="row mb-4">
    <div class="col-md-3">
        <label for="storageLocation" class="form-label fw-bold">Storage Location</label>
        <input type="text" class="form-control" id="storageLocation" placeholder="Storage Location" required>
    </div>

    <div class="col-md-3">
        <label for="category" class="form-label fw-bold">Category</label>
        <select class="form-select" id="category" required>
            <option value="">Select Category</option>
            <!-- Categories will be populated dynamically -->
        </select>
    </div>

    <div class="col-md-3">
        <label for="companyFee" class="form-label fw-bold">Rental Fee (₱)</label>
        <div class="input-group">
            <span class="input-group-text">₱</span>
            <input type="number" class="form-control" id="companyFee" min="0" step="0.01" required placeholder="0.00">
        </div>
    </div>

    <div class="col-md-3">
        <label for="availabilityStatus" class="form-label fw-bold">Availability Status</label>
        <select class="form-select" id="availabilityStatus" required>
            <!-- Statuses will be populated dynamically -->
        </select>
    </div>
</div>


                                                <div class="row mb-4">
                                                    <div class="col-md-4">
                                                        <label for="rateType" class="form-label fw-bold">Rate Type</label>
                                                        <select class="form-select" id="rateType" required>
                                                            <option value="">Select Rate Type</option>
                                                            <option value="Per Hour">Per Hour</option>
                                                            <option value="Per Event">Per Event</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="department" class="form-label fw-bold">Owning
                                                            Department</label>
                                                        <select class="form-select" id="department" required>
                                                            <!-- Departments will be populated dynamically -->
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="minRentalHours" class="form-label fw-bold">Min. Rental
                                                            Duration (hours)</label>
                                                        <input type="number" class="form-control" id="minRentalHours"
                                                            min="1" value="1" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                           <!-- Form Actions -->
<div class="d-flex justify-content-center gap-2 mt-4">
    <button type="submit" class="btn btn-primary">Add Equipment</button>
    <button type="button" class="btn btn-secondary" id="cancelBtn">Discard</button>
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
                        Are you sure you want to cancel? All entered data will be lost.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmCancelBtn">Confirm</button>
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
                                <input type="text" class="form-control" id="itemName" placeholder="Enter item name"
                                    required>
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
                                <div class="input-group">
                                    <input type="text" class="form-control" id="barcode" name="barcode"
                                        placeholder="Generate barcode" readonly>
                                    <button class="btn btn-outline-primary" type="button" id="generateBarcodeBtn">
                                        <i class="bi bi-upc-scan"></i> Generate
                                    </button>
                                </div>

                                <!-- Barcode Preview -->
                                <div class="barcode-container d-none mt-3" id="barcodeContainer">
                                    <canvas id="barcodePreview" class="barcode-preview"></canvas>
                                    <div class="barcode-number" id="barcodeNumberDisplay"></div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="downloadBarcodeBtn">
                                            <i class="bi bi-download"></i> Download
                                        </button>
                                    </div>
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
@endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // 1. Global variables first
                window.equipmentItems = [];
                window.currentEditingItemId = null;
                window.pendingImageUploads = []; // Track images to be uploaded
                window.pendingImageDeletions = []; // Track images to be deleted
                window.pendingItemPhotoChanges = new Map(); // Track item photo changes (itemId -> {action, file, publicId})
                window.newEquipmentId = null; // Will store the ID after equipment is created

                // 2. Authentication check
                const token = localStorage.getItem('adminToken');
                if (!token) {
                    window.location.href = '/admin/admin-login';
                    return;
                }

                // 3. Delete item function (needs to be defined before event handlers)
                window.deleteItem = async function (itemId, cloudinaryPublicId, event) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    if (!confirm('Are you sure you want to delete this item? The change will be applied when you click "Add Equipment".')) {
                        return;
                    }

                    try {
                        // Mark for deletion (will be processed on form submit)
                        pendingItemPhotoChanges.set(itemId, {
                            action: 'delete',
                            publicId: cloudinaryPublicId
                        });

                        // Remove from UI immediately
                        document.querySelector(`.equipment-item[data-item-id="${itemId}"]`)?.remove();

                    } catch (error) {
                        console.error('Error staging item deletion:', error);
                        showToast('Failed to stage item deletion: ' + error.message, 'error');
                    }
                };

                // 4. Open edit item modal function (needs to be defined before event handlers)
                window.openEditItemModal = function (itemId, event) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    const item = window.equipmentItems.find(i => i.item_id === itemId);
                    if (!item) return;

                    // Fill the form with existing data
                    document.getElementById('itemName').value = item.item_name;
                    document.getElementById('itemCondition').value = item.condition_id;
                    document.getElementById('barcode').value = item.barcode_number || '';
                    document.getElementById('itemNotes').value = item.item_notes || '';

                    // Set the photo preview
                    const itemPhotoPreview = document.getElementById('itemPhotoPreview');
                    const itemPhotoDropzone = document.getElementById('itemPhotoDropzone');
                    const removePhotoBtn = document.getElementById('removePhotoBtn');

                    if (item.image_url && item.image_url !== 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp') {
                        itemPhotoPreview.innerHTML = `<img src="${item.image_url}" class="img-thumbnail" style="max-height: 150px;">`;
                        itemPhotoDropzone.style.display = 'none';
                        removePhotoBtn.classList.remove('d-none');
                    } else {
                        itemPhotoPreview.innerHTML = '';
                        itemPhotoDropzone.style.display = 'block';
                        removePhotoBtn.classList.add('d-none');
                    }

                    // Store the current editing item ID
                    window.currentEditingItemId = itemId;

                    // Change modal title and button text
                    document.querySelector('#inventoryItemModal .modal-title').textContent = 'Edit Inventory Item';
                    document.getElementById('saveItemBtn').textContent = 'Update Item';

                    // Show the modal
                    bootstrap.Modal.getOrCreateInstance('#inventoryItemModal').show();
                };

                // 5. Toast notification function
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
                async function uploadToCloudinary(file, equipmentId) {
                    const CLOUD_NAME = 'dn98ntlkd'; // Your Cloudinary cloud name
                    const UPLOAD_PRESET = 'equipment-photos'; // Your unsigned upload preset

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('upload_preset', UPLOAD_PRESET);
                    formData.append('folder', `equipment-photos/${equipmentId}`);
                    formData.append('tags', `equipment_${equipmentId}`);

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

                // 12. Equipment file handling function
                async function handleEquipmentFiles(files) {
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
                    document.getElementById('equipmentName').value = '';
                    document.getElementById('description').value = '';
                    document.getElementById('brand').value = '';
                    document.getElementById('storageLocation').value = '';
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
                        itemsContainer.innerHTML = '<p class="text-muted">No items added yet. Click "Add Item" to track individual equipment pieces.</p>';
                    }

                    equipmentItems = [];
                }

                // 15. Equipment Photos Section
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

                // 16. Word count limiter for Description textbox
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

                               // 18. Initialize Inventory Item Modal
                const addItemBtn = document.getElementById('addItemBtn');
                if (addItemBtn) {
                    const inventoryItemModal = new bootstrap.Modal('#inventoryItemModal');
                    const itemPhotoInput = document.getElementById('itemPhoto');
                    const itemPhotoPreview = document.getElementById('itemPhotoPreview');
                    const itemNotes = document.getElementById('itemNotes');
                    const notesWordCount = document.getElementById('notesWordCount');
                    const saveItemBtn = document.getElementById('saveItemBtn');
                    const itemsContainer = document.getElementById('itemsContainer');
                    const removePhotoBtn = document.getElementById('removePhotoBtn');
                    const itemPhotoDropzone = document.getElementById('itemPhotoDropzone');
                    const generateBarcodeBtn = document.getElementById('generateBarcodeBtn');
                    const barcodeContainer = document.getElementById('barcodeContainer');
                    const barcodePreview = document.getElementById('barcodePreview');
                    const downloadBarcodeBtn = document.getElementById('downloadBarcodeBtn');
                    const barcodeInput = document.getElementById('barcode');

                    let itemPhotoFile = null;
                    let itemCloudinaryPublicId = null;

                    addItemBtn.addEventListener('click', () => {
                        document.getElementById('itemForm').reset();
                        itemPhotoPreview.innerHTML = '';
                        itemPhotoFile = null;
                        itemCloudinaryPublicId = null;
                        if (itemPhotoDropzone) itemPhotoDropzone.style.display = 'block';
                        if (removePhotoBtn) removePhotoBtn.classList.add('d-none');
                        inventoryItemModal.show();
                    });

                    // Handle item photo upload
                    if (itemPhotoInput) {
                        itemPhotoInput.addEventListener('change', function () {
                            if (this.files?.[0]) {
                                itemPhotoFile = this.files[0];
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    if (itemPhotoDropzone) itemPhotoDropzone.style.display = 'none';
                                    if (removePhotoBtn) removePhotoBtn.classList.remove('d-none');
                                    if (itemPhotoPreview) {
                                        itemPhotoPreview.innerHTML = `
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                                `;
                                    }

                                    // Store the file for later processing
                                    itemPhotoFile = this.files[0];
                                };
                                reader.readAsDataURL(itemPhotoFile);
                            }
                        });
                    }

                    // Handle photo removal
                    if (removePhotoBtn) {
                        removePhotoBtn.addEventListener('click', function () {
                            if (itemPhotoPreview) itemPhotoPreview.innerHTML = '';
                            if (itemPhotoDropzone) itemPhotoDropzone.style.display = 'block';
                            if (itemPhotoInput) itemPhotoInput.value = '';
                            this.classList.add('d-none');
                            itemPhotoFile = null;

                            // If editing an existing item, mark the current photo for deletion
                            if (currentEditingItemId) {
                                const existingItem = equipmentItems.find(i => i.item_id === currentEditingItemId);
                                if (existingItem && existingItem.cloudinary_public_id !== 'oxvsxogzu9koqhctnf7s') {
                                    pendingItemPhotoChanges.set(currentEditingItemId, {
                                        action: 'delete',
                                        publicId: existingItem.cloudinary_public_id
                                    });
                                }
                            }
                        });
                    }

                    // Handle dropzone click
                    if (itemPhotoDropzone) {
                        itemPhotoDropzone.addEventListener('click', function () {
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

                    // Handle barcode generation - FIXED VERSION
                    if (generateBarcodeBtn && barcodeInput) {
                        generateBarcodeBtn.addEventListener('click', function () {
                            // Generate a unique barcode value if empty
                            const barcodeValue = barcodeInput.value.trim() || `EQ${Date.now()}${Math.random().toString(36).substr(2, 5)}`;
                            barcodeInput.value = barcodeValue;

                            // Generate barcode
                            try {
                                // Use canvas instead of img for better download functionality
                                const canvas = document.getElementById('barcodePreview');
                                
                                JsBarcode(canvas, barcodeValue, {
                                    format: "CODE128",
                                    lineColor: "#000",
                                    width: 2,
                                    height: 40,
                                    displayValue: true,
                                    fontSize: 14,
                                    margin: 10
                                });

                                // Update barcode number display
                                const barcodeNumberDisplay = document.getElementById('barcodeNumberDisplay');
                                if (barcodeNumberDisplay) {
                                    barcodeNumberDisplay.textContent = barcodeValue;
                                }

                                // Show the barcode container
                                barcodeContainer.classList.remove('d-none');
                                
                                showToast('Barcode generated successfully!', 'success');
                            } catch (error) {
                                console.error('Barcode generation error:', error);
                                showToast('Failed to generate barcode: ' + error.message, 'error');
                            }
                        });
                    }

                    // Handle barcode download - FIXED VERSION
                    if (downloadBarcodeBtn) {
                        downloadBarcodeBtn.addEventListener('click', function () {
                            const canvas = document.getElementById('barcodePreview');
                            const barcodeValue = barcodeInput.value.trim() || 'barcode';
                            
                            if (!canvas) {
                                showToast('Please generate a barcode first', 'error');
                                return;
                            }

                            try {
                                // Create a download link
                                const link = document.createElement('a');
                                link.download = `barcode-${barcodeValue}.png`;
                                link.href = canvas.toDataURL('image/png');
                                
                                // Trigger download
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                                
                                showToast('Barcode downloaded successfully!', 'success');
                            } catch (error) {
                                console.error('Barcode download error:', error);
                                showToast('Failed to download barcode: ' + error.message, 'error');
                            }
                        });
                    }

                    // Save item functionality
                    if (saveItemBtn) {
                        saveItemBtn.addEventListener('click', async function () {
                            const itemName = document.getElementById('itemName')?.value;
                            const itemCondition = document.getElementById('itemCondition')?.value;
                            const barcode = document.getElementById('barcode')?.value || '';
                            const notes = document.getElementById('itemNotes')?.value || '';

                            if (!itemName || !itemCondition) {
                                showToast('Please fill in all required fields', 'error');
                                return;
                            }

                            //Barcode Validation
                            if (!barcode){
                                showToast('Please generate a barcode first','error');
                                return;
                            }

                            try {
                                let imageUrl = 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp';
                                let publicId = 'oxvsxogzu9koqhctnf7s';

                                // Create a temporary item object
                                const tempItem = {
                                    item_id: currentEditingItemId || 'temp-' + Date.now(),
                                    item_name: itemName,
                                    condition_id: itemCondition,
                                    condition: { condition_name: document.getElementById('itemCondition').options[document.getElementById('itemCondition').selectedIndex].text },
                                    barcode_number: barcode,
                                    item_notes: notes,
                                    image_url: imageUrl,
                                    cloudinary_public_id: publicId
                                };

                                // Handle photo changes
                                if (itemPhotoFile) {
                                    // Store file for processing on form submit
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        tempItem.image_url = e.target.result;

                                        // Update UI with temporary image
                                        if (currentEditingItemId) {
                                            updateItemInUI(tempItem);
                                        } else {
                                            addItemToUI(tempItem);
                                        }
                                    };
                                    reader.readAsDataURL(itemPhotoFile);

                                    // Store for processing on form submit
                                    pendingItemPhotoChanges.set(tempItem.item_id, {
                                        action: currentEditingItemId ? 'update' : 'create',
                                        itemData: {
                                            item_name: itemName,
                                            condition_id: itemCondition,
                                            barcode_number: barcode,
                                            item_notes: notes
                                        },
                                        file: itemPhotoFile
                                    });
                                } else if (currentEditingItemId) {
                                    // Keep existing photo if not changed
                                    const existingItem = equipmentItems.find(i => i.item_id === currentEditingItemId);
                                    if (existingItem) {
                                        tempItem.image_url = existingItem.image_url;
                                        tempItem.cloudinary_public_id = existingItem.cloudinary_public_id;
                                    }

                                    // Mark for update (no photo change)
                                    pendingItemPhotoChanges.set(currentEditingItemId, {
                                        action: 'update',
                                        itemData: {
                                            item_name: itemName,
                                            condition_id: itemCondition,
                                            barcode_number: barcode,
                                            item_notes: notes
                                        }
                                    });

                                    updateItemInUI(tempItem);
                                } else {
                                    // New item without photo
                                    addItemToUI(tempItem);
                                    pendingItemPhotoChanges.set(tempItem.item_id, {
                                        action: 'create',
                                        itemData: {
                                            item_name: itemName,
                                            condition_id: itemCondition,
                                            barcode_number: barcode,
                                            item_notes: notes
                                        }
                                    });
                                }

                                //  showToast(currentEditingItemId ? 'Item changes staged!' : 'Item staged for creation!', 'success');//
                                inventoryItemModal.hide();
                                currentEditingItemId = null;

                            } catch (error) {
                                console.error('Error saving item:', error);
                                showToast('Failed to save item: ' + error.message, 'error');
                            }
                        });
                    }
                    // Handle confirm delete button click
                    document.getElementById('confirmDeleteImageBtn').addEventListener('click', async function () {
                        try {
                            // Delete from Cloudinary first if public ID exists
                            if (currentDeletePublicId) {
                                await deleteImageFromCloudinary(currentDeletePublicId);
                            }

                            // Then delete from database if it's a saved image (has photoId)
                            if (currentDeletePhotoId && typeof currentDeletePhotoId === 'number') {
                                // For new equipment, we don't have equipment ID yet, so just remove from UI
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
                }

                // 18. UI update functions
                function updateItemInUI(updatedItem) {
                    const itemElement = document.querySelector(`.equipment-item[data-item-id="${updatedItem.item_id}"]`);
                    if (!itemElement) return;

                    const conditionColors = {
                        "New": "bg-success text-white",
                        "Good": "bg-primary text-white",
                        "Fair": "bg-warning text-dark",
                        "Needs Maintenance": "bg-danger text-white",
                        "Damaged": "bg-dark text-white"
                    };

                    itemElement.innerHTML = `
                                <div class="card-body">
                                    <div class="photo-container">
                                        <img src="${updatedItem.image_url}" class="img-thumbnail">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title">${updatedItem.item_name}</h6>
                                        <div class="d-flex flex-wrap gap-3">
                                            <span class="badge ${conditionColors[updatedItem.condition.condition_name] || 'bg-secondary'}">${updatedItem.condition.condition_name}</span>
                                        </div>
                                        ${updatedItem.barcode_number ? `<div class="mt-2"><strong>Barcode:</strong> ${updatedItem.barcode_number}</div>` : ''}
                                        ${updatedItem.item_notes ? `<p class="mt-2 mb-0"><strong>Notes:</strong> ${updatedItem.item_notes.substring(0, 50)}${updatedItem.item_notes.length > 50 ? '...' : ''}</p>` : ''}
                                    </div>
                                    <div class="d-flex align-self-start">
                                        <button type="button" class="btn btn-sm btn-primary me-1" onclick="openEditItemModal('${updatedItem.item_id}', event)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem('${updatedItem.item_id}', '${updatedItem.cloudinary_public_id}', event)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                }

                function addItemToUI(item) {
                    const conditionColors = {
                        "New": "bg-success text-white",
                        "Good": "bg-primary text-white",
                        "Fair": "bg-warning text-dark",
                        "Needs Maintenance": "bg-danger text-white",
                        "Damaged": "bg-dark text-white"
                    };

                    const itemCard = document.createElement('div');
                    itemCard.className = 'card equipment-item';
                    itemCard.dataset.itemId = item.item_id;
                    itemCard.innerHTML = `
                                <div class="card-body">
                                    <div class="photo-container">
                                        <img src="${item.image_url}" class="img-thumbnail">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title">${item.item_name}</h6>
                                        <div class="d-flex flex-wrap gap-3">
                                            <span class="badge ${conditionColors[item.condition.condition_name] || 'bg-secondary'}">${item.condition.condition_name}</span>
                                        </div>
                                        ${item.barcode_number ? `<div class="mt-2"><strong>Barcode:</strong> ${item.barcode_number}</div>` : ''}
                                        ${item.item_notes ? `<p class="mt-2 mb-0"><strong>Notes:</strong> ${item.item_notes.substring(0, 50)}${item.item_notes.length > 50 ? '...' : ''}</p>` : ''}
                                    </div>
                                    <div class="d-flex align-self-start">
                                        <button type="button" class="btn btn-sm btn-primary me-1" onclick="openEditItemModal('${item.item_id}', event)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem('${item.item_id}', '${item.cloudinary_public_id}', event)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;

                    if (itemsContainer) {
                        if (itemsContainer.querySelector('p.text-muted')) {
                            itemsContainer.innerHTML = '';
                        }
                        itemsContainer.appendChild(itemCard);
                    }
                }

                // 19. Function to upload item photo to Cloudinary
                async function uploadItemToCloudinary(file, equipmentId) {
                    const CLOUD_NAME = 'dn98ntlkd';
                    const UPLOAD_PRESET = 'equipment-photos';

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('upload_preset', UPLOAD_PRESET);
                    formData.append('folder', `equipment-items/${equipmentId}`);
                    formData.append('tags', `equipment_item_${equipmentId}`);

                    try {
                        const response = await fetch(`https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`, {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.error?.message || 'Upload failed');
                        }

                        return await response.json();

                    } catch (error) {
                        console.error('Cloudinary upload error:', error);
                        throw new Error('Failed to upload image: ' + error.message);
                    }
                }

                // 20. Initialize new equipment form
                async function initializeNewEquipmentForm() {
                    try {
                        // Clear form fields
                        document.getElementById('equipmentName').value = '';
                        document.getElementById('description').value = '';
                        document.getElementById('brand').value = '';
                        document.getElementById('storageLocation').value = '';
                        document.getElementById('companyFee').value = '';
                        document.getElementById('minRentalHours').value = 1;

                        // Clear images and items
                        document.getElementById('photosPreview').innerHTML = '';
                        document.getElementById('itemsContainer').innerHTML = '<p class="text-muted">No items added yet. Click "Add Item" to track individual equipment pieces.</p>';

                        // Fetch dropdown data with null values
                        await fetchDropdownData(null);

                        // Reset pending changes
                        pendingImageUploads = [];
                        pendingImageDeletions = [];
                        pendingItemPhotoChanges.clear();
                        equipmentItems = [];

                    } catch (error) {
                        console.error('Error initializing new equipment form:', error);
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

                // 22. Initialize new equipment form
                initializeNewEquipmentForm();

                // 23. Fetch dropdown data function
                async function fetchDropdownData(equipment) {
                    try {
                        const token = localStorage.getItem('adminToken');

                        // Fetch categories
                        const categoriesResponse = await fetch('http://127.0.0.1:8000/api/equipment-categories', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (categoriesResponse.ok) {
                            const categoriesData = await categoriesResponse.json();
                            if (Array.isArray(categoriesData)) {
                                populateDropdown('category', categoriesData, equipment ? equipment.category_id : null, 'category_id', 'category_name');
                            }
                        } else {
                            console.error('Failed to fetch categories:', categoriesResponse.status);
                        }

                        // Fetch statuses
                        const statusesResponse = await fetch('http://127.0.0.1:8000/api/availability-statuses', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (statusesResponse.ok) {
                            const statusesData = await statusesResponse.json();
                            if (Array.isArray(statusesData)) {
                                populateDropdown('availabilityStatus', statusesData, equipment ? equipment.status_id : 1, 'status_id', 'status_name');
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
                                populateDropdown('department', departmentsData, equipment ? equipment.department_id : null, 'department_id', 'department_name');
                            }
                        } else {
                            console.error('Failed to fetch departments:', departmentsResponse.status);
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
                            if (Array.isArray(conditionsData)) {
                                populateDropdown('itemCondition', conditionsData, null, 'condition_id', 'condition_name');
                            }
                        } else {
                            console.error('Failed to fetch conditions:', conditionsResponse.status);
                        }

                    } catch (error) {
                        console.error('Error fetching dropdown data:', error);
                        // Log error without exposing sensitive data
                        console.log('Dropdown fetch error details:', {
                            message: error.message,
                            stack: error.stack
                        });
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

                // 25. Form submission handler - FIXED FIELD NAMES
                document.getElementById('addEquipmentForm').addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const token = localStorage.getItem('adminToken');

                    try {
                        showToast('Creating equipment...', 'info');

                        // 1. First create the equipment record - FIXED FIELD NAMES
                        const formData = {
                            equipment_name: document.getElementById('equipmentName').value,
                            description: document.getElementById('description').value,
                            brand: document.getElementById('brand').value,
                            storage_location: document.getElementById('storageLocation').value,
                            category_id: document.getElementById('category').value,
                            external_fee: document.getElementById('companyFee').value,
                            rate_type: document.getElementById('rateType').value,
                            status_id: document.getElementById('availabilityStatus').value,
                            department_id: document.getElementById('department').value,
                            maximum_rental_hour: document.getElementById('minRentalHours').value,
                        };

                        const equipmentResponse = await fetch(`http://127.0.0.1:8000/api/admin/equipment`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        });

                        if (!equipmentResponse.ok) {
                            const errorData = await equipmentResponse.json();
                            throw new Error(errorData.message || 'Failed to create equipment');
                        }

                        const equipmentResult = await equipmentResponse.json();
                        window.newEquipmentId = equipmentResult.data.equipment_id;
                        showToast('Equipment created successfully! Processing images and items...', 'success');

                        // 2. Process equipment image uploads with the new equipment ID
                        for (const upload of pendingImageUploads) {
                            try {
                                const cloudinaryData = await uploadToCloudinary(upload.file, window.newEquipmentId);
                                await saveImageToDatabase(window.newEquipmentId, cloudinaryData.secure_url, cloudinaryData.public_id);
                            } catch (error) {
                                console.error('Error uploading equipment image:', error);
                                showToast('Warning: Failed to upload some images', 'warning');
                            }
                        }

                        // 3. Process item changes with the new equipment ID
                        for (const [itemId, change] of pendingItemPhotoChanges.entries()) {
                            try {
                                if (change.action === 'delete') {
                                    // Skip deletion for new equipment (items shouldn't exist yet)
                                    continue;
                                } else if (change.action === 'create') {
                                    // Create new item
                                    let imageUrl = 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp';
                                    let publicId = 'oxvsxogzu9koqhctnf7s';

                                    if (change.file) {
                                        const cloudinaryData = await uploadItemToCloudinary(change.file, window.newEquipmentId);
                                        imageUrl = cloudinaryData.secure_url;
                                        publicId = cloudinaryData.public_id;
                                    }

                                    const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${window.newEquipmentId}/items`, {
                                        method: 'POST',
                                        headers: {
                                            'Authorization': `Bearer ${token}`,
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            ...change.itemData,
                                            image_url: imageUrl,
                                            cloudinary_public_id: publicId
                                        })
                                    });

                                    if (!response.ok) throw new Error('Failed to create item');
                                }
                            } catch (error) {
                                console.error('Error processing item change:', error);
                                showToast('Warning: Failed to process some item changes', 'warning');
                            }
                        }

                        // Clear pending changes
                        pendingImageUploads = [];
                        pendingImageDeletions = [];
                        pendingItemPhotoChanges.clear();

                        showToast('Equipment created successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = '/admin/manage-equipment';
                        }, 1500);

                    } catch (error) {
                        console.error('Error creating equipment:', error);
                        // Log error without exposing sensitive data
                        console.log('Equipment creation error details:', {
                            message: error.message,
                            stack: error.stack
                        });
                        showToast('Failed to create equipment: ' + error.message, 'error');
                    }
                });
            });
        </script>
    @endsection