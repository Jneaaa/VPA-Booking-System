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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet">
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

    #miniCalendar {
      height: 300px;
      background-color: var(--light-gray);
      border: 1px solid #e0e0e0;
    }

    #showAllButton {
      margin-top: 0;
      align-self: center;
    }

    /* Bootstrap datepicker overrides */
    .datepicker {
      padding: 0;
      border: none;
    }

    .datepicker table {
      width: 100%;
    }

    .datepicker .datepicker-days table {
      margin: 0;
    }

    .datepicker-dropdown {
      box-shadow: none;
      border: 1px solid #ddd;
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
          <i class="bi bi-bell fs-4 position-relative" id="notificationIcon" data-bs-toggle="dropdown"
            aria-expanded="false"></i>
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
            <li>
              <hr class="dropdown-divider m-0">
            </li>
            <li><a href="#" class="dropdown-item view-all-item text-center">View all notifications</a></li>
          </ul>
        </div>
      </div>
      <!-- Dropdown Menu -->
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
          <a class="nav-link" href="dashboard.html">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="calendar.html">
            <i class="bi bi-calendar-event me-2"></i>
            Calendar
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="requisitions.html">
            <i class="bi bi-file-earmark-text me-2"></i>
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
          <a class="nav-link" href="equipment.html">
            <i class="bi bi-tools me-2"></i>
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
    <!-- Calendar Section -->
    <main id="main" style="padding: 1rem;">
      <section class="d-flex" style="height: calc(100vh - 60px);">
        <!-- Left Section: Mini Calendar and Event List -->
        <div class="me-4" style="width: 300px; flex-shrink: 0;"> <!-- Increased width -->

          <div id="miniCalendar" class="td-mini-calendar"></div>


          <div style="height: 50%; overflow-y: auto; margin-top: 20px;"> <!-- Added margin-top -->
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="fw-bold">Event Filter</h5>
              <button class="btn btn-sm btn-outline-primary" id="showAllButton">Show All</button>
            </div>
            <ul class="list-group">
              <li class="list-group-item text-warning d-flex justify-content-between align-items-center"
                data-filter="university">
                <span><i class="bi bi-calendar-event me-2"></i> CPU Event</span>
                <input type="checkbox" class="form-check-input filter-checkbox" data-filter="university">
              </li>
              <li class="list-group-item text-primary d-flex justify-content-between align-items-center"
                data-filter="facility">
                <span><i class="bi bi-calendar-event me-2"></i> Facility Rental</span>
                <input type="checkbox" class="form-check-input filter-checkbox" data-filter="facility">
              </li>
              <li class="list-group-item text-primary d-flex justify-content-between align-items-center"
                data-filter="equipment">
                <span><i class="bi bi-calendar-event me-2"></i> Equipment Rental</span>
                <input type="checkbox" class="form-check-input filter-checkbox" data-filter="equipment">
              </li>
              <li class="list-group-item text-secondary d-flex justify-content-between align-items-center"
                data-filter="external">
                <span><i class="bi bi-calendar-event me-2"></i> External Conference</span>
                <input type="checkbox" class="form-check-input filter-checkbox" data-filter="external">
              </li>
            </ul>
          </div>

        </div>
        <!-- Right Section: Events Calendar -->
        <div style="flex-grow: 1; height: 100%;"> <!-- Reduced flex-grow -->
          <div id="calendar" class="border p-2 calendar-container" style="height: 100%;"></div>
        </div>
      </section>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="js/global.js"></script>
  <script src="js/calendar.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Initialize Mini Calendar with Bootstrap Datepicker
      $('#miniCalendar').datepicker({
        format: "mm/dd/yyyy",
        todayHighlight: true,
        weekStart: 0, // Sunday
        autoclose: true
      }).on('changeDate', function (e) {
        // When a date is selected, switch the main calendar to day view
        if (typeof calendar !== 'undefined') {
          calendar.changeView('timeGridDay', e.date);
        }
      });

      // Show the calendar immediately
      $('#miniCalendar').datepicker('show');
    });
  </script>
</body>

</html>