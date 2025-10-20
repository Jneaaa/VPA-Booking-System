@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
  <style>
    .card {
  border: 0 !important;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  border-radius: 0.75rem; /* optional, for smoother corners */
}

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

    /* Status badge styles */
    .status-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
    }

    .status-awaiting {
      background-color: #d1ecf1;
      color: #0c5460;
      border: 1px solid #bee5eb;
    }

    /* System log styles */
    .log-container {
      max-height: 150px;
      overflow-y: auto;
    }

    .system-log-item {
      border-left: 4px solid #007bff;
      padding-left: 1rem;
      margin-bottom: 1rem;
    }

    .system-log-item.approval {
      border-left-color: #28a745;
    }

    .system-log-item.rejection {
      border-left-color: #dc3545;
    }

    .system-log-item.comment {
      border-left-color: #17a2b8;
    }

    .system-log-item.fee {
      border-left-color: #ffc107;
    }

    .log-request-id {
      background-color: #e9ecef;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: bold;
    }
  </style>

  <div>
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
                  <div
                    class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 45px; height: 45px; border-color: #5d759917 !important;">
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
                  <div
                    class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 45px; height: 45px; border-color: #5d759917 !important;">
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
                  <div
                    class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 45px; height: 45px; border-color: #5d759917 !important;">
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
        <a href="{{ url('/admin/manage-equipment') }}" class="text-decoration-none">
            <h5 class="fw-bold mb-0 text-primary">Inventory Tracker</h5>
        </a>
        <span class="badge bg-primary" id="equipmentCount">0 items</span>
    </div>
    <div class="equipment-list-container" style="height: 170px; overflow-y: auto;">
        <div id="equipmentList" class="d-flex flex-column gap-2">
            <!-- Equipment items will be loaded here dynamically -->
        </div>
    </div>
    <!-- Added footer with scan equipment button -->
    <div class="card-footer border-0 bg-transparent px-0 pb-0 pt-2 mt-auto">
        <a href="{{ url('/admin/scan-equipment') }}" class="btn btn-secondary w-100 py-2">
            <i class="bi bi-qr-code-scan me-2"></i>Scan Equipment
        </a>
    </div>
</div>

<!-- Pending Requisitions List -->
<div class="card p-3 flex-fill" style="height: 294px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ url('/admin/manage-requests') }}" class="text-decoration-none">
            <h5 class="fw-bold mb-0 text-primary">Pending Requisitions</h5>
        </a>
        <span class="badge bg-primary" id="requisitionCount">0</span>
    </div>
    <div class="requisition-list-container" style="height: 220px; overflow-y: auto;">
        <div id="requisitionList" class="d-flex flex-column gap-2">
            <!-- Requisition items will be loaded here dynamically -->
            <div class="text-center text-muted py-4">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <div class="mt-2">Loading requisitions...</div>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Right Column -->
        <div class="col-md-8">
          <!-- Calendar Section -->
          <div class="card p-3 h-100" style="height: 600px;">
<div class="card border-0 h-100" style="box-shadow: none !important;">
  <div class="card-body p-2 h-100">
    <div id="calendar"></div>
  </div>
</div>

          </div>
        </div>
      </div>

      <!-- System Log Section -->
      <div class="card shadow-sm mt-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary">System Log</h4>
            <div class="d-flex gap-2">
              <input type="date" class="form-control" id="logDateFilter" placeholder="Filter by Date">
            </div>
          </div>

          <!-- System log container -->
          <div id="systemLog" class="border rounded p-3 log-container">
            <div class="text-center text-muted py-4">
              <div class="spinner-border spinner-border-sm" role="status"></div>
              <div class="mt-2">Loading system log...</div>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>


      <!-- Event Details Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 800px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="eventModalBody">
            <div class="card border-0 shadow-none mb-3 p-3">
              <table class="table table-bordered mb-0 w-100" style="table-layout: fixed; border: 1px solid #dee2e6;">
                <thead>
                  <tr>
                    <th class="bg-light p-2" style="width: 50%; border: 1px solid #dee2e6;">
                      Event Information
                    </th>
                    <th class="bg-light p-2" style="width: 50%; border: 1px solid #dee2e6;">
                      Requested Items
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="border: 1px solid #dee2e6; padding: 0;">
                      <table class="table mb-0 w-100" style="border-collapse: collapse;">
                        <tbody>
                          <tr>
                            <th class="bg-light text-nowrap p-2"
                              style="width: 40%; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                              Requester
                            </th>
                            <td id="modalRequester" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-nowrap p-2"
                              style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                              Purpose
                            </th>
                            <td id="modalPurpose" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-nowrap p-2"
                              style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                              Participants
                            </th>
                            <td id="modalParticipants" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-nowrap p-2"
                              style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                              Status
                            </th>
                            <td id="modalStatus" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-nowrap p-2"
                              style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                              Approved Fee
                            </th>
                            <td id="modalFee" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-nowrap p-2" style="border-right: 1px solid #dee2e6;">Approvals</th>
                            <td id="modalApprovals" class="p-2"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-nowrap p-2" style="border-right: 1px solid #dee2e6;">Rejections</th>
                            <td id="modalRejections" class="p-2"></td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                    <td style="border: 1px solid #dee2e6; vertical-align: top; padding: 0;">
                      <div id="modalItems" class="p-3" style="min-height: 100%;">
                        <!-- JS will insert items here -->
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="modalViewDetails">View Full Details</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script>
  // Calendar functionality from calendar.blade.php
let calendar;
let allRequests = [];
let filteredRequests = [];

// Initialize main calendar
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        titleFormat: {
            year: 'numeric',
            month: 'short'
        },
        height: '100%',
        handleWindowResize: true,
        windowResizeDelay: 200,
        aspectRatio: null,
        expandRows: true,
        events: [],
        eventClick: function (info) {
            const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);
            if (request) {
                showEventModal(request);
            }
        },
        eventDidMount: function (info) {
            // Add custom styling based on status
            const status = info.event.extendedProps.status;
            const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);

            if (request) {
                info.el.style.backgroundColor = request.form_details.status.color;
                info.el.style.borderColor = request.form_details.status.color;
                info.el.style.color = '#fff';
                info.el.style.fontWeight = 'bold';
            }
        },
        datesSet: function (info) {
            calendar.updateSize();
        },
        viewDidMount: function (info) {
            setTimeout(() => calendar.updateSize(), 0);
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        },
        slotMinTime: '00:00:00',
        slotMaxTime: '24:00:00',
        allDaySlot: false,
        nowIndicator: true,
        navLinks: true,
        dayHeaderFormat: { weekday: 'long', month: 'short', day: 'numeric' },
        views: {
            dayGridMonth: {
                dayHeaderFormat: {
                    weekday: 'short'
                }
            }
        },
        eventDisplay: 'block',
        dayMaxEvents: true,
        moreLinkClick: 'popover'
    });

    calendar.render();
    updateCalendarEvents();
}

// Filter and update calendar events
function updateCalendarEvents() {
    if (!calendar) return;

    calendar.removeAllEvents();

    // Filter requests by status - show all events in dashboard
    filteredRequests = allRequests.filter(req => {
        const statusName = req.form_details.status.name;
        return statusName === 'Scheduled' || statusName === 'Ongoing' || statusName === 'Late';
    });

    console.log('Dashboard calendar events:', {
        totalRequests: allRequests.length,
        filteredRequests: filteredRequests.length,
        filteredRequestIds: filteredRequests.map(r => r.request_id)
    });

    // Add filtered events to calendar
    filteredRequests.forEach(req => {
        const calendarTitle = req.form_details.calendar_info?.title ||
            `Request #${String(req.request_id).padStart(4, '0')}`;

        calendar.addEvent({
            title: calendarTitle,
            start: `${req.schedule.start_date}T${req.schedule.start_time}`,
            end: `${req.schedule.end_date}T${req.schedule.end_time}`,
            extendedProps: {
                status: req.form_details.status.name,
                requestId: req.request_id
            },
            description: req.form_details.calendar_info?.description,
            color: req.form_details.status.color
        });
    });
}

// Show event details in modal
function showEventModal(request) {
    const formattedRequestId = String(request.request_id).padStart(4, '0');
    const calendarTitle = request.form_details.calendar_info?.title || 'No Calendar Title';

    document.getElementById('eventModalTitle').textContent =
        `Request ID #${formattedRequestId} (${calendarTitle})`;

    document.getElementById('modalRequester').textContent =
        `${request.user_details.first_name} ${request.user_details.last_name}`;
    document.getElementById('modalPurpose').textContent = request.form_details.purpose;
    document.getElementById('modalParticipants').textContent = request.form_details.num_participants;
    document.getElementById('modalStatus').innerHTML = `
        <span class="badge" style="background-color: ${request.form_details.status.color}">
            ${request.form_details.status.name}
        </span>
    `;
    document.getElementById('modalFee').textContent = `₱${request.fees.approved_fee}`;
    document.getElementById('modalApprovals').textContent = `${request.approval_info.approval_count}`;
    document.getElementById('modalRejections').textContent = `${request.approval_info.rejection_count}`;

    // Format requested items
    let itemsHtml = '';

    if (request.requested_items.facilities.length > 0) {
        itemsHtml += '<div class="fw-bold small mb-1">Facilities:</div>';
        itemsHtml += request.requested_items.facilities.map(f =>
            `<div class="mb-1 small">• ${f.name} | ₱${f.fee}${f.rate_type === 'Per Hour' ? '/hour' : '/event'}${f.is_waived ? ' <span class="text-muted">(Waived)</span>' : ''}</div>`
        ).join('');
    }

    if (request.requested_items.equipment.length > 0) {
        itemsHtml += '<div class="fw-bold small mt-2 mb-1">Equipment:</div>';
        itemsHtml += request.requested_items.equipment.map(e =>
            `<div class="mb-1 small">• ${e.name} × ${e.quantity || 1} | ₱${e.fee}${e.rate_type === 'Per Hour' ? '/hour' : '/event'}${e.is_waived ? ' <span class="text-muted">(Waived)</span>' : ''}</div>`
        ).join('');
    }

    document.getElementById('modalItems').innerHTML = itemsHtml || '<p class="text-muted small">No items requested</p>';

    // Set up view details button
    document.getElementById('modalViewDetails').onclick = function () {
        window.location.href = `/admin/requisition/${request.request_id}`;
    };

    // Show the modal
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    eventModal.show();
}

// Fetch all requisition forms for calendar
async function fetchRequisitionFormsForCalendar() {
    try {
        const token = localStorage.getItem('adminToken');
        console.log('Fetching requisition forms for calendar...');

        const response = await fetch('http://127.0.0.1:8000/api/admin/requisition-forms', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch requisition forms: ${response.status} ${response.statusText}`);
        }

        allRequests = await response.json();

        console.log('Fetched calendar requisition forms:', {
            totalRequests: allRequests.length,
            requestIds: allRequests.map(r => r.request_id)
        });

        // Initialize calendar after data is loaded
        initializeCalendar();

    } catch (error) {
        console.error('Error fetching requisition forms for calendar:', error);
    }
}

// Load calendar data when dashboard loads
fetchRequisitionFormsForCalendar();

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

         if (status === 'Scheduled' || status === 'Ongoing' || status === 'Late') {
    ongoingEvents++;
}

          });

          document.getElementById('totalRequisitions').textContent = totalRequisitions;
          document.getElementById('pendingRequests').textContent = pendingRequests;
          document.getElementById('ongoingEvents').textContent = ongoingEvents;

          // Display pending requisitions (status_id 1 and 2)
          displayPendingRequisitions(data);

          // Load system log data
          loadSystemLog(data);
        })
        .catch(error => {
          console.error('Error fetching requisition data:', error);
          document.getElementById('totalRequisitions').textContent = 'Error';
          document.getElementById('pendingRequests').textContent = 'Error';
          document.getElementById('ongoingEvents').textContent = 'Error';

          // Show error in requisition list
          document.getElementById('requisitionList').innerHTML = `
                          <div class="text-center text-danger py-4">
                              <i class="bi bi-exclamation-triangle fs-4"></i>
                              <div class="mt-2">Failed to load requisitions</div>
                              <small class="text-muted">${error.message}</small>
                          </div>
                      `;
        });

      // Fetch and display equipment data
      fetchEquipmentData();

      // Add event listeners for filters
      document.getElementById('logDateFilter').addEventListener('change', function () {
        loadSystemLog();
      });

      document.querySelectorAll('#adminRoleDropdown + .dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function () {
          const filter = this.getAttribute('data-filter');
          loadSystemLog(null, filter);
        });
      });
    });

    async function loadSystemLog(requisitionsData = null, roleFilter = 'all') {
      try {
        const token = localStorage.getItem('adminToken');
        const dateFilter = document.getElementById('logDateFilter').value;

        let systemLogData = [];

        // If we have requisitions data, use it; otherwise fetch fresh data
        if (requisitionsData) {
          systemLogData = await processSystemLogData(requisitionsData);
        } else {
          const response = await fetch('http://127.0.0.1:8000/api/admin/requisition-forms', {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) throw new Error('Failed to fetch requisition data');
          const data = await response.json();
          systemLogData = await processSystemLogData(data);
        }

        // Apply filters
        let filteredData = systemLogData;

        if (dateFilter) {
          filteredData = filteredData.filter(item => {
            const itemDate = new Date(item.timestamp).toISOString().split('T')[0];
            return itemDate === dateFilter;
          });
        }

        if (roleFilter !== 'all') {
          filteredData = filteredData.filter(item => item.admin_role === roleFilter);
        }

        displaySystemLog(filteredData);

      } catch (error) {
        console.error('Error loading system log:', error);
        document.getElementById('systemLog').innerHTML = `
                      <div class="text-center text-danger py-4">
                          <i class="bi bi-exclamation-triangle fs-4"></i>
                          <div class="mt-2">Failed to load system log</div>
                          <small class="text-muted">${error.message}</small>
                      </div>
                  `;
      }
    }

    async function processSystemLogData(requisitions) {
      const systemLog = [];
      const token = localStorage.getItem('adminToken');

      // Process each requisition for system log entries
      for (const requisition of requisitions) {
        const requestId = requisition.request_id;
        const formattedRequestId = String(requestId).padStart(4, '0');

        // Get approval history for this requisition
        try {
          const approvalResponse = await fetch(`/api/admin/requisition/${requestId}/approval-history`, {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });

          if (approvalResponse.ok) {
            const approvalHistory = await approvalResponse.json();

            // Add approval/rejection entries to system log
            approvalHistory.forEach(history => {
              systemLog.push({
                type: history.action === 'approved' ? 'approval' : 'rejection',
                admin_name: history.admin_name,
                admin_role: history.admin_role || 'Admin',
                request_id: requestId,
                formatted_request_id: formattedRequestId,
                timestamp: history.created_at,
                remarks: history.remarks,
                action: history.action
              });
            });
          }
        } catch (error) {
          console.error(`Error fetching approval history for request ${requestId}:`, error);
        }

        // Get comments for this requisition
        try {
          const commentsResponse = await fetch(`/api/admin/requisition/${requestId}/comments`, {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });

          if (commentsResponse.ok) {
            const commentsData = await commentsResponse.json();
            const comments = commentsData.comments || [];

            // Add comment entries to system log
            comments.forEach(comment => {
              systemLog.push({
                type: 'comment',
                admin_name: `${comment.admin.first_name} ${comment.admin.last_name}`,
                admin_role: comment.admin.role || 'Admin',
                request_id: requestId,
                formatted_request_id: formattedRequestId,
                timestamp: comment.created_at,
                comment: comment.comment
              });
            });
          }
        } catch (error) {
          console.error(`Error fetching comments for request ${requestId}:`, error);
        }

        // Get fees for this requisition
        try {
          const feesResponse = await fetch(`/api/admin/requisition/${requestId}/fees`, {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          });

          if (feesResponse.ok) {
            const fees = await feesResponse.json();

            // Add fee entries to system log
            fees.forEach(fee => {
              const amount = parseFloat(fee.type === 'discount' ? fee.discount_amount : fee.fee_amount);
              const typeName = fee.type === 'discount' ? 'Discount' : 'Additional fee';
              const adminName = fee.added_by?.name || 'Admin';

              systemLog.push({
                type: 'fee',
                admin_name: adminName,
                admin_role: fee.added_by?.role || 'Admin',
                request_id: requestId,
                formatted_request_id: formattedRequestId,
                timestamp: fee.created_at,
                fee_label: fee.label,
                fee_amount: amount,
                fee_type: typeName
              });
            });
          }
        } catch (error) {
          console.error(`Error fetching fees for request ${requestId}:`, error);
        }
      }

      // Sort by timestamp (newest first)
      return systemLog.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    }

    function displaySystemLog(logEntries) {
      const systemLogContainer = document.getElementById('systemLog');

      if (logEntries.length === 0) {
        systemLogContainer.innerHTML = `
                      <div class="text-center text-muted py-4">
                          <i class="bi bi-inbox fs-4"></i>
                          <div class="mt-2">No system log entries found</div>
                      </div>
                  `;
        return;
      }

      const logHTML = logEntries.map(entry => {
        let logContent = '';
        const formattedTime = formatTimeAgo(entry.timestamp);

        switch (entry.type) {
          case 'approval':
            logContent = `
                              <strong>${entry.admin_name}</strong> approved requisition 
                              <span class="log-request-id">#${entry.formatted_request_id}</span>
                              ${entry.remarks ? `with remarks: "${entry.remarks}"` : ''}
                          `;
            break;

          case 'rejection':
            logContent = `
                              <strong>${entry.admin_name}</strong> rejected requisition 
                              <span class="log-request-id">#${entry.formatted_request_id}</span>
                              ${entry.remarks ? `with remarks: "${entry.remarks}"` : ''}
                          `;
            break;

          case 'comment':
            logContent = `
                              <strong>${entry.admin_name}</strong> commented on requisition 
                              <span class="log-request-id">#${entry.formatted_request_id}</span>: 
                              "${entry.comment}"
                          `;
            break;

          case 'fee':
            logContent = `
                              <strong>${entry.admin_name}</strong> added a ${entry.fee_type.toLowerCase()} 
                              "<strong>${entry.fee_label}</strong>" of ₱${entry.fee_amount.toFixed(2)} to requisition 
                              <span class="log-request-id">#${entry.formatted_request_id}</span>
                          `;
            break;
        }

        return `
                      <div class="system-log-item ${entry.type}">
                          <div class="d-flex justify-content-between align-items-start mb-1">
                              <div class="flex-grow-1">
                                  ${logContent}
                              </div>
                          </div>
                          <small class="text-muted">${formattedTime}</small>
                      </div>
                  `;
      }).join('');

      systemLogContainer.innerHTML = logHTML;
    }

    // Helper function to format time ago (e.g., "2 minutes ago")
    function formatTimeAgo(timestamp) {
      const now = new Date();
      const commentTime = new Date(timestamp);
      const diffInSeconds = Math.floor((now - commentTime) / 1000);

      if (diffInSeconds < 60) {
        return 'just now';
      } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
      } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
      } else if (diffInSeconds < 604800) {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days !== 1 ? 's' : ''} ago`;
      } else {
        return commentTime.toLocaleDateString();
      }
    }

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
              equipment_name: equip.equipment_name,
              equipment_id: equip.equipment_id // Make sure equipment_id is included
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

      // Create equipment items list - with clickable items
      const itemsHTML = allItems.map((item, index) => {
        const conditionName = item.condition?.condition_name || 'Unknown';
        const conditionColor = item.condition?.color_code || '#6c757d';
        const equipmentId = item.equipment_id;

        console.log(`Item ${index}:`, {
          equipment_id: equipmentId,
          equipment_name: item.equipment_name,
          item_name: item.item_name,
          condition: conditionName,
          color: conditionColor
        });

        return `
                      <div class="d-flex justify-content-between align-items-center py-2 border-bottom clickable-equipment-item" 
                           data-equipment-id="${equipmentId}"
                           style="cursor: pointer; transition: background-color 0.2s;"
                           onmouseover="this.style.backgroundColor='#f8f9fa'" 
                           onmouseout="this.style.backgroundColor='transparent'">
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

      // Add click event listeners to all equipment items
      addEquipmentItemClickListeners();

      // Debug: Check the rendered HTML
      console.log('Rendered equipment list HTML:', equipmentList.innerHTML);
    }

    function addEquipmentItemClickListeners() {
      const equipmentItems = document.querySelectorAll('.clickable-equipment-item');

      equipmentItems.forEach(item => {
        item.addEventListener('click', function () {
          const equipmentId = this.getAttribute('data-equipment-id');
          if (equipmentId) {
            // Redirect to the edit equipment page with the equipment ID
            window.location.href = `/admin/edit-equipment?id=${equipmentId}`;
          }
        });
      });

      console.log(`Added click listeners to ${equipmentItems.length} equipment items`);
    }

    function displayPendingRequisitions(requisitions) {
      const requisitionList = document.getElementById('requisitionList');
      const requisitionCount = document.getElementById('requisitionCount');

      console.log('Displaying pending requisitions:', requisitions);

      // Filter requisitions with status_id 1 and 2 (Pending Approval and Awaiting Payment)
      const pendingRequisitions = requisitions.filter(req => {
        const statusId = req.form_details.status.id;
        return statusId === 1 || statusId === 2;
      });

      console.log('Filtered pending requisitions:', pendingRequisitions);

      if (pendingRequisitions.length === 0) {
        requisitionList.innerHTML = `
                      <div class="text-center text-muted py-4 small">
                          <i class="bi bi-inbox fs-4"></i>
                          <div class="mt-2">No pending requisitions</div>
                      </div>
                  `;
        requisitionCount.textContent = '0';
        return;
      }

      // Update count
      requisitionCount.textContent = pendingRequisitions.length;

      // Create requisition items list - with clickable items
      // Create requisition items list - with clickable items
      const requisitionsHTML = pendingRequisitions.map(req => {
        const requestId = req.request_id;
        const purpose = req.form_details.purpose;
        const status = req.form_details.status.name;
        const statusClass = status === 'Pending Approval' ? 'status-pending' : 'status-awaiting';
        const fullText = `#${requestId.toString().padStart(4, '0')} (${purpose})`;

        return `
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom clickable-requisition-item small"
               data-request-id="${requestId}"
               style="cursor: pointer; transition: background-color 0.2s;"
               onmouseover="this.style.backgroundColor='#f8f9fa'" 
               onmouseout="this.style.backgroundColor='transparent'"
               title="${fullText}">
              <div class="flex-grow-1 me-3" style="min-width: 0;">
                  <div class="fw-medium text-truncate">${fullText}</div>
              </div>
              <div class="flex-shrink-0">
                  <span class="status-badge ${statusClass}">${status}</span>
              </div>
          </div>
      `;
      }).join('');

      requisitionList.innerHTML = requisitionsHTML;

      // Add click event listeners to all requisition items
      addRequisitionItemClickListeners();

      // Enable Bootstrap tooltips (optional)
      const tooltipTriggerList = [].slice.call(requisitionList.querySelectorAll('[title]'));
      tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

      console.log('Rendered requisition list HTML:', requisitionList.innerHTML);
    }
    function addRequisitionItemClickListeners() {
      const requisitionItems = document.querySelectorAll('.clickable-requisition-item');

      requisitionItems.forEach(item => {
        item.addEventListener('click', function () {
          const requestId = this.getAttribute('data-request-id');
          if (requestId) {
            // Redirect to the requisition view page with the request ID
            window.location.href = `/admin/requisition/${requestId}`;
          }
        });
      });

      console.log(`Added click listeners to ${requisitionItems.length} requisition items`);
    }
  </script>
@endsection