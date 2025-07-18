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

    /* Exclude admin photo container and status circle */
    .profile-img {
      border-radius: 50% !important;
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
          <li><a class="dropdown-item" href="adminlogin.html" id="logoutLink"><i
                class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
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
    <!-- Main Content -->
    <main id="main">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Your Dashboard</h2>
        <a href="#" class="btn btn-primary">
          <i class="bi bi-gear me-1"></i> Manage Requests
        </a>
      </div>
      <!-- Stats Cards -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-muted">Total Requisitions</h5>
                  <p class="card-text fs-3 fw-bold">245</p>
                </div>
                <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-muted">Approved Bookings</h5>
                  <p class="card-text fs-3 fw-bold">112</p>
                </div>
                <i class="bi bi-check-circle fs-1 text-success"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stat-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title text-muted">Pending Requests</h5>
                  <p class="card-text fs-3 fw-bold">16</p>
                </div>
                <i class="bi bi-hourglass-split fs-1 text-warning"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Calendar Section -->
      <section>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="fw-bold">Events Calendar</h3>
          <div class="d-flex gap-2">
            <div class="dropdown">
              <button class="btn btn-secondary dropdown-toggle" type="button" id="eventTypeDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-funnel me-1"></i> Filter Events
              </button>
              <ul class="dropdown-menu" aria-labelledby="eventTypeDropdown">
                <li><button class="dropdown-item" data-filter="facility"><i class="bi bi-door-open me-2"></i>Facility
                    Rentals</button></li>
                <li><button class="dropdown-item" data-filter="equipment"><i class="bi bi-tools me-2"></i>Equipment
                    Rentals</button></li>
                <li><button class="dropdown-item" data-filter="university"><i class="bi bi-building me-2"></i>University
                    Events</button></li>
                <li><button class="dropdown-item" data-filter="external"><i class="bi bi-globe me-2"></i>External
                    Events</button></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><button class="dropdown-item" data-filter="all"><i class="bi bi-eye me-2"></i>Show All</button></li>
              </ul>
            </div>
            <a href="calendar.html" class="btn btn-primary">
              <i class="bi bi-calendar-week me-1"></i> Open Calendar
            </a>
          </div>
        </div>
        <div id="calendar" class="border rounded p-3 calendar-container" style="height: 600px;">
          <!-- Set height to 600px -->
        </div>
      </section>
      <!-- System Log Section -->
      <section class="mt-5">
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
                <li><button class="dropdown-item" data-filter="assistant-admin">Assistant Admin</button></li>
                <li><button class="dropdown-item" data-filter="all">Show All</button></li>
              </ul>
            </div>
            <input type="date" class="form-control" id="logDateFilter" placeholder="Filter by Date">
          </div>
        </div>
        <div id="systemLog" class="border rounded p-3 log-container">
          <ul class="list-group">
            <!-- Example log entries -->
            <li class="list-group-item">
              <strong>John Doe</strong> (Head Admin) approved a requisition for the Main Auditorium on <em>March 15,
                2024</em>.
            </li>
            <li class="list-group-item">
              <strong>Jane Smith</strong> (Assistant Admin) added new equipment: Sound System on <em>March 10,
                2024</em>.
            </li>
            <li class="list-group-item">
              <strong>John Doe</strong> (Head Admin) deleted a facility: Old Gymnasium on <em>March 5, 2024</em>.
            </li>
          </ul>
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
  <script src="js/global.js"></script>
  <script src="js/calendar.js"> </script>
  <script src="js/authentication.js"></script>
</body>

</html>