@extends('layouts.admin')

@section('title', 'Pending Requests')

@section('content')
  <style>
    .request-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: #dc3545;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 0.7rem;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1;
    }

    .requisition-card {
      position: relative;
    }

    .unread-card {
      border-left: 4px solid #0f4580ff !important;
      background-color: #f8f9fa;
    }

    .btn-secondary {
      background-color: #d6daddff;
      color: #222324ff;
    }

    .btn-secondary:hover {
  background-color: #c3c8ccff; /* slightly darker */
  color: #111214ff;
}

.btn-secondary:active {
  background-color: #b0b6bbff; /* deeper shade for pressed effect */
  color: #0f1011ff;
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
      border-color: #6e7d94ff;
    }

    .content-area {
      padding-top: 1rem;
      width: 100%;
    }

    .requisition-card {
      margin-bottom: 1rem;
      background-color: white;
      transition: background-color 0.3s ease;
    }

    .requisition-card:hover {
      background-color: #f6f8faff;
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

    /* Grid layout for compact cards */
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
      overflow-y: auto;
      max-height: calc(100vh - 300px);
      padding: 1rem; /* Added padding for requisitionContainer */
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


    .compact-info-column>div {
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
  </style>

  <main>
      <div class="card-header">
        <!-- Filter Bar with Search aligned right -->
        <div class="d-flex flex-column w-100">
          <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-2 filter-row">
            <!-- Left side: filters + layout toggle -->
            <div class="d-flex flex-wrap align-items-center gap-2">
              <select class="form-select filter-select" id="statusFilter">
                <option value="all">All Statuses</option>
                <!-- Only Pending Approval and Awaiting Payment will be shown -->
              </select>


              <select class="form-select filter-select" id="sortFilter">
                <option value="status">By Date</option>
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
              </select>

              <div class="btn-group" role="group">
                <button type="button" class="btn layout-toggle-btn" data-layout="detailed">
                  <i class="bi bi-list-ul"></i> Detailed
                </button>
                <button type="button" class="btn layout-toggle-btn active" data-layout="compact">
                  <i class="bi bi-grid"></i> Compact
                </button>
              </div>
            </div>

            <!-- Right side: search -->
            <div class="search-container">
              <input type="search" class="form-control" id="searchInput"
                placeholder="Search by request number...">
              <button class="btn btn-primary" id="searchButton">Search</button>
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

    <!-- Modal definitions remain the same -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
      <!-- ... existing modal content ... -->
    </div>

    <div class="modal fade" id="manageRequisitionModal" tabindex="-1" aria-hidden="true">
      <!-- ... existing modal content ... -->
    </div>

<!-- Approval History Modal -->
<div class="modal fade" id="approvalHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approval History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="approvalHistoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals" type="button" role="tab" aria-controls="approvals" aria-selected="true">
                            <i class="bi bi-hand-thumbs-up text-success me-1"></i>
                            Approvals <span class="badge bg-success ms-1" id="approvalsTabCount">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rejections-tab" data-bs-toggle="tab" data-bs-target="#rejections" type="button" role="tab" aria-controls="rejections" aria-selected="false">
                            <i class="bi bi-hand-thumbs-down text-danger me-1"></i>
                            Rejections <span class="badge bg-danger ms-1" id="rejectionsTabCount">0</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="approvalHistoryContent">
                    <div class="tab-pane fade show active" id="approvals" role="tabpanel" aria-labelledby="approvals-tab">
                        <div id="approvalsHistoryContent">
                            <div class="text-center text-muted py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading approvals...</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="rejections" role="tabpanel" aria-labelledby="rejections-tab">
                        <div id="rejectionsHistoryContent">
                            <div class="text-center text-muted py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading rejections...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
  </main>

@endsection

@section('scripts')
  <script>

    // Update the showApprovalHistory function
function showApprovalHistory(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('approvalHistoryModal'));
    loadApprovalHistory(requestId);
    modal.show();
}

// Function to load approval history
async function loadApprovalHistory(requestId) {
    try {
        const adminToken = localStorage.getItem('adminToken');
        if (!adminToken) {
            throw new Error('No authentication token found');
        }

        console.log('Fetching approval history for request:', requestId);
        
        const response = await fetch(`/api/admin/requisition/${requestId}/approval-history`, {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const approvalHistory = await response.json();
        console.log('Approval history data:', approvalHistory);

        // Separate approvals and rejections
        const approvals = approvalHistory.filter(item => item.action === 'approved');
        const rejections = approvalHistory.filter(item => item.action === 'rejected');

        // Update tab counts
        document.getElementById('approvalsTabCount').textContent = approvals.length;
        document.getElementById('rejectionsTabCount').textContent = rejections.length;

        // Update content
        document.getElementById('approvalsHistoryContent').innerHTML = generateApprovalHistoryHTML(approvals);
        document.getElementById('rejectionsHistoryContent').innerHTML = generateApprovalHistoryHTML(rejections);

    } catch (error) {
        console.error('Error loading approval history:', error);
        const errorHtml = `<div class="text-center text-danger py-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Failed to load history: ${error.message}
        </div>`;
        document.getElementById('approvalsHistoryContent').innerHTML = errorHtml;
        document.getElementById('rejectionsHistoryContent').innerHTML = errorHtml;
    }
}

// Function to generate approval history HTML (same as in request-view.blade.php)
function generateApprovalHistoryHTML(history) {
    if (!history || history.length === 0) {
        return '<div class="text-center text-muted py-4">No records found</div>';
    }

    return history.map(item => `
        <div class="d-flex align-items-center mb-3 p-2 border rounded">
            <div class="me-3 flex-shrink-0">
                ${item.admin_photo ?
                    `<img src="${item.admin_photo}" class="rounded-circle" width="45" height="45" alt="${item.admin_name}" style="object-fit: cover;">` :
                    `<div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" style="width: 45px; height: 45px;">
                        ${item.admin_name.split(' ').map(n => n.charAt(0)).join('')}
                    </div>`
                }
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong class="d-block">${item.admin_name}</strong>
                        <small class="text-muted">
                            <i class="fa ${item.action_icon} ${item.action_class} me-1"></i>
                            ${item.action} this request
                        </small>
                        ${item.remarks ? `<div class="mt-1 small text-muted">"${item.remarks}"</div>` : ''}
                    </div>
                    <small class="text-muted text-end">${item.formatted_date}</small>
                </div>
            </div>
        </div>
    `).join('');
}

    // Define handleManage globally so it can be accessed by onclick
    function handleManage(requestId) {
      if (!requestId) {
        console.error('No request ID provided');
        return;
      }
      // Mark as read when managing the request
      window.location.href = `/admin/requisition/${requestId}`;
    }

   function showApprovalHistory(requestId) {
    console.log('Showing approval history for request:', requestId);
    
    // Initialize the modal
    const modalElement = document.getElementById('approvalHistoryModal');
    if (!modalElement) {
        console.error('Modal element not found');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    
    // Set loading state
    document.getElementById('approvalsHistoryContent').innerHTML = `
        <div class="text-center text-muted py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading approvals...</p>
        </div>
    `;
    
    document.getElementById('rejectionsHistoryContent').innerHTML = `
        <div class="text-center text-muted py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading rejections...</p>
        </div>
    `;
    
    // Reset tab counts
    document.getElementById('approvalsTabCount').textContent = '0';
    document.getElementById('rejectionsTabCount').textContent = '0';
    
    // Show the modal first
    modal.show();
    
    // Then load the data
    loadApprovalHistory(requestId);
}

    document.addEventListener('DOMContentLoaded', function () {
      const requisitionContainer = document.getElementById('requisitionContainer');
      const layoutToggleButtons = document.querySelectorAll('.layout-toggle-btn');
      const statusFilter = document.getElementById('statusFilter');
      const sortFilter = document.getElementById('sortFilter');
      const searchInput = document.getElementById('searchInput');
      const searchButton = document.getElementById('searchButton');
      
      let unreadRequests = new Set();
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

      function updateRequisitionNavBadge() {
        const navBadge = document.getElementById('requisitionNotificationBadge');
        if (navBadge) {
          if (unreadRequests.size > 0) {
            navBadge.textContent = unreadRequests.size > 99 ? '99+' : unreadRequests.size;
            navBadge.style.display = 'flex';
          } else {
            navBadge.style.display = 'none';
          }
        }
      }


      function markRequestAsRead(requestId) {
        unreadRequests.delete(requestId.toString());
        localStorage.setItem('unreadRequests', JSON.stringify([...unreadRequests]));
        updateRequisitionNavBadge();
        displayForms(formsData); // Re-render to remove badge
      }

      function displayForms(forms) {
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
          requisitionContainer.style.padding = '1rem'; // Added padding for detailed layout
        }

        forms.forEach(form => {
          const statusName = getStatusName(form.status_id);
          const statusColor = getStatusColor(form.status_id);
          const requestNumber = form.request_id.toString().padStart(4, '0');
          const isUnread = unreadRequests.has(form.request_id.toString());

          let cardHtml = '';

          if (currentLayout === 'compact') {
            cardHtml = `
                    <div class="card requisition-card compact-card mb-1 ${isUnread ? 'unread-card' : ''}" 
                         data-request-id="${form.request_id}">
                        ${isUnread ? '<span class="request-badge"></span>' : ''}
                        <div class="card-body p-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="card-title mb-0 fw-bold">Request #${requestNumber}</h5>

                                <!-- Circle action buttons -->
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-secondary circle-btn view-history-btn"
                                            data-request-id="${form.request_id}" 
                                            title="Approval History">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary circle-btn manage-btn"
                                            data-request-id="${form.request_id}" 
                                            title="Manage Request">
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
      <div class="card requisition-card ${isUnread ? 'unread-card' : ''}" 
           data-request-id="${form.request_id}">
          ${isUnread ? '<span class="request-badge"></span>' : ''}
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
                  <button class="btn btn-primary manage-btn" 
                          data-request-id="${form.request_id}">Review Request</button>
                  <button class="btn btn-secondary view-history-btn" 
                          data-request-id="${form.request_id}">View Approval History</button>
              </div>
          </div>
      </div>
    `;
          }

          requisitionContainer.innerHTML += cardHtml;
        });
        addCardEventListeners();
      }

      function addCardEventListeners() {
        // REMOVED: Card click to mark as read - now only the manage button will mark as read

        // Manage button - now marks as read
document.querySelectorAll('.manage-btn').forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.stopPropagation();
    const requestId = this.getAttribute('data-request-id');
    handleManage(requestId);
  });
});

        // History button - does NOT mark as read
        document.querySelectorAll('.view-history-btn').forEach(btn => {
          btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const requestId = this.getAttribute('data-request-id');
            showApprovalHistory(requestId);
          });
        });
      }

      function trackUnreadRequests() {
        const savedUnread = localStorage.getItem('unreadRequests');
        if (savedUnread) {
          unreadRequests = new Set(JSON.parse(savedUnread));
        }
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
          const statusResponse = await fetch('/api/form-statuses', {
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

          // Populate status filter - only show Pending Approval and Awaiting Payment
          statusData.forEach(status => {
            if (status.status_name === 'Pending Approval' || status.status_name === 'Awaiting Payment') {
              const option = document.createElement('option');
              option.value = status.status_id;
              option.textContent = status.status_name;
              statusFilter.appendChild(option);
            }
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
      searchButton.addEventListener('click', function () {
        updateCurrentFilters();
        applyFilters();
      });

      searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
          updateCurrentFilters();
          applyFilters();
        }
      });

      // Filter change listeners
      statusFilter.addEventListener('change', function () {
        updateCurrentFilters();
        applyFilters();
      });

      sortFilter.addEventListener('change', function () {
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
        switch (currentFilters.sort) {
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

          const response = await fetch('/api/admin/simplified-forms', {
            headers: {
              'Authorization': `Bearer ${adminToken}`,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const newFormsData = await response.json();

          // Check for new requests and add to unread set
          const currentRequestIds = new Set(formsData.map(form => form.request_id.toString()));
          newFormsData.forEach(form => {
            if (!currentRequestIds.has(form.request_id.toString())) {
              unreadRequests.add(form.request_id.toString());
            }
          });

          // Save updated unread requests
          localStorage.setItem('unreadRequests', JSON.stringify([...unreadRequests]));

          formsData = newFormsData;
          applyFilters();

        } catch (error) {
          console.error('Error fetching forms:', error);
          requisitionContainer.innerHTML = '<div class="alert alert-danger">Failed to load requisitions. Please try again later.</div>';
        }
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

      // Initialize tracking when DOM loads
      trackUnreadRequests();

      // Initial load
      fetchFilterOptions().then(() => {
        fetchAndDisplayForms(true);
      });

      // Refresh every 30 seconds without showing loading spinner
      setInterval(() => {
        fetchAndDisplayForms(false);
      }, 30000);
    });
  </script>
@endsection