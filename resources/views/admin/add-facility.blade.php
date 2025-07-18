<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>

  <!-- Combined CSS resources -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/admin-styles.css">
  <style>
    /* Add sharp edges to all elements */
    * {
      border-radius: 0 !important;
    }
  </style>
</head>
<body>
  <!-- Top Bar -->
  <header id="topbar" class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <img src="../cpu-logo.png" alt="CPU Logo" class="me-2" style="height: 40px;">
      <span class="fw-bold">CPU Facilities and Equipment Management</span>
    </div>
    <div class="d-flex align-items-center">
      <!--Notifications-->
      <div class="position-relative me-3">
        <div class="dropdown">
          <i class="bi bi-bell fs-4 position-relative" id="notificationIcon" data-bs-toggle="dropdown" aria-expanded="false"></i>
          <span class="notification-badge">1</span>
          <ul class="dropdown-menu dropdown-menu-end p-0" id="notificationDropdown" aria-labelledby="notificationIcon">
            <li class="dropdown-header">Notifications</li>
            <li>
              <a href="#" class="notification-item unread d-block" data-notification-id="1">
                <div class="notification-title">New Facility Request</div>
                <div class="notification-text">John Smith requested the Main Auditorium for March 15, 2024</div>
                <div class="notification-time">2 minutes ago</div>
              </a>
            </li>
            <li>
              <a href="#" class="notification-item d-block">
                <div class="notification-title">Booking Approved</div>
                <div class="notification-text">Your equipment request for the sound system has been approved</div>
                <div class="notification-time">3 hours ago</div>
              </a>
            </li>
            <li><hr class="dropdown-divider m-0"></li>
            <li><a href="#" class="dropdown-item view-all-item text-center">View all notifications</a></li>
          </ul>
        </div>
      </div>
      <!-- Dropdown Menu -->
      <div class="dropdown">
        <button class="btn btn-link p-0 text-white" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-three-dots fs-4"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="adminlogin.html"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </header>
  <!-- Main Layout -->
  <div id="layout">
    <!-- Sidebar -->
    <nav id="sidebar">
      <div class="text-center mb-4">
        <div class="position-relative d-inline-block">
          <img src="assets/admin-pic.jpg" alt="Admin Profile" class="profile-img rounded-circle">
        </div>
        <h5 class="mt-3 mb-1">John Doe</h5>
        <p class="text-muted mb-0">Head Admin</p>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link active" href="#">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="calendar.html">
            <i class="bi bi-calendar-event me-2"></i>
            Calendar
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-file-earmark-text me-2"></i>
            Requisitions
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-building me-2"></i>
            Facilities
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-tools me-2"></i>
            Equipment
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="bi bi-people me-2"></i>
            Admin Roles
          </a>
        </li>
      </ul>
    </nav>

    <!-- Main Content -->
    <main id="main">

    <!-- Add New Facility Page -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Facility</h1>
        <a href="dashboard.html" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="addFacilityForm">
                <!-- Facility Photos and Details Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-4 h-100">
                            <label class="form-label fw-bold">Facility Photos</label>
                            <div class="dropzone border p-4 text-center h-100" id="facilityPhotosDropzone" style="cursor: pointer;">
                                <i class="bi bi-images fs-1 text-muted"></i>
                                <p class="mt-2">Drag & drop facility photos here or click to browse</p>
                                <input type="file" id="facilityPhotos" class="d-none" multiple accept="image/*">
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
                                    <input type="text" class="form-control" id="facilityName" placeholder="Enter facility name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="buildingCode" class="form-label">Building Code</label>
                                    <select class="form-select" id="buildingCode">
                                        <option value="">Select Building Code</option>
                                        <option value="MT">MT</option>
                                        <option value="LEB">LEB</option>
                                        <option value="LHB">LHB</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3 position-relative">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="description" rows="3" placeholder="Provide a detailed description of the facility" required></textarea>
                                    <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1" id="descriptionWordCount">0/255 characters</small>
                                </div>
                            </div>
                            <!-- Category & Subcategory Section -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Campus Buildings">Campus Buildings</option>
                                        <option value="Residential Buildings">Residential Buildings</option>
                                        <option value="Outside Spaces">Outside Spaces</option>
                                        <option value="Rooms">Rooms</option>
                                        <option value="Sports Venue">Sports Venue</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="subcategoryContainer" style="display: none;">
                                    <label for="subcategory" class="form-label">Subcategory *</label>
                                    <select class="form-select" id="subcategory">
                                        <option value="">Select Subcategory</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Room Details Section -->
                            <div class="row mb-4" id="roomDetailsContainer" style="display: none;">
                                <div class="col-md-4 mb-3">
                                    <label for="roomName" class="form-label">Room Name *</label>
                                    <input type="text" class="form-control" id="roomName">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="roomNumber" class="form-label">Room Number *</label>
                                    <input type="text" class="form-control" id="roomNumber">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="floorLevel" class="form-label">Floor Level *</label>
                                    <input type="number" class="form-control" id="floorLevel" min="1">
                                </div>
                            </div>
                            <!-- Location & Capacity Section -->
                            <div class="row mb-4">
                                <div class="col-md-8 mb-3 position-relative">
                                    <label for="locationNote" class="form-label">Location Note</label>
                                    <textarea class="form-control" id="locationNote" rows="2" maxlength="80" placeholder="Brief description of where the facility is located."></textarea>
                                    <small class="text-muted position-absolute bottom-0 end-0 me-4 mb-1" id="locationNoteWordCount">0/80 characters</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="capacity" class="form-label">Capacity (max people)</label>
                                    <input type="number" class="form-control" id="capacity" min="1" placeholder="Maximum capacity">
                                </div>
                            </div>
                            <!-- Department & Flags Section -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="owningDepartment" class="form-label">Owning Department *</label>
                                    <select class="form-select" id="owningDepartment" required>
                                        <option value="">Select Department</option>
                                        <option value="CS">CS</option>
                                        <option value="CBA">CBA</option>
                                        <option value="CAS">CAS</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="availabilityStatus" class="form-label">Availability Status *</label>
                                    <select class="form-select" id="availabilityStatus" required>
                                        <option value="Available" selected>Available</option>
                                        <option value="Unavailable">Unavailable</option>
                                        <option value="Hidden">Hidden</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="is_indoors" class="form-label">Indoors/Outdoors *</label>
                                    <select class="form-select" id="is_indoors" required>
                                        <option value="Indoors">Indoors</option>
                                        <option value="Outdoors">Outdoors</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amenities Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">Amenities</label>
                            <button type="button" class="btn btn-sm btn-secondary" id="addAmenityBtn">
                                <i class="bi bi-plus me-1"></i>Add Amenity
                            </button>
                        </div>
                        <div id="amenitiesContainer">
                            <p class="text-muted">No amenities added yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Equipment Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">Equipment</label>
                            <button type="button" class="btn btn-sm btn-primary" id="addEquipmentBtn" data-bs-toggle="modal" data-bs-target="#equipmentModal">
                                <i class="bi bi-plus me-1"></i>Add Equipment
                            </button>
                        </div>
                        <div id="equipmentContainer">
                            <p class="text-muted">No equipment added yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Facility</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Equipment Selection Modal -->
<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentModalLabel">Select Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search equipment..." id="equipmentSearch">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Equipment Name</th>
                                <th>Category</th>
                                <th>Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>Projector HD</td>
                                <td>AV Equipment</td>
                                <td>₱500.00</td>
                                <td><span class="badge bg-success">Available</span></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>Sound System</td>
                                <td>AV Equipment</td>
                                <td>₱1,200.00</td>
                                <td><span class="badge bg-success">Available</span></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>Microphone Set</td>
                                <td>AV Equipment</td>
                                <td>₱300.00</td>
                                <td><span class="badge bg-success">Available</span></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>Whiteboard</td>
                                <td>Classroom Equipment</td>
                                <td>₱150.00</td>
                                <td><span class="badge bg-success">Available</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmEquipmentSelection">Add Selected Equipment</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle category change to show/hide subcategory
        const categorySelect = document.getElementById('category');
        const subcategoryContainer = document.getElementById('subcategoryContainer');
        const subcategorySelect = document.getElementById('subcategory');
        const roomDetailsContainer = document.getElementById('roomDetailsContainer');
        
        // Subcategory options
        const subcategories = {
            'Campus Buildings': ['Hall', 'Building', 'Church'],
            'Rooms': ['Academic room', 'Conference room', 'Dorm room', 'Computer lab', 'Laboratory'],
            'Sports Venue': ['Swimming pool', 'Field', 'Court', 'Gym']
        };
        
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            
            // Reset subcategory
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            subcategoryContainer.style.display = 'none';
            roomDetailsContainer.style.display = 'none';
            
            // If category has subcategories, show the dropdown
            if (subcategories[selectedCategory]) {
                subcategoryContainer.style.display = 'block';
                subcategories[selectedCategory].forEach(function(subcat) {
                    const option = document.createElement('option');
                    option.value = subcat;
                    option.textContent = subcat;
                    subcategorySelect.appendChild(option);
                });
            }
        });
        
        // Handle subcategory change to show room details if needed
        subcategorySelect.addEventListener('change', function() {
            const selectedCategory = categorySelect.value;
            const selectedSubcategory = this.value;
            
            // Only show room details for Room subcategories
            if (selectedCategory === 'Rooms') {
                roomDetailsContainer.style.display = 'flex';
            } else {
                roomDetailsContainer.style.display = 'none';
            }
        });
        
        // Photo upload handling
        const dropzone = document.getElementById('facilityPhotosDropzone');
        const fileInput = document.getElementById('facilityPhotos');
        const photosPreview = document.getElementById('photosPreview');
        
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
            
            // Limit to 5 files
            const filesToUpload = Array.from(files).slice(0, 5);
            
            filesToUpload.forEach(file => {
                if (!file.type.startsWith('image/')) return;
                
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
                    };
                    
                    preview.appendChild(img);
                    preview.appendChild(removeBtn);
                    photosPreview.appendChild(preview);
                };
                reader.readAsDataURL(file);
            });
        }
        
        // Amenity management
        const amenitiesContainer = document.getElementById('amenitiesContainer');
        const addAmenityBtn = document.getElementById('addAmenityBtn');
        
        addAmenityBtn.addEventListener('click', function() {
            const amenityItem = document.createElement('div');
            amenityItem.className = 'amenity-item row g-2 mb-2';
            amenityItem.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Label (e.g., Wi-Fi, Projector)" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="Qty" min="1" value="1">
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" placeholder="Price" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100 remove-amenity">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            
            amenitiesContainer.appendChild(amenityItem);
            
            // Add event listener to remove button
            amenityItem.querySelector('.remove-amenity').addEventListener('click', function() {
                amenityItem.remove();
            });
        });
        
        // Equipment management
        const equipmentContainer = document.getElementById('equipmentContainer');
        const confirmEquipmentSelection = document.getElementById('confirmEquipmentSelection');
        
        confirmEquipmentSelection.addEventListener('click', function() {
            const selectedEquipment = Array.from(document.querySelectorAll('#equipmentModal .form-check-input:checked'))
                .map(checkbox => checkbox.closest('tr'));
                
            if (selectedEquipment.length === 0) {
                alert('Please select at least one equipment item');
                return;
            }
            
            // Clear previous equipment display if it's the default message
            if (equipmentContainer.querySelector('p.text-muted')) {
                equipmentContainer.innerHTML = '';
            }
            
            selectedEquipment.forEach(row => {
                const cells = row.querySelectorAll('td');
                const name = cells[1].textContent;
                const fee = cells[3].textContent;
                
                const equipmentItem = document.createElement('div');
                equipmentItem.className = 'equipment-item card mb-2';
                equipmentItem.innerHTML = `
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">${name}</h6>
                                <small class="text-muted">${fee}</small>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-equipment">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                equipmentContainer.appendChild(equipmentItem);
                
                // Add event listener to remove button
                equipmentItem.querySelector('.remove-equipment').addEventListener('click', function() {
                    equipmentItem.remove();
                    
                    // Show default message if no equipment left
                    if (equipmentContainer.children.length === 0) {
                        equipmentContainer.innerHTML = '<p class="text-muted">No equipment added yet</p>';
                    }
                });
            });
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('equipmentModal')).hide();
        });
        
        // Word counter for description
        const descriptionField = document.getElementById('description');
        const wordCountDisplay = document.getElementById('descriptionWordCount');

        descriptionField.addEventListener('input', function() {
            const wordCount = this.value.length;
            wordCountDisplay.textContent = `${wordCount}/255 words`;
        });

        // Word counter for location note
        const locationNoteField = document.getElementById('locationNote');
        const locationNoteWordCountDisplay = document.getElementById('locationNoteWordCount');

        locationNoteField.addEventListener('input', function() {
            const wordCount = this.value.length;
            locationNoteWordCountDisplay.textContent = `${wordCount}/80 words`;
        });

        // Form submission
        document.getElementById('addFacilityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would collect all form data and send it to the server
            alert('Facility added successfully!');
            // In a real implementation, you would use fetch() or similar to send data to your backend
        });
    });
</script>

    </main>
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
  <!-- Combined JS resources -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="js/global.js"></script>
</body>
</html>