@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <style>

        /* New styles for the dashboard header */
        .dashboard-header {
            position: relative;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
            overflow: hidden;
            color: white;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background-image: url("{{ asset('assets/cpu-pic1.jpg') }}");
            background-size: cover;
            background-position: center;
            z-index: -1;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 51, 102, 0.8), rgba(0, 51, 102, 0.5));
            z-index: -1;
        }

    </style>

    <div id="main-container">
        <!-- Main Content -->
        <main>
            <!-- Dashboard Header with Wallpaper -->
            <div class="dashboard-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0">Your Dashboard</h2>
                    <a href="#" id="manageProfileBtn" class="btn btn-light">
                        <i class="bi bi-gear me-1"></i> Edit Profile
                    </a>
                </div>
            </div>
<!-- Stats Cards -->
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100 hover-effect">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <span class="text-muted small">Ongoing Events</span>
            <h2 class="mt-2 mb-0 fw-bold" id="ongoingEvents">0</h2>
          </div>
          <a href="{{ asset('admin/calendar') }}" class="text-primary text-decoration-none">
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center border border-2 border-primary" style="width: 45px; height: 45px; border-color: #5d759917 !important;">
              <i class="fa-solid fa-angle-right fs-5"></i>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100 hover-effect">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <span class="text-muted small">Pending Requests</span>
            <h2 class="mt-2 mb-0 fw-bold" id="pendingRequests">0</h2>
          </div>
          <a href="{{ asset('admin/manage-requests') }}" class="text-primary text-decoration-none">
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center border border-2 border-primary" style="width: 45px; height: 45px; border-color: #5d759917 !important;">
              <i class="fa-solid fa-angle-right fs-5"></i>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100 hover-effect">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <span class="text-muted small">Total Requisitions</span>
            <h2 class="mt-2 mb-0 fw-bold" id="totalRequisitions">0</h2>
          </div>
          <a href="{{ asset('admin/archives') }}" class="text-primary text-decoration-none">
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center border border-2 border-primary" style="width: 45px; height: 45px; border-color: #5d759917 !important;">
              <i class="fa-solid fa-angle-right fs-5"></i>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Left Column -->
  <div class="col-md-4 d-flex flex-column gap-3">
    <!-- Equipment Condition Tracker -->
    <div class="card p-3 flex-fill" style="height: 294px;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Inventory Tracker</h5>
        <span class="badge bg-primary" id="equipmentCount">0 items</span>
      </div>
      <div class="equipment-list-container" style="height: 220px; overflow-y: auto;">
        <div id="equipmentList" class="d-flex flex-column gap-2">
          <!-- Equipment items will be loaded here dynamically -->
        </div>
      </div>
    </div>
    
    <div class="card p-3 flex-fill" style="height: 294px;">
      <!-- Bottom container (empty for now) -->
    </div>
  </div>

  <!-- Right Column -->
  <div class="col-md-8">
    <!-- Calendar Section -->
    <div class="card p-3 h-100" style="height: 600px;">
      <div class="card border-0 h-100">
        <div class="card-body p-2 h-100">
          <div id="calendar" style="height: 100%;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
            <!-- System Log Section -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold">System Log</h3>
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="adminRoleDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person me-1"></i> Filter by Role
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="adminRoleDropdown">
                                    <li><button class="dropdown-item" data-filter="head-admin">Head Admin</button></li>
                                    <li><button class="dropdown-item" data-filter="assistant-admin">Assistant Admin</button>
                                    </li>
                                    <li><button class="dropdown-item" data-filter="all">Show All</button></li>
                                </ul>
                            </div>
                            <input type="date" class="form-control" id="logDateFilter" placeholder="Filter by Date">
                        </div>
                    </div>

                    <!-- System log container -->
                    <div id="systemLog" class="border rounded p-3 log-container">
                        <ul class="list-group">
                            <!-- Example log entries -->
                            <li class="list-group-item">
                                <strong>John Doe</strong> (Head Admin) approved a requisition for the Main Auditorium on
                                <em>March 15, 2024</em>.
                            </li>
                            <li class="list-group-item">
                                <strong>Jane Smith</strong> (Assistant Admin) added new equipment: Sound System on
                                <em>March 10, 2024</em>.
                            </li>
                            <li class="list-group-item">
                                <strong>John Doe</strong> (Head Admin) deleted a facility: Old Gymnasium on
                                <em>March 5, 2024</em>.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

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
                    <!-- Additional event details will be inserted here -->
                    <div id="eventDetails"></div>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/calendar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show loading state
            document.getElementById('equipmentList').innerHTML = `
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <div class="mt-2">Loading equipment...</div>
                </div>
            `;

            const adminId = localStorage.getItem('adminId');
            const manageProfileBtn = document.getElementById('manageProfileBtn');
            if (adminId) {
                manageProfileBtn.href = `/admin/profile/${adminId}`;
            }

            // Get the authentication token
            const token = localStorage.getItem('adminToken');

            if (!token) {
                console.error('No authentication token found');
                return;
            }

            // Fetch requisition data from API with authentication
            fetch('http://127.0.0.1:8000/api/admin/requisition-forms', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'include'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);

                    let totalRequisitions = data.length;
                    let pendingRequests = 0;
                    let ongoingEvents = 0;

                    data.forEach(item => {
                        const status = item.form_details.status.name;

                        if (status === 'Pending Approval' || status === 'Awaiting Payment') {
                            pendingRequests++;
                        }

                        if (status === 'Scheduled' || status === 'Ongoing') {
                            ongoingEvents++;
                        }
                    });

                    document.getElementById('totalRequisitions').textContent = totalRequisitions;
                    document.getElementById('pendingRequests').textContent = pendingRequests;
                    document.getElementById('ongoingEvents').textContent = ongoingEvents;
                })
                .catch(error => {
                    console.error('Error fetching requisition data:', error);
                    document.getElementById('totalRequisitions').textContent = 'Error';
                    document.getElementById('pendingRequests').textContent = 'Error';
                    document.getElementById('ongoingEvents').textContent = 'Error';
                });

            // Fetch and display equipment data
            fetchEquipmentData();
        });

        function fetchEquipmentData() {
            console.log('Fetching equipment data...');
            fetch('http://127.0.0.1:8000/api/equipment', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Equipment API response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Equipment data received:', data);
                if (data && data.data) {
                    displayEquipmentItems(data.data);
                } else {
                    throw new Error('Invalid data format - no data array found');
                }
            })
            .catch(error => {
                console.error('Error fetching equipment data:', error);
                document.getElementById('equipmentList').innerHTML = `
                    <div class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                        <div class="mt-2">Failed to load equipment data</div>
                        <small class="text-muted">${error.message}</small>
                    </div>
                `;
            });
        }

        function displayEquipmentItems(equipment) {
            const equipmentList = document.getElementById('equipmentList');
            const equipmentCount = document.getElementById('equipmentCount');
            
            console.log('Displaying equipment items:', equipment);
            
            // Extract all items from all equipment
            const allItems = [];
            equipment.forEach(equip => {
                console.log(`Processing equipment: ${equip.equipment_name}`, equip.items);
                if (equip.items && equip.items.length > 0) {
                    equip.items.forEach(item => {
                        allItems.push({
                            ...item,
                            equipment_name: equip.equipment_name
                        });
                    });
                }
            });

            console.log('All items extracted:', allItems);

            if (allItems.length === 0) {
                equipmentList.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-4"></i>
                        <div class="mt-2">No equipment items found</div>
                    </div>
                `;
                equipmentCount.textContent = '0 items';
                return;
            }

            // Update count
            equipmentCount.textContent = `${allItems.length} ${allItems.length === 1 ? 'item' : 'items'}`;

            // Create equipment items list - WRAPPING STATUS INDICATOR AND CONDITION TEXT TOGETHER
            const itemsHTML = allItems.map((item, index) => {
                const conditionName = item.condition?.condition_name || 'Unknown';
                const conditionColor = item.condition?.color_code || '#6c757d';
                
                console.log(`Item ${index}:`, {
                    equipment_name: item.equipment_name,
                    item_name: item.item_name,
                    condition: conditionName,
                    color: conditionColor
                });
                
                return `
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="flex-grow-1 me-3" style="min-width: 0;">
                            <div class="small text-muted text-truncate">${item.equipment_name}</div>
                            <div class="fw-medium text-truncate">${item.item_name}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0" style="white-space: nowrap;">
                            <div style="width: 10px; height: 10px; border-radius: 50%; background-color: ${conditionColor}; border: 2px solid white; box-shadow: 0 0 0 1px #dee2e6;"></div>
                            <span class="small">${conditionName}</span>
                        </div>
                    </div>
                `;
            }).join('');

            equipmentList.innerHTML = itemsHTML;
            
            // Debug: Check the rendered HTML
            console.log('Rendered equipment list HTML:', equipmentList.innerHTML);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
@endsection