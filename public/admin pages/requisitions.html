<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Manage Requisitions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/admin-styles.css">
  <style>
    /* Add sharp edges to all elements */
    * {
      border-radius: 0 !important;
    }

    /* Exclude admin photo container and status circle */
    .profile-img {
      border-radius: 50% !important;
    }

    .status-indicator {
      border-radius: 50% !important;
    }

    /* Styles for requisition status */
    .status-pending {
      color: orange;
      font-weight: bold;
    }

    .status-approved {
      color: green;
      font-weight: bold;
    }

    .status-rejected {
      color: red;
      font-weight: bold;
    }

    .status-completed {
      color: blue;
      font-weight: bold;
    }

    /* Basic list view styling */
    .list-view .requisition-card {
      width: 100%;
    }

    .list-view .requisition-card .card {
      flex-direction: row;
    }

    .list-view .requisition-card .card-img-top {
      width: 150px;
      /* Adjust as needed */
      height: auto;
      object-fit: cover;
    }

    /* Added for button spacing */
    .requisition-actions {
      display: flex;
      gap: 8px;
      /* Space between buttons */
      flex-wrap: wrap;
      /* Allow buttons to wrap on smaller screens */
    }

    .requisition-actions .btn {
      flex: 1;
      /* Make buttons fill available space evenly */
      min-width: 80px;
      /* Minimum width for buttons */
    }
  </style>
</head>

<body>
  <header id="topbar" class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <img src="../cpu-logo.png" alt="CPU Logo" class="me-2" style="height: 40px;">
      <span class="fw-bold">CPU Facilities and Equipment Management</span>
    </div>
    <div class="d-flex align-items-center">
      <div class="position-relative me-3">
        <div class="dropdown">
          <i class="bi bi-bell fs-4 position-relative" id="notificationIcon" data-bs-toggle="dropdown"
            aria-expanded="false"></i>
          <span class="notification-badge">1</span>
          <ul class="dropdown-menu dropdown-menu-end p-0" id="notificationDropdown" aria-labelledby="notificationIcon">
            <li class="dropdown-header">Notifications</li>
            <li>
              <a href="#" class="notification-item unread d-block" data-notification-id="1">
                <div class="notification-title">New Equipment Request</div>
                <div class="notification-text">John Smith requested a Microphone for March 15, 2024
                </div>
                <div class="notification-time">2 minutes ago</div>
              </a>
            </li>
            <li>
              <a href="#" class="notification-item d-block">
                <div class="notification-title">Booking Approved</div>
                <div class="notification-text">Your equipment request for the sound system has been
                  approved</div>
                <div class="notification-time">3 hours ago</div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider m-0">
            </li>
            <li><a href="#" class="dropdown-item view-all-item text-center">View all notifications</a></li>
          </ul>
        </div>
      </div>
      <div class="dropdown">
        <button class="btn btn-link p-0 text-white" id="dropdownMenuButton" data-bs-toggle="dropdown"
          aria-expanded="false">
          <i class="bi bi-three-dots fs-4"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="adminlogin.html"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </header>
  <div id="layout">
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
          <a class="nav-link" href="dashboard.html">
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
          <a class="nav-link active" href="requisitions.html"> <i class="bi bi-file-earmark-text me-2"></i>
            Requisitions
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="facilities.html">
            <i class="bi bi-building me-2"></i>
            Facilities
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="equipment.html"> <i class="bi bi-tools me-2"></i>
            Equipment
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin-roles-page.html">
            <i class="bi bi-people me-2"></i>
            Admin Roles
          </a>
        </li>
      </ul>
    </nav>

    <main id="main">
      <div class="container-fluid bg-light rounded p-4">
        <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Requisitions</h2>
          </div>
        </div>

        <div class="row mb-3 g-2">
          <div class="col-sm-6 col-md-2 col-lg-2">
            <select id="layoutSelect" class="form-select">
              <option value="grid">Grid Layout</option>
              <option value="list">List Layout</option>
            </select>
          </div>
          <div class="col-sm-6 col-md-2 col-lg-2">
            <select id="statusFilter" class="form-select">
              <option value="all">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div class="col-sm-6 col-md-2 col-lg-2">
            <select id="applicantTypeFilter" class="form-select">
              <option value="all">All Applicant Types</option>
              <option value="internal">Internal</option>
              <option value="external">External</option>
            </select>
          </div>
          <div class="col-sm-6 col-md-2 col-lg-2">
            <select id="purposeFilter" class="form-select">
              <option value="all">All Purposes</option>
              <option value="Academic">Academic</option>
              <option value="Event">Event</option>
              <option value="Research">Research</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-4 col-lg-4">
            <div class="search-container">
              <i class="bi bi-search"></i>
              <input type="text" id="searchInput" class="form-control" placeholder="Search requisition form...">
            </div>
          </div>
        </div>

        <div id="requisitionContainer" class="row g-3">
          <div class="col-md-4 requisition-card" data-id="REQ001" data-status="pending" data-applicant-type="internal"
            data-purpose="Academic" data-title="Request #001 - Class Project">
            <div class="card">
              <div class="card-body d-flex flex-column">
                <div>
                  <h5 class="card-title">Request #001 - Class Project</h5>
                  <p class="card-text text-muted mb-2">
                    <i class="bi bi-person-fill text-primary"></i> Juan Dela Cruz (Individual) |
                    <i class="bi bi-journal-text text-primary"></i> Academic
                  </p>
                  <p class="status-pending">Pending</p>
                  <p class="card-text mb-3">
                    <strong>Date:</strong> 2025-07-20 <br>
                    <strong>Time:</strong> 09:00 AM - 05:00 PM <br>
                    <strong>Items:</strong> Auditorium (1), Projector (1) <br>
                    <strong>Est. Fee:</strong> Php 5,500.00
                  </p>
                </div>
                <div class="requisition-actions mt-auto pt-3 d-flex gap-2">
                  <button class="btn btn-primary btn-approve flex-fill">Approve</button>
                  <button class="btn btn-manage btn-flex flex-fill" data-bs-toggle="modal"
                    data-bs-target="#manageRequisitionModal">Manage</button>
                  <button class="btn btn-outline-danger btn-delete flex-fill">Delete</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4 requisition-card" data-id="REQ002" data-status="approved" data-applicant-type="internal"
            data-purpose="Event" data-title="Request #002 - Dept Meeting">
            <div class="card">
              <div class="card-body d-flex flex-column">
                <div>
                  <h5 class="card-title">Request #002 - Dept Meeting</h5>
                  <p class="card-text text-muted mb-2">
                    <i class="bi bi-buildings-fill text-primary"></i> CCS Department |
                    <i class="bi bi-journal-text text-primary"></i> Event
                  </p>
                  <p class="status-approved">Approved</p>
                  <p class="card-text mb-3">
                    <strong>Date:</strong> 2025-07-22 <br>
                    <strong>Time:</strong> 01:00 PM - 03:00 PM <br>
                    <strong>Items:</strong> Conference Room A (1), Speaker System (1) <br>
                    <strong>Est. Fee:</strong> Php 1,200.00
                  </p>
                </div>
                <div class="requisition-actions mt-auto pt-3 d-flex gap-2">
                  <button class="btn btn-manage btn-flex flex-fill" data-bs-toggle="modal"
                    data-bs-target="#manageRequisitionModal">Manage</button>
                  <button class="btn btn-outline-danger btn-delete flex-fill">Delete</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4 requisition-card" data-id="REQ003" data-status="rejected" data-applicant-type="external"
            data-purpose="Other" data-title="Request #003 - NGO Charity"
            data-rejection-reason="Facility unavailable on requested date. Conflict with prior booking.">
            <div class="card">
              <div class="card-body d-flex flex-column">
                <div>
                  <h5 class="card-title">Request #003 - NGO Charity</h5>
                  <p class="card-text text-muted mb-2">
                    <i class="bi bi-building-fill text-primary"></i> NGO X (Organization) |
                    <i class="bi bi-tags-fill text-primary"></i> Other
                  </p>
                  <p class="status-rejected">Rejected</p>
                  <p class="card-text rejection-reason-display mb-3">
                    <strong>Reason:</strong> Facility unavailable on requested date. Conflict with
                    prior booking.
                  </p>
                  <p class="card-text mb-3">
                    <strong>Date:</strong> 2025-07-25 <br>
                    <strong>Time:</strong> 10:00 AM - 04:00 PM <br>
                    <strong>Items:</strong> University Gym (1), Stage Lights (3) <br>
                    <strong>Est. Fee:</strong> Php 8,000.00
                  </p>
                </div>
                <div class="requisition-actions mt-auto pt-3 d-flex gap-2">
                  <button class="btn btn-manage btn-flex flex-fill" data-bs-toggle="modal"
                    data-bs-target="#manageRequisitionModal">Manage</button>
                  <button class="btn btn-outline-danger btn-delete flex-fill">Delete</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4 requisition-card" data-id="REQ004" data-status="completed" data-applicant-type="internal"
            data-purpose="Research" data-title="Request #004 - Research Project">
            <div class="card">
              <div class="card-body d-flex flex-column">
                <div>
                  <h5 class="card-title">Request #004 - Research Project</h5>
                  <p class="card-text text-muted mb-2">
                    <i class="bi bi-person-fill text-primary"></i> Maria Clara (Individual) |
                    <i class="bi bi-journal-text text-primary"></i> Research
                  </p>
                  <p class="status-completed">Completed</p>
                  <p class="card-text mb-3">
                    <strong>Date:</strong> 2025-07-18 <br>
                    <strong>Time:</strong> 10:00 AM - 12:00 PM <br>
                    <strong>Items:</strong> Lab Room C (1), Microscope (2) <br>
                    <strong>Est. Fee:</strong> Php 2,000.00
                  </p>
                </div>
                <div class="requisition-actions mt-auto pt-3 d-flex gap-2">
                  <button class="btn btn-manage btn-flex flex-fill" data-bs-toggle="modal"
                    data-bs-target="#manageRequisitionModal">Manage</button>
                  <button class="btn btn-outline-danger btn-delete flex-fill">Delete</button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="d-flex justify-content-center mt-4">
          <nav aria-label="Requisition pagination">
            <ul class="pagination">
              <li class="page-item disabled" id="prevPage">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
              </li>
              <li class="page-item" id="nextPage">
                <a class="page-link" href="#" data-page="2">Next</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </main>
  </div>

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

  <div class="modal fade" id="manageRequisitionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Manage Requisition</h5> <button type="button" class="btn-close"
            data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="requisitionManageForm">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="requisitionId" class="form-label">Requisition ID</label>
                <input type="text" class="form-control" id="requisitionId" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label for="applicantName" class="form-label">Applicant Name</label>
                <input type="text" class="form-control" id="applicantName" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="applicantEmail" class="form-label">Applicant Email</label>
                <input type="email" class="form-control" id="applicantEmail" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label for="applicantPhone" class="form-label">Applicant Phone</label>
                <input type="tel" class="form-control" id="applicantPhone" readonly>
              </div>
            </div>
            <div class="mb-3">
              <label for="purpose" class="form-label">Purpose</label>
              <input type="text" class="form-control" id="purpose" readonly>
            </div>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="rentalDate" class="form-label">Rental Date</label>
                <input type="date" class="form-control" id="rentalDate" readonly>
              </div>
              <div class="col-md-4 mb-3">
                <label for="startTime" class="form-label">Start Time</label>
                <input type="time" class="form-control" id="startTime" readonly>
              </div>
              <div class="col-md-4 mb-3">
                <label for="endTime" class="form-label">End Time</label>
                <input type="time" class="form-control" id="endTime" readonly>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Requested Facilities</label>
              <ul id="requestedFacilitiesList" class="list-group">
              </ul>
            </div>
            <div class="mb-3">
              <label class="form-label">Requested Equipment</label>
              <ul id="requestedEquipmentList" class="list-group">
              </ul>
            </div>
            <div class="mb-3">
              <label for="estimatedFee" class="form-label">Estimated Total Fee</label>
              <input type="text" class="form-control" id="estimatedFee" readonly>
            </div>
            <div class="mb-3">
              <label for="requisitionStatus" class="form-label">Status</label>
              <select class="form-select" id="requisitionStatus">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="adminNotes" class="form-label">Admin Notes / Rejection Reason</label>
              <textarea class="form-control" id="adminNotes" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="js/global.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const requisitionContainer = document.getElementById('requisitionContainer');
      const searchInput = document.getElementById('searchInput');
      const layoutSelect = document.getElementById('layoutSelect');
      const statusFilter = document.getElementById('statusFilter');
      const applicantTypeFilter = document.getElementById('applicantTypeFilter');
      const purposeFilter = document.getElementById('purposeFilter');

      // Pagination variables
      const itemsPerPage = 9;
      let currentPage = 1;
      let allRequisitionCards = Array.from(document.querySelectorAll('.requisition-card'));
      const pageLinks = document.querySelectorAll('.page-link[data-page]');
      const prevPageBtn = document.getElementById('prevPage');
      const nextPageBtn = document.getElementById('nextPage');

      // Manage Requisition Modal Elements
      const manageRequisitionModal = new bootstrap.Modal(document.getElementById('manageRequisitionModal'));
      const requisitionManageForm = document.getElementById('requisitionManageForm');
      const modalRequisitionId = document.getElementById('requisitionId');
      const modalApplicantName = document.getElementById('applicantName');
      const modalApplicantEmail = document.getElementById('applicantEmail');
      const modalApplicantPhone = document.getElementById('applicantPhone');
      const modalPurpose = document.getElementById('purpose');
      const modalRentalDate = document.getElementById('rentalDate');
      const modalStartTime = document.getElementById('startTime');
      const modalEndTime = document.getElementById('endTime');
      const modalRequestedFacilitiesList = document.getElementById('requestedFacilitiesList');
      const modalRequestedEquipmentList = document.getElementById('requestedEquipmentList');
      const modalEstimatedFee = document.getElementById('estimatedFee');
      const modalRequisitionStatus = document.getElementById('requisitionStatus');
      const modalAdminNotes = document.getElementById('adminNotes');
      const modalSaveChangesBtn = document.querySelector('#manageRequisitionModal .btn-primary');

      // --- Functions ---

      // Initialize pagination and add event listeners
      function initializePagination() {
        updatePagination();
        showPage(1); // Show first page initially
      }

      // Show a specific page of requisitions
      function showPage(page) {
        currentPage = page;
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        const visibleCards = getFilteredRequisitions();

        // Hide all cards first
        allRequisitionCards.forEach(card => {
          card.style.display = 'none';
        });

        // Show cards for current page
        for (let i = startIndex; i < endIndex && i < visibleCards.length; i++) {
          visibleCards[i].style.display = 'block';
        }

        updatePagination();
        attachRequisitionActionListeners(); // Re-attach listeners after showing/hiding cards
      }

      // Update pagination controls (active state, disabled buttons)
      function updatePagination() {
        const totalPages = Math.ceil(getFilteredRequisitions().length / itemsPerPage);

        document.querySelectorAll('.pagination .page-item-number').forEach(item => {
          item.remove(); // Remove old numbers
        });

        const prevItem = document.getElementById('prevPage');
        const nextItem = document.getElementById('nextPage');

        for (let i = 1; i <= totalPages; i++) {
          const li = document.createElement('li');
          li.className = `page-item page-item-number ${i === currentPage ? 'active' : ''}`;
          li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
          nextItem.before(li); // Insert before next button
        }

        // Update page links after adding new ones
        const currentTotalPageLinks = document.querySelectorAll('.page-link[data-page]');
        currentTotalPageLinks.forEach(link => {
          link.addEventListener('click', function (e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute('data-page'));
            showPage(page);
          });
        });

        prevPageBtn.classList.toggle('disabled', currentPage === 1);
        nextPageBtn.classList.toggle('disabled', currentPage === totalPages || totalPages === 0);
      }

      // Get filtered requisitions based on search and filters
      function getFilteredRequisitions() {
        const searchTerm = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        const applicantType = applicantTypeFilter.value;
        const purpose = purposeFilter.value;

        return allRequisitionCards.filter(card => {
          const cardStatus = card.dataset.status;
          const cardApplicantType = card.dataset.applicantType;
          const cardPurpose = card.dataset.purpose;
          const cardTitle = card.dataset.title.toLowerCase();
          const cardText = card.querySelector('.card-text').textContent.toLowerCase();

          const matchesSearch = cardTitle.includes(searchTerm) || cardText.includes(searchTerm);
          const matchesStatus = (status === 'all' || cardStatus === status);
          const matchesApplicantType = (applicantType === 'all' || cardApplicantType === applicantType);
          const matchesPurpose = (purpose === 'all' || cardPurpose === purpose);

          return matchesSearch && matchesStatus && matchesApplicantType && matchesPurpose;
        });
      }

      // Filter requisitions based on all criteria and apply layout
      function filterRequisitions() {
        const layout = layoutSelect.value;
        requisitionContainer.className = `row g-3 ${layout === 'list' ? 'list-view' : ''}`;
        showPage(1); // Reset to first page after filtering
      }

      // --- Event Listeners for Filters and Search ---
      [searchInput, layoutSelect, statusFilter, applicantTypeFilter, purposeFilter].forEach(control => {
        control.addEventListener('change', filterRequisitions);
      });
      searchInput.addEventListener('input', filterRequisitions);

      // --- Dynamic Button Attachment and Action Listeners ---
      function attachRequisitionActionListeners() {
        // IMPORTANT: Re-select all cards as their visibility might have changed
        const currentVisibleCards = document.querySelectorAll('.requisition-card[style*="block"]');

        // Remove existing listeners to prevent duplicates from previous calls
        document.querySelectorAll('.btn-approve').forEach(btn => btn.removeEventListener('click', handleApprove));
        document.querySelectorAll('.btn-delete').forEach(btn => btn.removeEventListener('click', handleDelete));
        document.querySelectorAll('.btn-manage').forEach(btn => btn.removeEventListener('click', handleManage));


        currentVisibleCards.forEach(card => {
          const actionsDiv = card.querySelector('.requisition-actions');
          const currentStatus = card.dataset.status;

          // --- Approve Button Logic ---
          let approveBtn = actionsDiv.querySelector('.btn-approve');
          if (currentStatus === 'pending') {
            if (!approveBtn) { // If button doesn't exist, create it
              approveBtn = document.createElement('button');
              approveBtn.classList.add('btn', 'btn-primary', 'btn-approve', 'flex-fill');
              approveBtn.textContent = 'Approve';
              const manageBtn = actionsDiv.querySelector('.btn-manage');
              if (manageBtn) {
                actionsDiv.insertBefore(approveBtn, manageBtn);
              } else {
                actionsDiv.prepend(approveBtn);
              }
            }
            approveBtn.addEventListener('click', handleApprove);
          } else {
            if (approveBtn) {
              approveBtn.remove(); // Remove if status is not pending
            }
          }

          // --- Re-attach Listeners for Manage and Delete ---
          const deleteBtn = actionsDiv.querySelector('.btn-delete');
          if (deleteBtn) deleteBtn.addEventListener('click', handleDelete);

          const manageBtn = actionsDiv.querySelector('.btn-manage');
          if (manageBtn) manageBtn.addEventListener('click', handleManage);
        });
      }


      // --- Action Handlers ---

      async function handleApprove(e) {
        const card = e.target.closest('.requisition-card');
        const requisitionId = card.dataset.id;

        if (confirm(`Are you sure you want to approve Requisition ${requisitionId}?`)) {
          try {
            // --- Backend Integration (Simulated) ---
            // In a real Laravel app, you'd send a PUT/PATCH request to your API:
            // const response = await fetch(`/api/requisitions/${requisitionId}`, {
            //     method: 'PUT',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         // 'X-CSRF-TOKEN': 'your-csrf-token' // If using Laravel Blade
            //     },
            //     body: JSON.stringify({ status: 'approved' })
            // });
            // const data = await response.json();
            // if (!response.ok) throw new Error(data.message || 'Failed to approve');

            console.log(`Approving requisition ${requisitionId}...`);
            await new Promise(resolve => setTimeout(resolve, 500)); // Simulate network delay

            // --- UI Update on Success ---
            card.dataset.status = 'approved';
            const statusParagraph = card.querySelector('.status-pending, .status-approved, .status-rejected, .status-completed');
            if (statusParagraph) {
              statusParagraph.className = 'status-approved';
              statusParagraph.textContent = 'Approved';
            }
            // Remove the approve button after approval
            e.target.remove();
            // Remove rejection reason display if it exists (in case it was previously rejected)
            const rejectionReasonDisplay = card.querySelector('.rejection-reason-display');
            if (rejectionReasonDisplay) rejectionReasonDisplay.remove();
            card.removeAttribute('data-rejection-reason');

            alert(`Requisition ${requisitionId} approved successfully!`);
            filterRequisitions(); // Re-apply filters to update button visibility
          } catch (error) {
            console.error('Error approving requisition:', error);
            alert('Failed to approve requisition. Please try again.');
          }
        }
      }

      async function handleDelete(e) {
        const card = e.target.closest('.requisition-card');
        const requisitionId = card.dataset.id;

        if (confirm(`Are you sure you want to delete Requisition ${requisitionId}? This action cannot be undone.`)) {
          try {
            // --- Backend Integration (Simulated) ---
            // In a real Laravel app, you'd send a DELETE request to your API:
            // const response = await fetch(`/api/requisitions/${requisitionId}`, {
            //     method: 'DELETE',
            //     // headers: { 'X-CSRF-TOKEN': 'your-csrf-token' } // If using Laravel Blade
            // });
            // const data = await response.json();
            // if (!response.ok) throw new Error(data.message || 'Failed to delete');

            console.log(`Deleting requisition ${requisitionId}...`);
            await new Promise(resolve => setTimeout(resolve, 500)); // Simulate network delay

            // --- UI Update on Success ---
            card.remove(); // Remove the card from the DOM
            allRequisitionCards = Array.from(document.querySelectorAll('.requisition-card')); // Update reference
            alert(`Requisition ${requisitionId} deleted successfully!`);
            filterRequisitions(); // Re-apply filters and update pagination
          } catch (error) {
            console.error('Error deleting requisition:', error);
            alert('Failed to delete requisition. Please try again.');
          }
        }
      }

      function handleManage(e) {
        const card = e.target.closest('.requisition-card');
        const requisitionId = card.dataset.id;
        const currentStatus = card.dataset.status;
        const rejectionReason = card.dataset.rejectionReason || '';

        // Populate modal fields
        modalRequisitionId.value = requisitionId;
        // These applicant details are hardcoded from HTML, for a real app, you'd fetch them or pass more data
        modalApplicantName.value = card.querySelector('i.bi-person-fill, i.bi-buildings-fill').nextSibling.textContent.split('|')[0].trim();
        modalApplicantEmail.value = 'dummy.email@example.com'; // Placeholder
        modalApplicantPhone.value = '09XX-XXX-XXXX'; // Placeholder
        modalPurpose.value = card.dataset.purpose;
        modalRentalDate.value = card.querySelector('strong:contains("Date:")').nextSibling.textContent.trim();
        modalStartTime.value = card.querySelector('strong:contains("Time:")').nextSibling.textContent.split('-')[0].trim();
        modalEndTime.value = card.querySelector('strong:contains("Time:")').nextSibling.textContent.split('-')[1].trim();
        modalEstimatedFee.value = card.querySelector('strong:contains("Est. Fee:")').nextSibling.textContent.trim();
        modalRequisitionStatus.value = currentStatus; // Set current status
        modalAdminNotes.value = rejectionReason; // Populate with rejection reason if exists

        // Clear and populate Facilities and Equipment lists
        modalRequestedFacilitiesList.innerHTML = '';
        modalRequestedEquipmentList.innerHTML = '';
        const itemsText = card.querySelector('strong:contains("Items:")').nextSibling.textContent.trim();
        if (itemsText) {
          itemsText.split(',').forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = item.trim();
            // Simple heuristic for demo: distinguish based on keywords
            if (item.toLowerCase().includes('auditorium') || item.toLowerCase().includes('room') || item.toLowerCase().includes('gym') || item.toLowerCase().includes('hall')) {
              modalRequestedFacilitiesList.appendChild(li);
            } else {
              modalRequestedEquipmentList.appendChild(li);
            }
          });
        }

        // Store the requisition ID on the save button for easy access
        modalSaveChangesBtn.dataset.cardId = requisitionId;
      }

      async function handleSaveChanges() {
        const requisitionId = modalSaveChangesBtn.dataset.cardId;
        const newStatus = modalRequisitionStatus.value;
        const adminNotes = modalAdminNotes.value.trim();

        if (!requisitionId) {
          alert('Error: Requisition ID not found for saving changes.');
          return;
        }

        try {
          // --- Backend Integration (Simulated) ---
          // In a real Laravel app, you'd send a PUT/PATCH request to your API:
          // const response = await fetch(`/api/requisitions/${requisitionId}`, {
          //     method: 'PUT', // Or PATCH
          //     headers: {
          //         'Content-Type': 'application/json',
          //         // 'X-CSRF-TOKEN': 'your-csrf-token' // If using Laravel Blade
          //     },
          //     body: JSON.stringify({ status: newStatus, admin_notes: adminNotes })
          // });
          // const data = await response.json();
          // if (!response.ok) throw new Error(data.message || 'Failed to save changes');

          console.log(`Saving changes for requisition ${requisitionId}: Status=${newStatus}, Notes=${adminNotes}`);
          await new Promise(resolve => setTimeout(resolve, 700)); // Simulate network delay

          // --- UI Update on Success ---
          const cardToUpdate = document.querySelector(`.requisition-card[data-id="${requisitionId}"]`);
          if (cardToUpdate) {
            cardToUpdate.dataset.status = newStatus;
            const statusParagraph = cardToUpdate.querySelector('.status-pending, .status-approved, .status-rejected, .status-completed');
            if (statusParagraph) {
              statusParagraph.className = `status-${newStatus}`;
              statusParagraph.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1); // Capitalize first letter
            }

            // Update rejection reason display on card if status is rejected
            let rejectionReasonDisplay = cardToUpdate.querySelector('.rejection-reason-display');
            if (newStatus === 'rejected') {
              cardToUpdate.dataset.rejectionReason = adminNotes; // Save notes as rejection reason
              if (!rejectionReasonDisplay) {
                rejectionReasonDisplay = document.createElement('p');
                rejectionReasonDisplay.classList.add('card-text', 'rejection-reason-display', 'mb-3');
                // Insert before the original date/time/items text block
                const originalDetails = cardToUpdate.querySelector('.card-text.mb-3:not(.rejection-reason-display):not(.status-pending):not(.status-approved):not(.status-rejected):not(.status-completed)');
                if (originalDetails) {
                  originalDetails.before(rejectionReasonDisplay);
                } else {
                  cardToUpdate.querySelector('.card-body div:first-child').appendChild(rejectionReasonDisplay);
                }
              }
              rejectionReasonDisplay.innerHTML = `<strong>Reason:</strong> ${adminNotes}`;
            } else {
              cardToUpdate.removeAttribute('data-rejection-reason');
              if (rejectionReasonDisplay) {
                rejectionReasonDisplay.remove(); 
              }
            }
          }

          alert(`Requisition ${requisitionId} updated successfully to ${newStatus}!`);
          manageRequisitionModal.hide(); 
          filterRequisitions();
        } catch (error) {
          console.error('Error saving changes:', error);
          alert('Failed to save changes. Please try again.');
        }
      }

      // --- Global Listeners ---
      prevPageBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (!this.classList.contains('disabled')) {
          showPage(currentPage - 1);
        }
      });

      nextPageBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (!this.classList.contains('disabled')) {
          showPage(currentPage + 1);
        }
      });

      modalSaveChangesBtn.addEventListener('click', handleSaveChanges);


      // Initial setup
      initializePagination();
      filterRequisitions(); 
    });
  </script>
</body>

</html>