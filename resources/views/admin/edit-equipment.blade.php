@extends('layouts.admin')

@section('title', 'Edit Equipment')

@section('content')
    <style>

.card-body.position-relative {
    min-height: 200px;
}

#itemsLoading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
    width: 100%;
}

#itemsEmptyState {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
    width: 100%;
}


        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        label.required::after {
            content: " *";
            color: red;
        }

        #barcode[readonly] {
            background-color: #e9ecef;
            opacity: 0.7;
            cursor: not-allowed;
        }

        .barcode-container {
            margin-top: 10px;
            text-align: center;
        }

        .barcode-preview {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            background: white;
        }

        #itemsContainer {
            min-height: 100px;
            max-height: 450px;
            /* Adjust this value as needed */
            overflow-y: auto;
            padding: 10px;
            border: none;
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

    <main>
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
                                <h5 class="fw-bold mb-0">Equipment Cover</h5>
                            </div>
                            <div class="card-body">
                                <div class="photo-section">
                                    <div class="dropzone border p-4 text-center" id="equipmentPhotosDropzone"
                                        style="cursor: pointer;">
                                        <i class="bi bi-images fs-1 text-muted"></i>
                                        <p class="mt-2">Drag & drop equipment photos here or click to browse</p>
                                        <input type="file" id="equipmentPhotos" class="d-none" multiple accept="image/*">
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
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">Delete
                                        Photo</button>
                                </div>
                            </div>
                        </div>
                    </div>


<!-- Inventory Items Card -->
<div class="col-md-6">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center" style="height: 56px;">
            <h5 class="fw-bold mb-0">Inventory Items</h5>
            <button type="button" class="btn btn-sm btn-secondary" id="addItemBtn">
                <i class="bi bi-plus me-1"></i>Add Item
            </button>
        </div>
        <div class="card-body position-relative" style="min-height: 300px;">
            <!-- Loading State -->
            <div id="itemsLoading" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading items...</span>
                </div>
                <p class="mt-2 text-muted">Loading items...</p>
            </div>

            <!-- Empty State -->
            <div id="itemsEmptyState" class="text-center py-4 d-none">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No items added yet.</p>
                <p class="text-muted small">Click "Add Item" to track individual equipment pieces.</p>
            </div>

            <!-- Items Container -->
            <div id="itemsContainer" class="d-none">
                <!-- Items will be populated here -->
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
                            <label for="equipmentName" class="form-label fw-bold d-flex align-items-center">
                                Equipment Name
                                <i class="bi bi-pencil text-secondary ms-2 edit-icon" data-field="equipmentName"
                                    style="cursor: pointer;"></i>
                                <div class="edit-actions ms-2 d-none" data-field="equipmentName">
                                    <button type="button" class="btn btn-sm btn-success me-1 save-btn">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger cancel-btn">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </label>
                            <input type="text" class="form-control text-secondary" id="equipmentName" value=""
                                readonly>
                        </div>

                        <div class="col-md-6">
                            <label for="brand" class="form-label fw-bold d-flex align-items-center">
                                Brand
                                <i class="bi bi-pencil text-secondary ms-2 edit-icon" data-field="brand"
                                    style="cursor: pointer;"></i>
                                <div class="edit-actions ms-2 d-none" data-field="brand">
                                    <button type="button" class="btn btn-sm btn-success me-1 save-btn">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger cancel-btn">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </label>
                            <input type="text" class="form-control text-secondary" id="brand" value=""
                                readonly>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 position-relative">
                            <label for="description" class="form-label fw-bold d-flex align-items-center">
                                Description
                                <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                    data-field="description" style="cursor: pointer;"></i>
                                <div class="edit-actions ms-2 d-none" data-field="description">
                                    <button type="button" class="btn btn-sm btn-success me-1 save-btn">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger cancel-btn">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </label>
                            <textarea class="form-control text-secondary" id="description" rows="3"
                                readonly></textarea>
                            <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1"
                                id="descriptionWordCount">0/255 characters</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="storageLocation" class="form-label fw-bold d-flex align-items-center">
                                Storage Location
                                <i class="bi bi-pencil text-secondary ms-2 edit-icon"
                                    data-field="storageLocation" style="cursor: pointer;"></i>
                                <div class="edit-actions ms-2 d-none" data-field="storageLocation">
                                    <button type="button" class="btn btn-sm btn-success me-1 save-btn">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger cancel-btn">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </label>
                            <input type="text" class="form-control text-secondary" id="storageLocation"
                                value="" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <select class="form-select" id="category" required>
                                <option value="">Select Category</option>
                                <!-- Categories will be populated dynamically -->
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="companyFee" class="form-label fw-bold">Rental Fee
                                (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="companyFee" min="0"
                                    step="0.01" required placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="availabilityStatus" class="form-label fw-bold">Availability
                                Status</label>
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
                            <input type="number" class="form-control" id="minRentalHours" min="1"
                                value="1" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Equipment</button>
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
        <!-- Inventory Item Modal -->
        <div class="modal fade" id="inventoryItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inventoryItemModalTitle">Add Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="itemForm">
                            <!-- Item Photo -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label fw-bold">Item Photo</label>
                                    <button type="button" class="btn btn-sm btn-danger d-none" id="removePhotoBtn">
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
                                <label for="itemName" class="form-label fw-bold required">Item Name</label>
                                <input type="text" class="form-control" id="itemName" placeholder="Enter item name"
                                    required>
                            </div>

                            <!-- Condition -->
                            <div class="mb-3">
                                <label for="itemCondition" class="form-label fw-bold required">Condition</label>
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
                                    <button class="btn btn-primary" type="button" id="generateBarcodeBtn">
                                        <i class="bi bi-upc-scan"></i> Generate
                                    </button>
                                    <!-- Download existing barcode button -->
                                    <button class="btn btn-success d-none" type="button"
                                        id="downloadExistingBarcodeBtn">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </div>

                                <!-- Barcode Preview -->
                                <div class="barcode-container d-none mt-3" id="barcodeContainer">
                                    <canvas id="barcodePreview" class="barcode-preview"></canvas>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-secondary"
                                            id="downloadBarcodeBtn">
                                            <i class="bi bi-download"></i> Download
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-3 position-relative">
                                <label for="itemNotes" class="form-label fw-bold">Item Notes</label>
                                <textarea class="form-control" id="itemNotes" rows="3"
                                    placeholder="Additional notes about this item"></textarea>
                                <small class="text-muted position-absolute bottom-0 end-0 me-2 mb-1"
                                    id="notesWordCount">0/80 words</small>
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
        <!-- Delete Item Confirmation Modal -->
        <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Item Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this item? The change will be applied when you click "Update
                        Equipment".
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteItemBtn">Delete Item</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const barcodeInput = document.getElementById('barcode');
            const generateBarcodeBtn = document.getElementById('generateBarcodeBtn');
            const barcodeContainer = document.getElementById('barcodeContainer');
            const barcodePreview = document.getElementById('barcodePreview'); // This is the canvas
            const downloadBarcodeBtn = document.getElementById('downloadBarcodeBtn');
            // 1. Global variables first
            window.equipmentItems = [];
            window.currentEditingItemId = null;
            window.pendingImageUploads = []; // Track images to be uploaded
            window.pendingImageDeletions = []; // Track images to be deleted
            window.pendingItemPhotoChanges = new Map(); // Track item photo changes (itemId -> {action, file, publicId})

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

                // Convert itemId to string for consistent comparison
                const itemIdStr = itemId.toString();

                // Store the item details for deletion
                window.pendingDeleteItem = {
                    itemId: itemIdStr,
                    publicId: cloudinaryPublicId
                };

                // Show the confirmation modal
                const deleteItemModal = new bootstrap.Modal('#deleteItemModal');
                deleteItemModal.show();
            };
            // 4. Open edit item modal function (needs to be defined before event handlers)
            window.openEditItemModal = function (itemId, event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                // Convert itemId to string for consistent comparison
                const itemIdStr = itemId.toString();
                const item = window.equipmentItems.find(i => i.item_id.toString() === itemIdStr);

                if (!item) {
                    console.error('Item not found:', itemId);
                    return;
                }

                // Fill the form with existing data
                document.getElementById('itemName').value = item.item_name;
                document.getElementById('itemCondition').value = item.condition_id;
                document.getElementById('barcode').value = item.barcode_number || '';
                document.getElementById('itemNotes').value = item.item_notes || '';

                document.getElementById('barcode').setAttribute('readonly', true);

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

                // Set the barcode field and handle download button
                if (item.barcode_number) {
                    barcodeInput.value = item.barcode_number;
                    barcodeInput.setAttribute('readonly', true);

                    // Show download button for existing barcode (now inside input group)
                    if (downloadExistingBarcodeBtn) {
                        downloadExistingBarcodeBtn.classList.remove('d-none');
                    }

                    // Hide generate button in edit mode when barcode exists
                    if (generateBarcodeBtn) {
                        generateBarcodeBtn.style.display = 'none';
                    }

                    // Hide barcode container (for newly generated barcodes)
                    barcodeContainer.classList.add('d-none');
                } else {
                    // No existing barcode
                    barcodeInput.value = '';
                    if (downloadExistingBarcodeBtn) {
                        downloadExistingBarcodeBtn.classList.add('d-none');
                    }
                }

                // Store the current editing item ID
                window.currentEditingItemId = itemId;

                // Change modal title and button text for editing
                document.getElementById('inventoryItemModalTitle').textContent = 'Edit Inventory Item';
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
                    // showToast('Uploading image to Cloudinary...', 'info', 3000);//

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

                    // REMOVED THE saveImageToDatabase CALL HERE - it will be called during form submission
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
                    throw error; // Re-throw to handle in the calling function
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
                        // Success! The image was deleted from Cloudinary
                        //  showToast('Image deleted from storage', 'success'); //
                        return result;
                    } else {
                        console.warn('Unexpected Cloudinary response format:', result);
                        // Still consider it a success since the API call didn't fail
                        //   showToast('Image cleanup completed', 'success'); //
                        return result;
                    }

                } catch (error) {
                    console.error('Error deleting image from Cloudinary:', error);
                    showToast('Failed to delete from storage: ' + error.message, 'error');
                    throw error;
                }
            }

            async function deleteImage(equipmentId, imageId, cloudinaryPublicId) {
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
                        //       showToast('Image deleted from storage', 'success'); //
                    }

                    // 2. Delete from your database
                    const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to delete image from database');
                    }

                    //     showToast('Image reference deleted', 'success'); //

                } catch (error) {
                    console.error('Error deleting image:', error);
                    showToast('Failed to delete image: ' + error.message, 'error');
                    throw error;
                }
            }

            // 12. Equipment file handling function (moved outside conditional block)
            async function handleEquipmentFiles(files) {
                const equipmentId = document.getElementById('equipmentId').value;

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

            // 13. Edit functionality for fields
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

            // 14. Cancel confirmation modal
            const cancelConfirmationModal = new bootstrap.Modal('#cancelConfirmationModal', {
                backdrop: 'static',
                keyboard: false
            });

            // 15. Cancel button handlers
            document.getElementById('cancelBtn').addEventListener('click', function (e) {
                e.preventDefault();
                cancelConfirmationModal.show();
            });

            document.getElementById('confirmCancelBtn').addEventListener('click', function () {
                // Hide the modal first
                cancelConfirmationModal.hide();

                // Reset all changes and reload data
                resetAllChanges();
                showToast('Changes discarded', 'info');
            });

            // Add this function to handle cancel operations
            function resetAllChanges() {
                // Clear all pending changes
                pendingImageUploads = [];
                pendingImageDeletions = [];
                pendingItemPhotoChanges.clear();

                // Reload equipment data to reset UI
                const equipmentId = document.getElementById('equipmentId').value;
                loadEquipmentData(equipmentId);

                // Clear any temporary UI elements
                const photosPreview = document.getElementById('photosPreview');
                if (photosPreview) {
                    photosPreview.innerHTML = '';
                }

                // Reset loading state - hide loading animation
                const itemsLoading = document.getElementById('itemsLoading');
                const itemsEmptyState = document.getElementById('itemsEmptyState');
                if (itemsLoading) itemsLoading.classList.add('d-none');
                if (itemsEmptyState) itemsEmptyState.classList.add('d-none');

                // Reload items
                loadEquipmentItems(equipmentId);
            }

            // 16. Equipment Photos Section
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

            // 17. Word count limiter for Description textbox
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

            // Delete item modal initialization
            const deleteItemModal = bootstrap.Modal.getOrCreateInstance('#deleteItemModal');

            // Handle confirm delete item button click
            document.getElementById('confirmDeleteItemBtn').addEventListener('click', async function () {
                if (!window.pendingDeleteItem) return;

                const { itemId, publicId } = window.pendingDeleteItem;

                try {
                    // Mark for deletion (will be processed on form submit)
                    pendingItemPhotoChanges.set(itemId, {
                        action: 'delete',
                        publicId: publicId
                    });

                    // Remove from UI immediately
                    document.querySelector(`.equipment-item[data-item-id="${itemId}"]`)?.remove();

                    // Hide the modal FIRST
                    deleteItemModal.hide();

                    // Clear the pending delete
                    window.pendingDeleteItem = null;

                    // Check if container is empty and show empty state
                    checkItemsContainerEmpty();

                    showToast('Item marked for deletion. Click "Update Equipment" to confirm.', 'info');

                } catch (error) {
                    console.error('Error staging item deletion:', error);
                    showToast('Failed to stage item deletion: ' + error.message, 'error');
                }
            });


// Helper function to check if items container is empty and show appropriate state
function checkItemsContainerEmpty() {
    const itemsContainer = document.getElementById('itemsContainer');
    const itemsEmptyState = document.getElementById('itemsEmptyState');
    
    if (!itemsContainer || !itemsEmptyState) return;

    const hasItems = itemsContainer.querySelector('.equipment-item');
    
    if (!hasItems) {
        itemsContainer.classList.add('d-none');
        itemsEmptyState.classList.remove('d-none');
    } else {
        itemsContainer.classList.remove('d-none');
        itemsEmptyState.classList.add('d-none');
    }
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
                    // Reset form and clear all fields
                    document.getElementById('itemForm').reset();
                    itemPhotoPreview.innerHTML = '';
                    itemPhotoFile = null;
                    itemCloudinaryPublicId = null;

                    // Reset UI elements
                    if (itemPhotoDropzone) itemPhotoDropzone.style.display = 'block';
                    if (removePhotoBtn) removePhotoBtn.classList.add('d-none');

                    // Reset barcode section
                    barcodeInput.value = '';
                    barcodeInput.removeAttribute('readonly');
                    barcodeContainer.classList.add('d-none');

                    // Show generate button and hide download button for new items
                    if (generateBarcodeBtn) {
                        generateBarcodeBtn.style.display = 'block';
                    }
                    if (downloadExistingBarcodeBtn) {
                        downloadExistingBarcodeBtn.classList.add('d-none');
                    }

                    // Reset modal title and button text for adding new item
                    document.getElementById('inventoryItemModalTitle').textContent = 'Add Inventory Item';
                    document.getElementById('saveItemBtn').textContent = 'Save Item';

                    // Clear any editing state
                    currentEditingItemId = null;

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
                    removePhotoBtn.addEventListener('click', function (e) {
                        e.preventDefault(); // Prevent any default behavior
                        e.stopPropagation(); // Stop event bubbling

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

                // Improved barcode generation for optimal Quagga scanning
                if (generateBarcodeBtn && barcodeInput) {
                    generateBarcodeBtn.addEventListener('click', async function () {
                        const generateBarcodeBtn = this;
                        const originalText = generateBarcodeBtn.innerHTML;

                        try {
                            generateBarcodeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generating...';
                            generateBarcodeBtn.disabled = true;

                            // Generate optimal barcode format for scanning
                            const randomPart = Math.random().toString(36).substring(2, 9).toUpperCase(); // 7 chars
                            const barcodeValue = `EQ-${randomPart}`;

                            barcodeInput.value = barcodeValue;

                            // Generate barcode with optimal settings for scanning
                            JsBarcode("#barcodePreview", barcodeValue, {
                                format: "CODE128",
                                width: 3, // Optimal width for scanning
                                height: 120, // Good height for reliability
                                displayValue: true,
                                fontSize: 16,
                                margin: 15, // Good margin
                                background: "#ffffff",
                                lineColor: "#000000", // Pure black for best contrast
                                valid: function (valid) {
                                    if (valid) {
                                        console.log('Barcode generated successfully:', barcodeValue);
                                    } else {
                                        console.warn('Barcode validation failed');
                                    }
                                }
                            });

                            const barcodeNumberDisplay = document.getElementById('barcodeNumberDisplay');
                            if (barcodeNumberDisplay) {
                                barcodeNumberDisplay.textContent = barcodeValue;
                                barcodeNumberDisplay.style.fontFamily = 'monospace'; // Better readability
                            }

                            barcodeContainer.classList.remove('d-none');
                            barcodeInput.setAttribute('readonly', true);

                            showToast('Barcode generated! Format: ' + barcodeValue, 'success');

                            // Test barcode scannability
                            setTimeout(() => {
                                testBarcodeScannability(barcodeValue);
                            }, 1000);

                        } catch (error) {
                            console.error('Barcode generation error:', error);
                            showToast('Failed to generate barcode', 'error');
                        } finally {
                            generateBarcodeBtn.innerHTML = originalText;
                            generateBarcodeBtn.disabled = false;
                        }
                    });
                }

                // Handle download of existing barcodes
                const downloadExistingBarcodeBtn = document.getElementById('downloadExistingBarcodeBtn');

                if (downloadExistingBarcodeBtn) {
                    downloadExistingBarcodeBtn.addEventListener('click', function () {
                        const barcodeValue = barcodeInput.value.trim();
                        if (!barcodeValue) {
                            showToast('No barcode available to download', 'error');
                            return;
                        }

                        try {
                            // Create a temporary canvas for barcode generation
                            const tempCanvas = document.createElement('canvas');

                            // Generate the barcode on the temporary canvas
                            JsBarcode(tempCanvas, barcodeValue, {
                                format: "CODE128",
                                width: 3,
                                height: 120,
                                displayValue: true,
                                fontSize: 16,
                                margin: 15,
                                background: "#ffffff",
                                lineColor: "#000000"
                            });

                            // Create download link
                            const link = document.createElement('a');
                            link.download = `barcode-${barcodeValue}.png`;
                            link.href = tempCanvas.toDataURL('image/png');
                            link.click();

                            showToast('Existing barcode downloaded successfully!', 'success');

                        } catch (error) {
                            console.error('Existing barcode download error:', error);
                            showToast('Failed to download existing barcode', 'error');
                        }
                    });
                }

                // Test barcode format for scannability
                function testBarcodeScannability(barcodeValue) {
                    const tests = [
                        { test: () => barcodeValue.length >= 8, message: 'Barcode should be at least 8 characters' },
                        { test: () => barcodeValue.startsWith('EQ-'), message: 'Barcode should start with EQ-' },
                        { test: () => /^EQ-[A-Z0-9]+$/.test(barcodeValue), message: 'Barcode should contain only letters and numbers after EQ-' },
                        { test: () => barcodeValue.length <= 20, message: 'Barcode should not be too long' }
                    ];

                    const failedTests = tests.filter(test => !test.test());

                    if (failedTests.length > 0) {
                        console.warn('Barcode scannability issues:', failedTests.map(t => t.message));
                        return false;
                    }

                    console.log('Barcode format is scannable:', barcodeValue);
                    return true;
                }

                // Handle barcode download with better quality
                if (downloadBarcodeBtn) {
                    downloadBarcodeBtn.addEventListener('click', function () {
                        const barcodeValue = barcodeInput.value.trim();
                        if (!barcodeValue) {
                            showToast('Please generate a barcode first', 'error');
                            return;
                        }

                        try {
                            // Get the canvas element
                            const canvas = document.getElementById('barcodePreview');
                            if (!canvas) {
                                showToast('Barcode preview not found', 'error');
                                return;
                            }

                            // Create a higher quality canvas for download
                            const downloadCanvas = document.createElement('canvas');
                            const downloadCtx = downloadCanvas.getContext('2d');

                            // Double the resolution for better print quality
                            downloadCanvas.width = canvas.width * 2;
                            downloadCanvas.height = canvas.height * 2;
                            downloadCtx.scale(2, 2);

                            // Draw the original barcode
                            downloadCtx.drawImage(canvas, 0, 0);

                            // Create download link
                            const link = document.createElement('a');
                            link.download = `barcode-${barcodeValue}.png`;
                            link.href = downloadCanvas.toDataURL('image/png');
                            link.click();

                            showToast('Barcode downloaded! Save as PNG for best scanning results.', 'success');

                        } catch (error) {
                            console.error('Barcode download error:', error);
                            showToast('Failed to download barcode', 'error');
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

                        try {
                            // Create a temporary item object
                            const tempItem = {
                                item_id: currentEditingItemId || 'temp-' + Date.now(),
                                item_name: itemName,
                                condition_id: itemCondition,
                                condition: { condition_name: document.getElementById('itemCondition').options[document.getElementById('itemCondition').selectedIndex].text },
                                barcode_number: barcode,
                                item_notes: notes,
                                image_url: 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp',
                                cloudinary_public_id: 'oxvsxogzu9koqhctnf7s'
                            };

                            // Handle photo preview
                            if (itemPhotoFile) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    tempItem.image_url = e.target.result;
                                    // Update UI immediately with the new image
                                    if (currentEditingItemId) {
                                        updateItemInUI(tempItem);
                                    } else {
                                        addItemToUI(tempItem);
                                    }
                                };
                                reader.readAsDataURL(itemPhotoFile);

                                // Store for form submission
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
                            } else {
                                // No new photo - check if we're updating or creating
                                if (currentEditingItemId) {
                                    // Update existing item - keep existing photo
                                    const existingItem = equipmentItems.find(i => i.item_id == currentEditingItemId);
                                    if (existingItem) {
                                        tempItem.image_url = existingItem.image_url;
                                        tempItem.cloudinary_public_id = existingItem.cloudinary_public_id;
                                    }

                                    // Update UI immediately
                                    updateItemInUI(tempItem);

                                    // Store for form submission
                                    pendingItemPhotoChanges.set(currentEditingItemId, {
                                        action: 'update',
                                        itemData: {
                                            item_name: itemName,
                                            condition_id: itemCondition,
                                            barcode_number: barcode,
                                            item_notes: notes
                                        }
                                    });
                                } else {
                                    // Create new item without photo
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
                            }

                            // Show success message and close modal
                            if (currentEditingItemId) {
                                showToast('Item updated successfully!', 'success');
                            } else {
                                showToast('Item added successfully!', 'success');
                            }

                            inventoryItemModal.hide();
                            currentEditingItemId = null;

                            // Reset modal for next use
                            document.getElementById('inventoryItemModalTitle').textContent = 'Add Inventory Item';
                            document.getElementById('saveItemBtn').textContent = 'Save Item';

                        } catch (error) {
                            console.error('Error saving item:', error);
                            showToast('Failed to save item: ' + error.message, 'error');
                        }
                    });
                }

                // Handle confirm delete button click
                document.getElementById('confirmDeleteImageBtn').addEventListener('click', async function () {
                    try {
                        const equipmentId = document.getElementById('equipmentId').value;

                        // Delete from Cloudinary first if public ID exists
                        if (currentDeletePublicId) {
                            await deleteImageFromCloudinary(currentDeletePublicId);
                            //   showToast('Image deleted from storage successfully', 'success'); //
                        }

                        // Then delete from database if it's a saved image (has photoId)
                        if (currentDeletePhotoId && typeof currentDeletePhotoId === 'number') {
                            await deleteImage(equipmentId, currentDeletePhotoId);
                            //      showToast('Image reference removed from database', 'success'); //
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

            // 19. UI update functions
            function updateItemInUI(updatedItem) {
                console.log('Updating item in UI:', updatedItem.item_id);

                // Find the existing item element
                const itemElement = document.querySelector(`.equipment-item[data-item-id="${updatedItem.item_id}"]`);
                if (!itemElement) {
                    console.error('Item element not found for update:', updatedItem.item_id);
                    return;
                }

                const conditionColors = {
                    "New": "bg-success text-white",
                    "Good": "bg-primary text-white",
                    "Fair": "bg-warning text-dark",
                    "Needs Maintenance": "bg-danger text-white",
                    "Damaged": "bg-dark text-white"
                };

                // Update the existing element's content
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
                                                        <button type="button" class="btn btn-sm btn-primary me-1" onclick="openEditItemModal(${updatedItem.item_id}, event)">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem(${updatedItem.item_id}, '${updatedItem.cloudinary_public_id}', event)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            `;

                // Update the item in the equipmentItems array
                const itemIndex = equipmentItems.findIndex(i => i.item_id == updatedItem.item_id);
                if (itemIndex !== -1) {
                    equipmentItems[itemIndex] = { ...equipmentItems[itemIndex], ...updatedItem };
                    console.log('Updated item in array:', equipmentItems[itemIndex]);
                }
            }

         function addItemToUI(item) {
    const itemsContainer = document.getElementById('itemsContainer');
    const itemsEmptyState = document.getElementById('itemsEmptyState');
    
    if (!itemsContainer || !itemsEmptyState) return;

    // Debug: Check if item already exists
    const existingElement = document.querySelector(`.equipment-item[data-item-id="${item.item_id}"]`);
    if (existingElement) {
        console.log('Item already exists in UI, updating instead:', item.item_id);
        updateItemInUI(item);
        return;
    }

    // If this is the first item being added, hide empty state and show container
    if (itemsEmptyState && !itemsEmptyState.classList.contains('d-none')) {
        itemsEmptyState.classList.add('d-none');
        itemsContainer.classList.remove('d-none');
    }

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
                <button type="button" class="btn btn-sm btn-primary me-1" onclick="openEditItemModal(${item.item_id}, event)">
                    <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem(${item.item_id}, '${item.cloudinary_public_id}', event)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;

    itemsContainer.appendChild(itemCard);

    // Add to equipmentItems array if it's a new item
    if (!equipmentItems.find(i => i.item_id == item.item_id)) {
        equipmentItems.push(item);
    }
}

            // 20. Function to upload item photo to Cloudinary
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

          // 21. Load equipment data
async function loadEquipmentData(equipmentId) {
    try {
        // Show loading state for items immediately
        const itemsLoading = document.getElementById('itemsLoading');
        const itemsContainer = document.getElementById('itemsContainer');
        const itemsEmptyState = document.getElementById('itemsEmptyState');
        
        if (itemsLoading && itemsContainer && itemsEmptyState) {
            itemsLoading.classList.remove('d-none');
            itemsContainer.classList.add('d-none');
            itemsEmptyState.classList.add('d-none');
        }

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

        document.getElementById('companyFee').value = equipment.external_fee;
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
                preview.dataset.imageId = image.image_id;
                preview.dataset.publicId = image.cloudinary_public_id;

                const img = document.createElement('img');
                img.src = image.image_url;
                img.className = 'img-thumbnail h-100 w-100 object-fit-cover';

                const removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                removeBtn.onclick = function () {
                    // Mark for deletion (will be processed on form submit)
                    pendingImageDeletions.push({
                        imageId: image.image_id,
                        publicId: image.cloudinary_public_id,
                        previewElement: preview
                    });
                    preview.remove();
                };

                preview.appendChild(img);
                preview.appendChild(removeBtn);
                photosPreview.appendChild(preview);
            });
        }

        // Fetch dropdown data
        await fetchDropdownData(equipment);

        // Load equipment items
        await loadEquipmentItems(equipmentId);

    } catch (error) {
        console.error('Error loading equipment data:', error);
        alert('Failed to load equipment data: ' + error.message);
        
        // Hide loading animation on error
        const itemsLoading = document.getElementById('itemsLoading');
        const itemsEmptyState = document.getElementById('itemsEmptyState');
        if (itemsLoading) itemsLoading.classList.add('d-none');
        if (itemsEmptyState) {
            itemsEmptyState.classList.remove('d-none');
            itemsEmptyState.innerHTML = `
                <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                <p class="mt-2 text-muted">Failed to load equipment data</p>
                <p class="text-muted small">${error.message}</p>
            `;
        }
    }
}

          // 22. Load equipment items
async function loadEquipmentItems(equipmentId) {
    try {
        const itemsContainer = document.getElementById('itemsContainer');
        const itemsLoading = document.getElementById('itemsLoading');
        const itemsEmptyState = document.getElementById('itemsEmptyState');
        
        if (!itemsContainer || !itemsLoading || !itemsEmptyState) {
            console.error('Required elements not found');
            return;
        }

        // Show loading, hide container and empty state
        itemsLoading.classList.remove('d-none');
        itemsContainer.classList.add('d-none');
        itemsEmptyState.classList.add('d-none');
        
        // Clear any existing items
        itemsContainer.innerHTML = '';

        const token = localStorage.getItem('adminToken');
        const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/items`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch equipment items: ${response.status}`);
        }

        const data = await response.json();
        equipmentItems = data.data || [];

        // Hide loading animation
        itemsLoading.classList.add('d-none');

        if (equipmentItems.length === 0) {
            // Show empty state
            itemsEmptyState.classList.remove('d-none');
            itemsContainer.classList.add('d-none');
        } else {
            // Show container and populate items
            itemsContainer.classList.remove('d-none');
            itemsEmptyState.classList.add('d-none');
            
            equipmentItems.forEach(item => {
                addItemToUI(item);
            });
        }

    } catch (error) {
        console.error('Error loading equipment items:', error);
        
        // Hide loading animation on error
        const itemsLoading = document.getElementById('itemsLoading');
        const itemsEmptyState = document.getElementById('itemsEmptyState');
        const itemsContainer = document.getElementById('itemsContainer');
        
        if (itemsLoading) itemsLoading.classList.add('d-none');
        if (itemsContainer) itemsContainer.classList.add('d-none');
        if (itemsEmptyState) {
            itemsEmptyState.classList.remove('d-none');
            itemsEmptyState.innerHTML = `
                <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                <p class="mt-2 text-muted">Failed to load items</p>
                <p class="text-muted small">${error.message}</p>
            `;
        }
        
        showToast('Failed to load equipment items: ' + error.message, 'error');
    }
}

            // 23. Add 'required' class to labels with required fields
            document.querySelectorAll('label[for]').forEach(label => {
                const input = document.getElementById(label.getAttribute('for'));
                if (input && input.hasAttribute('required')) {
                    label.classList.add('required');
                }
            });

            // 24. Load equipment data
            loadEquipmentData(equipmentId);

            // 25. Fetch dropdown data function
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

            // 26. Populate dropdown function
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

            // 27. Form submission handler
            document.getElementById('editEquipmentForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const equipmentId = document.getElementById('equipmentId').value;
                const token = localStorage.getItem('adminToken');

                try {
                    showToast('Processing changes...', 'success');

                    // 1. Process equipment image deletions
                    for (const deletion of pendingImageDeletions) {
                        try {
                            if (deletion.publicId && deletion.publicId !== 'oxvsxogzu9koqhctnf7s') {
                                await deleteImageFromCloudinary(deletion.publicId);
                            }
                            await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/images/${deletion.imageId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Authorization': `Bearer ${token}`,
                                    'Accept': 'application/json'
                                }
                            });
                        } catch (error) {
                            console.error('Error deleting equipment image:', error);
                            showToast('Warning: Failed to delete some images', 'warning');
                        }
                    }

                    // 2. Process equipment image uploads
                    for (const upload of pendingImageUploads) {
                        try {
                            const cloudinaryData = await uploadToCloudinary(upload.file, equipmentId);
                            await saveImageToDatabase(equipmentId, cloudinaryData.secure_url, cloudinaryData.public_id);
                        } catch (error) {
                            console.error('Error uploading equipment image:', error);
                            showToast('Warning: Failed to upload some images', 'warning');
                        }
                    }

                    // 3. Process item changes
                    for (const [itemId, change] of pendingItemPhotoChanges.entries()) {
                        try {
                            if (change.action === 'delete') {
                                // Delete item
                                if (change.publicId && change.publicId !== 'oxvsxogzu9koqhctnf7s') {
                                    await deleteImageFromCloudinary(change.publicId);
                                }
                                await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/items/${itemId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Authorization': `Bearer ${token}`,
                                        'Accept': 'application/json'
                                    }
                                });
                            } else if (change.action === 'create') {
                                // Create new item
                                let imageUrl = 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp';
                                let publicId = 'oxvsxogzu9koqhctnf7s';

                                if (change.file) {
                                    const cloudinaryData = await uploadItemToCloudinary(change.file, equipmentId);
                                    imageUrl = cloudinaryData.secure_url;
                                    publicId = cloudinaryData.public_id;
                                }

                                const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/items`, {
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
                            } else if (change.action === 'update') {
                                // Update existing item
                                let imageUrl = null;
                                let publicId = null;

                                if (change.file) {
                                    const cloudinaryData = await uploadItemToCloudinary(change.file, equipmentId);
                                    imageUrl = cloudinaryData.secure_url;
                                    publicId = cloudinaryData.public_id;
                                }

                                const updateData = { ...change.itemData };
                                if (imageUrl) updateData.image_url = imageUrl;
                                if (publicId) updateData.cloudinary_public_id = publicId;

                                const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/items/${itemId}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Authorization': `Bearer ${token}`,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(updateData)
                                });

                                if (!response.ok) throw new Error('Failed to update item');
                            }
                        } catch (error) {
                            console.error('Error processing item change:', error);
                            showToast('Warning: Failed to process some item changes', 'warning');
                        }
                    }

                    // 4. Update equipment details
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
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Failed to update equipment');
                    }

                    // Clear pending changes
                    pendingImageUploads = [];
                    pendingImageDeletions = [];
                    pendingItemPhotoChanges.clear();

                    showToast('Equipment updated successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '/admin/manage-equipment';
                    }, 1500);

                } catch (error) {
                    console.error('Error updating equipment:', error);
                    showToast('Failed to update equipment: ' + error.message, 'error');
                }
            });
        });
    </script>
@endsection