@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
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

  .equipment-item .photo-container {
    flex-shrink: 0;
    /* Prevent photo container from stretching */
  }

  .equipment-item .flex-grow-1 {
    flex: 1;
    /* Allow details section to take remaining space */
  }

  .form-label.required::after {
    content: " *";
    color: red;
  }
</style>
<div class="container-fluid px-4">
  <!-- Main Layout -->
  <div id="layout">
    <!-- Main Content -->
    <main id="main">
      <!-- Add New Equipment Page -->

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Equipment</h1>
        <a href="{{ url('/admin/manage-equipment') }}" class="btn btn-primary">
          <i class="bi bi-arrow-left me-2"></i>Back to Equipment
        </a>
      </div>

      <div class="card shadow mb-4">
        <div class="card-body">
          <form id="addEquipmentForm">
            <!-- Equipment Photos and Inventory Items Section -->
            <div class="row mb-4">
              <!-- Equipment Photos Card -->
              <div class="col-md-6">
                <div class="card h-100">
                  <div class="card-header d-flex justify-content-between align-items-center" style="height: 56px;">
                    <h5 class="fw-bold mb-0">Equipment Photos</h5>
                  </div>
                  <div class="card-body">
                    <div class="photo-section">
                      <div class="dropzone border p-4 text-center" id="equipmentPhotosDropzone"
                        style="cursor: pointer;">
                        <i class="bi bi-images fs-1 text-muted"></i>
                        <p class="mt-2">Drag & drop equipment photos here or click to browse</p>
                        <input type="file" id="equipmentPhotos" class="d-none" multiple accept="image/*">
                      </div>
                      <small class="text-muted mt-2 d-block">Upload at least one photo of the equipment (max 5
                        photos)</small>
                      <div id="photosPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
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
                  <div class="card-body">
                    <div id="itemsContainer">
                      <p class="text-muted">No items added yet. Click "Add Item" to track individual equipment pieces.
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
                          <label for="equipmentName" class="form-label">Equipment Name</label>
                          <input type="text" class="form-control" id="equipmentName"
                            placeholder="(e.g., HD Projector, Wireless Microphone)" required>
                        </div>
                        <div class="col-md-6">
                          <label for="brand" class="form-label">Brand</label>
                          <input type="text" class="form-control" id="brand" placeholder="(e.g., Sony, JBL)">
                        </div>
                      </div>
                      <div class="row mb-4">
                        <div class="col-12 position-relative">
                          <label for="description" class="form-label">Description</label>
                          <textarea class="form-control" id="description" rows="3"
                            placeholder="Provide a detailed description of the equipment including specifications and features"></textarea>
                          <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1"
                            id="descriptionWordCount">0/255 characters</small>
                        </div>
                      </div>
                      <!-- Location & Category Section -->
                      <div class="row mb-4">
                        <div class="col-md-6">
                          <label for="storageLocation" class="form-label">Storage Location</label>
                          <input type="text" class="form-control" id="storageLocation"
                            placeholder="(e.g., MT Building Room 201)" required>
                        </div>
                        <div class="col-md-6">
                          <label for="category" class="form-label">Category</label>
                          <select class="form-select" id="category" required>
                            <option value="">Select Category</option>
                            <option value="Audio Equipment">Audio Equipment</option>
                            <option value="Visual Equipment">Visual Equipment</option>
                            <option value="Lighting Equipment">Lighting Equipment</option>
                            <option value="Conference Equipment">Conference Equipment</option>
                          </select>
                        </div>
                      </div>
                      <!-- Quantity & Pricing Section -->
                      <div class="row mb-4">
                        <div class="col-md-3">
                          <label for="totalQuantity" class="form-label">Total Quantity</label>
                          <input type="number" class="form-control" id="totalQuantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-3">
                          <label for="rentalFee" class="form-label">Rental Fee (₱)</label>
                          <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="rentalFee" min="0" step="0.01" required
                              placeholder="0.00">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="companyFee" class="form-label">Company Fee (₱)</label>
                          <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="companyFee" min="0" step="0.01"
                              placeholder="0.00">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="rateType" class="form-label">Rate Type</label>
                          <select class="form-select" id="rateType" required>
                            <option value="Hour">Per Hour</option>
                            <option value="Event">Per Event</option>
                          </select>
                        </div>
                      </div>
                      <!-- Department & Availability Section -->
                      <div class="row mb-4">
                        <div class="col-md-4">
                          <label for="department" class="form-label">Owning Department</label>
                          <select class="form-select" id="department" required>
                            <option value="">Select Department</option>
                            <option value="CBA">CBA</option>
                            <option value="CAS">CAS</option>
                            <option value="CCS">CCS</option>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label for="minRentalHours" class="form-label">Minimum Rental Duration (hours)</label>
                          <input type="number" class="form-control" id="minRentalHours" min="1" value="1" required>
                        </div>
                        <div class="col-md-4">
                          <label for="availabilityStatus" class="form-label">Availability Status</label>
                          <select class="form-select" id="availabilityStatus" required>
                            <option value="Available" selected>Available</option>
                            <option value="Unavailable">Unavailable</option>
                            <option value="Under Maintenance">Under Maintenance</option>
                            <option value="Hidden">Hidden</option>
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
              <button type="reset" class="btn btn-secondary">Reset</button>
              <button type="submit" class="btn btn-primary">Save Equipment</button>
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
                <label class="form-label">Item Photo</label>
                <button class="btn btn-sm btn-danger d-none" id="removePhotoBtn">
                  <i class="bi bi-trash"></i> Remove
                </button>
              </div>
              <div class="dropzone border p-4 text-center" id="itemPhotoDropzone" style="cursor: pointer;">
                <i class="bi bi-image fs-1 text-muted"></i>
                <p class="mt-2">Click to upload item photo</p>
                <input type="file" id="itemPhoto" class="d-none" accept="image/*">
              </div>
              <div id="itemPhotoPreview" class="mt-3 text-center"></div>
            </div>

            <!-- Item Name -->
            <div class="mb-3">
              <label for="itemName" class="form-label">Item Name</label>
              <input type="text" class="form-control" id="itemName" placeholder="Enter item name">
            </div>

            <!-- Condition -->
            <div class="mb-3">
              <label for="itemCondition" class="form-label">Condition</label>
              <select class="form-select" id="itemCondition" required>
                <option value="">Select Condition</option>
                <option value="New">New</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Needs Maintenance">Needs Maintenance</option>
                <option value="Damaged">Damaged</option>
              </select>
            </div>

            <!-- Barcode -->
            <div class="mb-3">
              <label for="barcode" class="form-label">Barcode Number</label>
              <input type="text" class="form-control" id="barcode" placeholder="Scan or enter barcode">
            </div>

            <!-- Notes -->
            <div class="mb-3 position-relative">
              <label for="itemNotes" class="form-label">Item Notes</label>
              <textarea class="form-control" id="itemNotes" rows="3"
                placeholder="Additional notes about this item"></textarea>
              <small class="text-muted position-absolute bottom-0 end-0 me-2 mb-1" id="notesWordCount">0/80
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
      // Initialize the reset confirmation modal
      const resetConfirmationModal = new bootstrap.Modal('#resetConfirmationModal', {
      backdrop: 'static',
      keyboard: false
      });

      // Handle reset button click
      document.querySelector('button[type="reset"]').addEventListener('click', function (e) {
      e.preventDefault();
      resetConfirmationModal.show();
      });

      // Handle confirm reset button click
      document.getElementById('confirmResetBtn').addEventListener('click', function () {
      // Clear uploaded photos
      const photosPreview = document.getElementById('photosPreview');
      if (photosPreview) photosPreview.innerHTML = '';

      // Reset inventory items
      const itemsContainer = document.getElementById('itemsContainer');
      if (itemsContainer) itemsContainer.innerHTML = '<p class="text-muted">No items added yet. Click "Add Item" to track individual equipment pieces.</p>';

      // Reset the form
      const form = document.getElementById('addEquipmentForm');
      if (form) form.reset();

      // Scroll to the top smoothly
      window.scrollTo({ top: 0, behavior: 'smooth' });

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

      function handleEquipmentFiles(files) {
      // Filter out non-image files
      const imageFiles = Array.from(files).filter(file => file.type.startsWith('image/'));

      // Calculate remaining slots (max 5 photos total)
      const remainingSlots = Math.max(0, 5 - uploadedPhotos.length);
      if (remainingSlots <= 0) {
        alert('Maximum of 5 photos allowed');
        return;
      }

      // Take only the number of files that will fit within the limit
      const filesToUpload = imageFiles.slice(0, remainingSlots);

      filesToUpload.forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
        const photoId = Date.now();

        const preview = document.createElement('div');
        preview.className = 'photo-preview';
        preview.dataset.id = photoId;

        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'img-thumbnail h-100 w-100 object-fit-cover';

        const removeBtn = document.createElement('button');
        removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.onclick = function () {
          preview.remove();
          uploadedPhotos = uploadedPhotos.filter(photo => photo.id !== photoId);
        };

        preview.appendChild(img);
        preview.appendChild(removeBtn);
        if (photosPreview) photosPreview.appendChild(preview);

        uploadedPhotos.push({
          id: photoId,
          file: file,
          element: preview
        });
        };
        reader.readAsDataURL(file);
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
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Authentication check
      const token = localStorage.getItem('adminToken');
      if (!token) {
      window.location.href = '/admin/admin-login';
      return;
      }

      const form = document.getElementById('addEquipmentForm');
      if (!form) return;

      // Fetch necessary dropdown data
      fetchDropdownData();

      form.addEventListener('submit', async function (e) {
      e.preventDefault();

      // Collect form data
      const formData = {
        equipment_name: document.getElementById('equipmentName').value,
        description: document.getElementById('description').value,
        brand: document.getElementById('brand').value,
        storage_location: document.getElementById('storageLocation').value,
        category_id: document.getElementById('category').value,
        total_quantity: document.getElementById('totalQuantity').value,
        internal_fee: document.getElementById('internal_fee').value,
        external_fee: document.getElementById('external_fee').value,
        type_id: document.getElementById('rateType').value,
        status_id: document.getElementById('availabilityStatus').value,
        department_id: document.getElementById('department').value,
        minimum_hour: document.getElementById('minRentalHours').value,
        images: [], // Will be populated with uploaded images
        items: []  // Will be populated with inventory items
      };

      // Add inventory items if any
      const itemCards = document.querySelectorAll('.equipment-item');
      itemCards.forEach(card => {
        formData.items.push({
        item_name: card.querySelector('.card-title').textContent.replace('Item #', ''),
        condition_id: card.querySelector('.badge').textContent,
        barcode_number: card.querySelector('div:nth-child(3)')?.textContent.replace('Barcode:', '').trim() || '',
        item_notes: card.querySelector('p')?.textContent.replace('Notes:', '').trim() || ''
        });
      });

      try {
        // First create the equipment
        const response = await fetch('http://127.0.0.1:8000/api/admin/equipment', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
        });

        if (!response.ok) {
        throw new Error('Failed to create equipment');
        }

        const data = await response.json();
        const equipmentId = data.data.equipment_id;

        // Then upload images if any
        await uploadImages(equipmentId);

        alert('Equipment created successfully!');
        window.location.href = '{{ url('/admin/manage-equipment') }}';
      } catch (error) {
        console.error('Error creating equipment:', error);
        alert('Failed to create equipment: ' + error.message);
      }
      });

      async function fetchDropdownData() {
      try {
        // Fetch user role
        const userResponse = await fetch('http://127.0.0.1:8000/api/admin/user-role', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
        });

        if (!userResponse.ok) {
        throw new Error('Failed to fetch user role');
        }

        const userData = await userResponse.json();
        const userRole = userData.role;

        // Fetch departments
        const departmentsResponse = await fetch('http://127.0.0.1:8000/api/admin/departments', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
        });

        if (departmentsResponse.ok) {
        const departmentsData = await departmentsResponse.json();

        // Filter departments based on role
        let filteredDepartments = [];
        if (userRole === 'head admin') {
          filteredDepartments = departmentsData.data; // Head admins see all departments
        } else if (userRole === 'inventory manager') {
          filteredDepartments = departmentsData.data.filter(department =>
          department.assigned_to.includes(userData.id)
          ); // Inventory managers see only their assigned departments
        }

        populateDropdown('department', filteredDepartments);
        }

        // Fetch categories
        const categoriesResponse = await fetch('http://127.0.0.1:8000/api/admin/equipment-categories', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
        });

        if (categoriesResponse.ok) {
        const categoriesData = await categoriesResponse.json();
        populateDropdown('category', categoriesData.data);
        }

        // Fetch statuses
        const statusesResponse = await fetch('http://127.0.0.1:8000/api/admin/availability-statuses', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
        });

        if (statusesResponse.ok) {
        const statusesData = await statusesResponse.json();
        populateDropdown('availabilityStatus', statusesData.data);
        }

        // Fetch rate types
        const rateTypesResponse = await fetch('http://127.0.0.1:8000/api/admin/rate-types', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
        });

        if (rateTypesResponse.ok) {
        const rateTypesData = await rateTypesResponse.json();
        populateDropdown('rateType', rateTypesData.data);
        }

      } catch (error) {
        console.error('Error fetching dropdown data:', error);
      }
      }

      function populateDropdown(elementId, data) {
      const dropdown = document.getElementById(elementId);
      if (!dropdown) return;

      // Clear existing options except the first one
      while (dropdown.options.length > 1) {
        dropdown.remove(1);
      }

      // Add new options
      data.forEach(item => {
        const option = document.createElement('option');
        option.value = item[Object.keys(item)[0]]; // Get the ID (first key)
        option.textContent = item[Object.keys(item)[1]]; // Get the name (second key)
        dropdown.appendChild(option);
      });
      }

      async function uploadImages(equipmentId) {
      const photos = Array.from(document.querySelectorAll('.photo-preview'));
      if (photos.length === 0) return;

      for (const photo of photos) {
        const img = photo.querySelector('img');
        if (!img) continue;

        try {
        // Convert data URL to blob
        const blob = await fetch(img.src).then(r => r.blob());
        const formData = new FormData();
        formData.append('image', blob);
        formData.append('type_id', photo === photos[0] ? 1 : 2); // First image is primary

        const response = await fetch(`http://127.0.0.1:8000/api/admin/equipment/${equipmentId}/upload-image`, {
          method: 'POST',
          headers: {
          'Authorization': `Bearer ${token}`
          },
          body: formData
        });

        if (!response.ok) {
          console.error('Failed to upload image');
        }
        } catch (error) {
        console.error('Error uploading image:', error);
        }
      }
      }

      // Existing form handling code remains the same...
    });
    </script>
  @endsection