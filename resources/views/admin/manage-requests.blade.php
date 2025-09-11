@extends('layouts.admin')

@section('title', 'Review Submissions')

@section('content')
  <style>
    .btn-secondary {
      background-color: #889096ff;
      color: white;
    }

    /* Filter bar styles */
    .filter-bar {
      background: #f8f9fa;
      padding: 1rem;
      border: 1px solid #dee2e6;
      margin-bottom: 1.5rem;
      border-radius: 0.375rem;
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: center;
    }

    .filter-select {
      min-width: 150px;
      width: auto;
    }

    .layout-toggle-btn {
      border: 1px solid #dee2e6;
      background: white;
    }

    .layout-toggle-btn.active {
      background-color: #e9ecef;
      border-color: #0d6efd;
    }

    .content-area {
      padding: 1.5rem;
      width: 100%;
      min-height: 400px;
    }

.requisition-card {
  margin-bottom: 1rem;
  height: auto;
  background-color: white;
  transition: background-color 0.3s ease; /* smooth fade */
}

.requisition-card:hover {
  background-color: #f6f8faff; /* slightly darker */
}


    .compact-card {
      padding: 0.75rem;
    }

    .compact-card .card-body {
      padding: 0.5rem;
    }

    .card-label {
      font-weight: 600;
      color: #4a5568;
      font-size: 0.875rem;
    }

    .compact-card .card-label {
      font-size: 0.8125rem;
    }

    .compact-info-row {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.5rem;
      margin-bottom: 0.75rem;
    }

    /* Status badges */
    .badge {
      font-size: 0.85rem;
      padding: 0.35em 0.65em;
    }

    .bg-pink {
      background-color: #FF69B4;
    }

    .bg-purple {
      background-color: #6f42c1;
    }

    /* Grid layout for compact cards */
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
      overflow-y: auto;
      max-height: calc(100vh - 300px);
    }

    .cards-container {
      overflow-y: auto;
      max-height: calc(100vh - 300px);
    }

    /* Center content for empty state */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 200px;
      color: #6c757d;
    }

    /* Loading spinner */
    .loading-spinner {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 200px;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
      .filter-bar {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-select {
        min-width: 100%;
      }
    }

    @media (max-width: 767.98px) {
      .compact-info-row {
        grid-template-columns: 1fr;
      }

      .content-area {
        padding: 1rem;
      }

      .cards-grid {
        grid-template-columns: 1fr;
      }
      
      .filter-row {
        flex-direction: column;
      }
      
      .search-container {
        width: 100%;
        margin-top: 0.5rem;
      }
      
      .search-container input {
        width: 100% !important;
      }
    }

    @media (max-width: 575.98px) {
      .filter-bar {
        padding: 0.75rem;
      }

      .content-area {
        padding: 0.75rem;
      }

      #requisitionContainer {
        max-height: none !important;
      }

      .cards-grid {
        max-height: none !important;
      }
    }

    .compact-info-column > div {
      margin-bottom: 0.25rem;
    }

    /* Center circle buttons */
    .circle-btn {
      width: 30px;
      height: 30px;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
    
    .circle-btn i {
      font-size: 0.9rem;
      line-height: 1;
      margin: 0;
    }
    
    .filter-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.5rem;
      width: 100%;
    }
    
    .search-container {
      display: flex;
      gap: 0.5rem;
      margin-left: auto;
    }
    
    .search-container input {
      width: 200px;
    }
  </style>

  <main id="main">
    <!-- Main Content Container as Card -->
    <div class="main-content-container card">
      <div class="card-header">
        <!-- Filter Bar with Search aligned right -->
        <div class="d-flex flex-column w-100">
          <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-2 filter-row">
            <!-- Left side: filters + layout toggle -->
            <div class="d-flex flex-wrap align-items-center gap-2">
              <select class="form-select form-select-sm filter-select" id="statusFilter">
                <option value="all">All Statuses</option>
                <!-- Status options will be populated dynamically -->
              </select>


              <select class="form-select form-select-sm filter-select" id="sortFilter">
                <option value="status">By Status</option>
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
              </select>

              <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm layout-toggle-btn" data-layout="detailed">
                  <i class="bi bi-list-ul"></i> Detailed
                </button>
                <button type="button" class="btn btn-sm layout-toggle-btn active" data-layout="compact">
                  <i class="bi bi-grid"></i> Compact
                </button>
              </div>
            </div>

            <!-- Right side: search -->
            <div class="search-container">
              <input type="search" class="form-control form-control-sm" id="searchInput" placeholder="Search by request number...">
              <button class="btn btn-primary btn-sm" id="searchButton">Search</button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <!-- Content Area -->
        <div class="content-area">
          <div id="requisitionContainer">
            <!-- Loading state initially -->
            <div class="loading-spinner">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading Requisitions...</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal definitions remain the same -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
      <!-- ... existing modal content ... -->
    </div>

    <div class="modal fade" id="manageRequisitionModal" tabindex="-1" aria-hidden="true">
      <!-- ... existing modal content ... -->
    </div>

    <div class="modal fade" id="approvalHistoryModal" tabindex="-1" aria-hidden="true">
      <!-- ... existing modal content ... -->
    </div>
  </main>

@endsection

@section('scripts')
  <script>
    // Define handleManage globally so it can be accessed by onclick
    function handleManage(requestId) {
      if (!requestId) {
        console.error('No request ID provided');
        return;
      }
      window.location.href = `/admin/requisition/${requestId}`;
    }

    function showApprovalHistory(requestId) {
      // Implementation for showing approval history
      console.log('Showing approval history for request:', requestId);
    }

    document.addEventListener('DOMContentLoaded', function () {
      const requisitionContainer = document.getElementById('requisitionContainer');
      const layoutToggleButtons = document.querySelectorAll('.layout-toggle-btn');
      const statusFilter = document.getElementById('statusFilter');
      const sortFilter = document.getElementById('sortFilter');
      const searchInput = document.getElementById('searchInput');
      const searchButton = document.getElementById('searchButton');
      
      let currentLayout = 'compact'; // Set compact as default
      let formsData = []; // Store forms data to avoid refetching
      let statusOptions = [];
      let currentFilters = {
        status: 'all',
        sort: 'status',
        search: ''
      };

      // Store current filter values
      function updateCurrentFilters() {
        currentFilters = {
          status: statusFilter.value,
          sort: sortFilter.value,
          search: searchInput.value.trim()
        };
        
        // Save to localStorage for persistence
        localStorage.setItem('requisitionFilters', JSON.stringify(currentFilters));
        localStorage.setItem('requisitionLayout', currentLayout);
      }

      // Load saved filter values
      function loadSavedFilters() {
        const savedFilters = localStorage.getItem('requisitionFilters');
        const savedLayout = localStorage.getItem('requisitionLayout');
        
        if (savedFilters) {
          const filters = JSON.parse(savedFilters);
          statusFilter.value = filters.status;
          sortFilter.value = filters.sort;
          searchInput.value = filters.search;
          currentFilters = filters;
        }
        
        if (savedLayout) {
          currentLayout = savedLayout;
          // Update layout toggle buttons
          layoutToggleButtons.forEach(btn => {
            const layout = btn.getAttribute('data-layout');
            if (layout === currentLayout) {
              btn.classList.add('active');
            } else {
              btn.classList.remove('active');
            }
          });
        }
      }

      // Fetch status and purpose options
      async function fetchFilterOptions() {
        try {
          const adminToken = localStorage.getItem('adminToken');
          if (!adminToken) {
            throw new Error('No authentication token found');
          }

          // Fetch status options
          const statusResponse = await fetch('http://127.0.0.1:8000/api/form-statuses', {
            headers: {
              'Authorization': `Bearer ${adminToken}`,
              'Accept': 'application/json'
            }
          });

          if (!statusResponse.ok) {
            throw new Error(`HTTP error! status: ${statusResponse.status}`);
          }

          const statusData = await statusResponse.json();
          statusOptions = statusData;
          
          // Populate status filter
          statusData.forEach(status => {
            const option = document.createElement('option');
            option.value = status.status_id;
            option.textContent = status.status_name;
            statusFilter.appendChild(option);
          });

          // Load saved filters after options are populated
          loadSavedFilters();

        } catch (error) {
          console.error('Error fetching filter options:', error);
        }
      }

      // Layout toggle functionality
      layoutToggleButtons.forEach(button => {
        button.addEventListener('click', function () {
          const layout = this.getAttribute('data-layout');
          layoutToggleButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          currentLayout = layout;
          updateCurrentFilters();
          displayForms(formsData); // Use cached data for instant layout switching
        });
      });

      // Search functionality
      searchButton.addEventListener('click', function() {
        updateCurrentFilters();
        applyFilters();
      });

      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          updateCurrentFilters();
          applyFilters();
        }
      });

      // Filter change listeners
      statusFilter.addEventListener('change', function() {
        updateCurrentFilters();
        applyFilters();
      });
      
      sortFilter.addEventListener('change', function() {
        updateCurrentFilters();
        applyFilters();
      });

      function applyFilters() {
        let filteredData = [...formsData];
        
        // Apply status filter
        if (currentFilters.status !== 'all') {
          filteredData = filteredData.filter(form => form.status_id.toString() === currentFilters.status);
        }
      
        
        // Apply search filter
        if (currentFilters.search) {
          filteredData = filteredData.filter(form => 
            form.request_id.toString().includes(currentFilters.search)
          );
        }
        
        // Apply sorting
        switch(currentFilters.sort) {
          case 'newest':
            filteredData.sort((a, b) => b.request_id - a.request_id);
            break;
          case 'oldest':
            filteredData.sort((a, b) => a.request_id - b.request_id);
            break;
          case 'status':
          default:
            filteredData.sort((a, b) => a.status_id - b.status_id);
            break;
        }
        
        displayForms(filteredData);
      }

      async function fetchAndDisplayForms(showLoading = true) {
        try {
          // Show loading state only if explicitly requested
          if (showLoading) {
            requisitionContainer.innerHTML = `
              <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading Requisitions...</p>
              </div>
            `;
          }

          const adminToken = localStorage.getItem('adminToken');
          if (!adminToken) {
            throw new Error('No authentication token found');
          }

          const response = await fetch('http://127.0.0.1:8000/api/admin/simplified-forms', {
            headers: {
              'Authorization': `Bearer ${adminToken}`,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          formsData = await response.json();
          applyFilters(); // Apply filters with current settings

        } catch (error) {
          console.error('Error fetching forms:', error);
          requisitionContainer.innerHTML = '<div class="alert alert-danger">Failed to load requisitions. Please try again later.</div>';

          // Log additional error details for debugging (without exposing sensitive data)
          if (error.message.includes('authentication')) {
            console.warn('Authentication issue detected. Redirecting to login...');
            // You might want to redirect to login here
          } else if (error.message.includes('network')) {
            console.warn('Network error detected. Please check your connection.');
          }
        }
      }

      function displayForms(forms) {
        // Clear container and set appropriate class
        requisitionContainer.innerHTML = '';
        
        if (forms.length === 0) {
          requisitionContainer.innerHTML = `
            <div class="empty-state">
              <i class="bi bi-inbox" style="font-size: 3rem;"></i>
              <p class="mt-3 text-muted">No requisitions found matching your criteria.</p>
            </div>
          `;
          return;
        }

        if (currentLayout === 'compact') {
          requisitionContainer.classList.add('cards-grid');
          requisitionContainer.classList.remove('overflow-auto');
        } else {
          requisitionContainer.classList.remove('cards-grid');
          requisitionContainer.classList.add('overflow-auto');
          requisitionContainer.style.maxHeight = 'calc(100vh - 300px)';
        }

        forms.forEach(form => {
          const statusName = getStatusName(form.status_id);
          const statusColor = getStatusColor(form.status_id);
          const requestNumber = form.request_id.toString().padStart(4, '0');

          let cardHtml = '';

          if (currentLayout === 'compact') {
            cardHtml = `
              <div class="card requisition-card compact-card mb-1">
                <div class="card-body p-1">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="card-title mb-0 fw-bold">Request #${requestNumber}</h5>

                    <!-- Circle action buttons -->
                    <div class="d-flex gap-1">
                      <button class="btn btn-sm btn-secondary circle-btn"
                              onclick="showApprovalHistory(${form.request_id})" title="Approval History">
                        <i class="bi bi-clock-history"></i>
                      </button>
                      <button class="btn btn-sm btn-primary circle-btn"
                              onclick="handleManage(${form.request_id})" title="Manage Request">
                        <i class="bi bi-pencil"></i>
                      </button>
                    </div>
                  </div>

                  <div class="compact-info-column">
                    <div>
                      <span class="card-label">Status:</span> 
                      <span class="badge" style="background-color: ${statusColor}">${statusName}</span>
                    </div>
                    <div>
                    <span class="card-label">Requester:</span> ${form.requester || 'N/A'}
                    </div>
                    <div>
                      <span class="card-label">Purpose:</span> ${form.purpose || 'N/A'}
                    </div>
                    <div>
                      <span class="card-label">Approvals:</span> ${form.approvals || '0'}
                    </div>
                    <div>
                      <span class="card-label">Rejections:</span> ${form.rejections || '0'}
                    </div>
                  </div>
                </div>
              </div>
            `;
          } else {
            cardHtml = `
              <div class="card requisition-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0">Request #${requestNumber}</h5>
                    <span class="badge" style="background-color: ${statusColor}">${statusName}</span>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-6">
                      <p class="mb-2"><span class="card-label">Requester:</span> ${form.requester}</p>
                      <p class="mb-2"><span class="card-label">Purpose:</span> ${form.purpose}</p>
                      <p class="mb-2"><span class="card-label">Schedule:</span> ${form.schedule}</p>
                    </div>
                    <div class="col-md-6">
                      <p class="mb-2"><span class="card-label">Facilities:</span> ${form.requested_items}</p>
                      <p class="mb-2"><span class="card-label">Tentative Fee:</span> ₱${form.tentative_fee}</p>
                      <p class="mb-2"><span class="card-label">Approvals:</span> ${form.approvals}</p>
                    </div>
                  </div>

                  <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="handleManage(${form.request_id})">Review Request</button>
                    <button class="btn btn-secondary" onclick="showApprovalHistory(${form.request_id})">View Approval History</button>
                  </div>
                </div>
              </div>
            `;
          }

          requisitionContainer.innerHTML += cardHtml;
        });
      }

      // Helper function to get status name
      function getStatusName(statusId) {
        const status = statusOptions.find(s => s.status_id === statusId);
        return status ? status.status_name : 'Unknown';
      }

      // Helper function to get status color
      function getStatusColor(statusId) {
        const status = statusOptions.find(s => s.status_id === statusId);
        return status ? status.color_code : '#6c757d'; // Default gray if not found
      }

      // Initial load
      fetchFilterOptions().then(() => {
        fetchAndDisplayForms(true); // Show loading on initial load
      });

      // Refresh every 30 seconds without showing loading spinner
      setInterval(() => {
        fetchAndDisplayForms(false);
      }, 30000);
    });
  </script>
@endsection