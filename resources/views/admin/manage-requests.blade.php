@extends('layouts.admin')

@section('title', 'Admin Dashboard • Manage Requisitions')

@section('content')
<style>

  .btn-secondary {
    background-color: #889096ff;
    color: white;
  }

  .filters-column {
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    height: 100%;
    position: sticky;
    top: 70px; /* Account for topbar */
  }

  /* Main content container */
  .main-content-container {
    padding: 20px;
    width: 100%;
  }

  .content-area {
    background: #fff;
    padding: 1.5rem;
    border: 1px solid #dee2e6;
    border-radius: 0;
    width: 100%;
    /* Lessen the top margin to account for the topbar */
    margin-top: 3rem;
  }

  .requisition-card {
    margin-bottom: 1rem;
    width: 100%;
  }

  .requisition-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .card-label {
    font-weight: 600;
    color: #4a5568;
  }

  /* Status badges */
  .badge {
    font-size: 0.85rem;
    padding: 0.35em 0.65em;
  }

  .bg-pink { background-color: #FF69B4; }
  .bg-purple { background-color: #6f42c1; }

  /* Make cards more compact */
  .card-body {
    padding: 1rem;
  }

  /* Responsive adjustments */
  @media (max-width: 991.98px) {
    .filters-column {
      position: relative;
      border-right: none;
      border-bottom: 1px solid #dee2e6;
      margin-bottom: 20px;
      padding-bottom: 20px;
      top: 0;
    }

    .content-area {
      padding: 1rem;
    }
  }

  @media (max-width: 767.98px) {
    .card-body {
      padding: 0.75rem;
    }

    .d-flex.gap-2 {
      flex-direction: column;
      gap: 0.5rem !important;
    }

    .d-flex.gap-2 button {
      width: 100%;
    }

    .main-content-container {
      padding: 10px;
    }

    .modal-dialog {
      margin: 0.5rem;
    }
  }

  @media (max-width: 575.98px) {
    .d-flex.justify-content-between {
      flex-direction: column;
      gap: 1rem;
    }

    .d-flex.justify-content-between input[type="search"] {
      width: 100% !important;
    }

    .row.mb-3 .col-md-6 {
      margin-bottom: 0.5rem;
    }

    .content-area {
      padding: 0.75rem;
    }
    
    #requisitionContainer {
      max-height: none !important;
    }
  }
</style>

<div class="main-content-container">
  <div class="content-area">
    <div class="row g-3">
      <!-- Left Sidebar Filters -->
      <div class="col-lg-3">
        <div class="filters-column p-3">
          <h5 class="mb-3">Filters</h5>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select form-select-sm" id="statusFilter">
              <option value="all">All Statuses</option>
              <option value="pending">Pending Approval</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select class="form-select form-select-sm" id="typeFilter">
              <option value="all">All Types</option>
              <option value="facility">Facility</option>
              <option value="equipment">Equipment</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Date Range</label>
            <input type="date" class="form-control form-control-sm mb-2" id="startDate">
            <input type="date" class="form-control form-control-sm" id="endDate">
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0">Manage Requisitions</h4>
          <div class="d-flex gap-2">
            <input type="search" class="form-control form-control-sm" placeholder="Search requisitions..." style="width: 200px;">
            <button class="btn btn-primary btn-sm">Search</button>
          </div>
        </div>

        <div id="requisitionContainer" class="overflow-auto" style="max-height: calc(100vh - 250px);">
          <!-- Cards will be populated dynamically -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- All your modal definitions remain exactly the same -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <!-- ... existing modal content ... -->
</div>

<div class="modal fade" id="manageRequisitionModal" tabindex="-1" aria-hidden="true">
    <!-- ... existing modal content ... -->
</div>

<div class="modal fade" id="approvalHistoryModal" tabindex="-1" aria-hidden="true">
    <!-- ... existing modal content ... -->
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const requisitionContainer = document.getElementById('requisitionContainer');

    async function fetchAndDisplayForms() {
      try {
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

        const forms = await response.json();
        requisitionContainer.innerHTML = '';
        
        forms.forEach(form => {
          const statusName = getStatusName(form.status_id);
          const statusColor = getStatusColor(statusName);
          
          const card = `
            <div class="card requisition-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <h5 class="card-title mb-0">Request #${form.request_id}</h5>
                  <span class="badge bg-${statusColor}">${statusName}</span>
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
          requisitionContainer.innerHTML += card;
        });

      } catch (error) {
        console.error('Error:', error);
        requisitionContainer.innerHTML = '<div class="alert alert-danger">Failed to load forms</div>';
      }
    }

    // Helper function to get status name
    function getStatusName(statusId) {
      const statusMap = {
        1: 'Pending Approval',
        2: 'In Review',
        3: 'Awaiting Payment',
        4: 'Scheduled',
        5: 'Ongoing',
        6: 'Returned',
        7: 'Late Return',
        8: 'Completed',
        9: 'Rejected',
        10: 'Cancelled'
      };
      return statusMap[statusId] || 'Unknown';
    }

    // Helper function to get status color
    function getStatusColor(status) {
      const colors = {
        'Pending Approval': 'warning',
        'In Review': 'info',
        'Awaiting Payment': 'pink',
        'Scheduled': 'purple',
        'Ongoing': 'primary',
        'Returned': 'secondary',
        'Late Return': 'danger',
        'Completed': 'success',
        'Rejected': 'danger',
        'Cancelled': 'secondary'
      };
      return colors[status] || 'secondary';
    }

    // Initial load
    fetchAndDisplayForms();
    
    // Refresh every 30 seconds
    setInterval(fetchAndDisplayForms, 30000);
  });
</script>
@endsection