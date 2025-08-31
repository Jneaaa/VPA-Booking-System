@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
  <style>
    /* Add sharp edges to all elements */
    * {
      border-radius: 0 !important;
    }
    
    /* Loading indicator styles */
    #loadingIndicator {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
  </style>
  
  <!-- Loading Indicator -->
  <div id="loadingIndicator">
      <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
      </div>
  </div>

<div class="container-fluid px-4">
  <!-- Main Layout -->
  <div id="layout">

    <!-- Main Content -->
    <main id="main">

    <!-- Add New Facility Page -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Facility</h1>
        <a href="{{ route('admin.manage-facilities') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>Back to Facilities
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="addFacilityForm" enctype="multipart/form-data">
                @csrf
                <!-- Facility Photos and Details Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-4 h-100">
                            <label class="form-label fw-bold">Facility Photos</label>
                            <div class="dropzone border p-4 text-center h-100" id="facilityPhotosDropzone" style="cursor: pointer;">
                                <i class="bi bi-images fs-1 text-muted"></i>
                                <p class="mt-2">Drag & drop facility photos here or click to browse</p>
                                <input type="file" id="facilityPhotos" name="images[]" class="d-none" multiple accept="image/*">
                            </div>
                            <div id="photosPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                            <small class="text-muted">Upload at least one photo of the facility (max 5 photos)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4 h-100">
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="facilityName" class="form-label">Facility Name *</label>
                                    <input type="text" class="form-control" id="facilityName" name="facility_name" placeholder="Enter facility name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="buildingCode" class="form-label">Building Code</label>
                                    <select class="form-select" id="buildingCode" name="building_code">
                                        <option value="">Select Building Code</option>
                                        <option value="MT">MT</option>
                                        <option value="LEB">LEB</option>
                                        <option value="LHB">LHB</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3 position-relative">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Provide a detailed description of the facility" required></textarea>
                                    <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1" id="descriptionWordCount">0/255 characters</small>
                                </div>
                            </div>
                            <!-- Category & Subcategory Section -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="subcategoryContainer" style="display: none;">
                                    <label for="subcategory_id" class="form-label">Subcategory</label>
                                    <select class="form-select" id="subcategory_id" name="subcategory_id">
                                        <option value="">Select Subcategory</option>
                                        @foreach($subcategories as $subcategory)
                                            <option value="{{ $subcategory->subcategory_id }}">{{ $subcategory->subcategory_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Room Details Section -->
                            <div class="row mb-4" id="roomDetailsContainer" style="display: none;">
                                <div class="col-md-4 mb-3">
                                    <label for="roomName" class="form-label">Room Name</label>
                                    <input type="text" class="form-control" id="roomName" name="room_name">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="roomNumber" class="form-label">Room Number</label>
                                    <input type="text" class="form-control" id="roomNumber" name="room_code">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="floorLevel" class="form-label">Floor Level</label>
                                    <input type="number" class="form-control" id="floorLevel" name="floor_level" min="1">
                                </div>
                            </div>
                            <!-- Location & Capacity Section -->
                            <div class="row mb-4">
                                <div class="col-md-8 mb-3 position-relative">
                                    <label for="locationNote" class="form-label">Location Note *</label>
                                    <textarea class="form-control" id="locationNote" name="location_note" rows="2" maxlength="80" placeholder="Brief description of where the facility is located." required></textarea>
                                    <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1" id="locationNoteWordCount">0/80 characters</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="capacity" class="form-label">Capacity (max people) *</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity" min="1" placeholder="Maximum capacity" required>
                                </div>
                            </div>
                            <!-- Department & Flags Section -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="department_id" class="form-label">Owning Department *</label>
                                    <select class="form-select" id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="status_id" class="form-label">Availability Status *</label>
                                    <select class="form-select" id="status_id" name="status_id" required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->status_id }}">{{ $status->status_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="location_type" class="form-label">Indoors/Outdoors *</label>
                                    <select class="form-select" id="location_type" name="location_type" required>
                                        <option value="Indoors">Indoors</option>
                                        <option value="Outdoors">Outdoors</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Fees Section -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="internal_fee" class="form-label">Internal Fee (₱) *</label>
                                    <input type="number" class="form-control" id="internal_fee" name="internal_fee" min="0" step="0.01" value="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="external_fee" class="form-label">External Fee (₱) *</label>
                                    <input type="number" class="form-control" id="external_fee" name="external_fee" min="0" step="0.01" value="0" required>
                                </div>
                            </div>
                            <!-- Rate Type -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="rate_type" class="form-label">Rate Type *</label>
                                    <select class="form-select" id="rate_type" name="rate_type" required>
                                        <option value="Per Hour">Per Hour</option>
                                        <option value="Per Event">Per Event</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn btn-secondary">Reset</button>

                    <form action="{{ route('admin.facilities.store') }}" method="POST">
                        @csrf
                        <!-- form fields here -->
                        <button type="submit" class="btn btn-primary">Add Facility</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const categorySelect = document.getElementById('category_id');
    const subcategoryContainer = document.getElementById('subcategoryContainer');
    const subcategorySelect = document.getElementById('subcategory_id');
    const roomDetailsContainer = document.getElementById('roomDetailsContainer');
    const dropzone = document.getElementById('facilityPhotosDropzone');
    const fileInput = document.getElementById('facilityPhotos');
    const photosPreview = document.getElementById('photosPreview');
    const descriptionField = document.getElementById('description');
    const wordCountDisplay = document.getElementById('descriptionWordCount');
    const locationNoteField = document.getElementById('locationNote');
    const locationNoteWordCountDisplay = document.getElementById('locationNoteWordCount');
    const addFacilityForm = document.getElementById('addFacilityForm');
    const loadingIndicator = document.getElementById('loadingIndicator');

    // Subcategory options (you might want to load these dynamically from your server)
    const subcategoryMap = {
        @foreach($categories as $category)
            '{{ $category->category_id }}': [
                @foreach($category->subcategories as $subcategory)
                    { id: {{ $subcategory->subcategory_id }}, name: '{{ $subcategory->subcategory_name }}' },
                @endforeach
            ],
        @endforeach
    };

    // Category change handler
    categorySelect.addEventListener('change', function() {
        const selectedCategoryId = this.value;
        
        // Reset subcategory
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        subcategoryContainer.style.display = 'none';
        roomDetailsContainer.style.display = 'none';
        
        // If category has subcategories, show the dropdown
        if (subcategoryMap[selectedCategoryId] && subcategoryMap[selectedCategoryId].length > 0) {
            subcategoryContainer.style.display = 'block';
            subcategoryMap[selectedCategoryId].forEach(function(subcat) {
                const option = document.createElement('option');
                option.value = subcat.id;
                option.textContent = subcat.name;
                subcategorySelect.appendChild(option);
            });
        }
        
        // Show room details for Rooms category (you might need to adjust this logic)
        const selectedCategoryText = categorySelect.options[categorySelect.selectedIndex].text;
        if (selectedCategoryText.toLowerCase().includes('room')) {
            roomDetailsContainer.style.display = 'flex';
        }
    });
    
    // Photo upload handling
    dropzone.addEventListener('click', function() {
        fileInput.click();
    });
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-primary');
    });
    
    dropzone.addEventListener('dragleave', function() {
        this.classList.remove('border-primary');
    });
    
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary');
        if (e.dataTransfer.files.length) {
            handleFiles(e.dataTransfer.files);
        }
    });
    
    function handleFiles(files) {
        // Clear previous previews
        photosPreview.innerHTML = '';
        
        // Create new DataTransfer to store files
        const dataTransfer = new DataTransfer();
        
        // Keep only the first 5 files
        const filesToKeep = Array.from(files).slice(0, 5);
        
        filesToKeep.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            
            // Add to DataTransfer
            dataTransfer.items.add(file);
            
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'position-relative';
                preview.style.width = '100px';
                preview.style.height = '100px';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail h-100 w-100 object-fit-cover';
                
                const removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                removeBtn.onclick = function() {
                    preview.remove();
                    // Remove file from input
                    const newFiles = Array.from(fileInput.files).filter(f => f !== file);
                    const newDT = new DataTransfer();
                    newFiles.forEach(f => newDT.items.add(f));
                    fileInput.files = newDT.files;
                };
                
                preview.appendChild(img);
                preview.appendChild(removeBtn);
                photosPreview.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
        
        // Update file input
        fileInput.files = dataTransfer.files;
    }
    
    // Word counter for description
    descriptionField.addEventListener('input', function() {
        const charCount = this.value.length;
        wordCountDisplay.textContent = `${charCount}/255 characters`;
    });

    // Word counter for location note
    locationNoteField.addEventListener('input', function() {
        const charCount = this.value.length;
        locationNoteWordCountDisplay.textContent = `${charCount}/80 characters`;
    });

    // Form submission
    addFacilityForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Show loading indicator
        loadingIndicator.style.display = 'flex';
        
        try {
            // Create FormData object from the form
            const formData = new FormData(this);
            
            console.log('Submitting form data...');
            
            // Submit to server
            const response = await fetch('{{ route("admin.facilities.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            });
            
            console.log('Response status:', response.status);
            
            // Handle response
            if (response.redirected) {
                // Laravel redirected us (success)
                window.location.href = response.url;
            } else {
                // Try to parse as JSON
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                try {
                    const data = JSON.parse(responseText);
                    if (response.ok) {
                        alert('Facility created successfully!');
                        window.location.href = '{{ route("admin.manage-facilities") }}';
                    } else {
                        throw new Error(data.message || 'Server error');
                    }
                } catch (e) {
                    // If it's not JSON, it's probably an HTML error page
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Server returned an error page. Check console for details.');
                }
            }
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + (error.message || 'An error occurred. Please try again.'));
        } finally {
            loadingIndicator.style.display = 'none';
        }
    });
    
    function validateForm() {
        const facilityName = document.getElementById('facilityName').value.trim();
        const category = document.getElementById('category_id').value;
        const department = document.getElementById('department_id').value;
        const description = document.getElementById('description').value.trim();
        const locationNote = document.getElementById('locationNote').value.trim();
        const capacity = document.getElementById('capacity').value;
        const internalFee = document.getElementById('internal_fee').value;
        const externalFee = document.getElementById('external_fee').value;
        
        if (!facilityName) {
            alert('Facility name is required');
            return false;
        }
        
        if (!category) {
            alert('Category is required');
            return false;
        }
        
        if (!department) {
            alert('Owning department is required');
            return false;
        }
        
        if (!description) {
            alert('Description is required');
            return false;
        }
        
        if (!locationNote) {
            alert('Location note is required');
            return false;
        }
        
        if (!capacity) {
            alert('Capacity is required');
            return false;
        }
        
        if (!internalFee) {
            alert('Internal fee is required');
            return false;
        }
        
        if (!externalFee) {
            alert('External fee is required');
            return false;
        }
        
        if (fileInput.files.length === 0) {
            alert('At least one photo is required');
            return false;
        }
        
        return true;
    }
});
</script>
@endsection