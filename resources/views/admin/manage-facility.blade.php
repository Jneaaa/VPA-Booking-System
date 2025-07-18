<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Facility | CPU Admin</title>

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

    .status-indicator {
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
          <span class="status-indicator"></span>
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
          <a class="nav-link" href="requisitions.html">
            <i class="bi bi-file-earmark-text me-2"></i>
            Requisitions
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="facilities.html">
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
      <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h1 class="h3 mb-0 text-gray-800">Manage Facility</h1>
          </div>
          <a href="facilities.html" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Facilities
          </a>
        </div>

        <!-- Facility Overview Card -->
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Facility Overview</h6>
            <div>
              <button class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-printer me-1"></i>Print
              </button>
              <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Delete Facility
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- Facility Images Carousel -->
              <div class="col-md-5">
                <div id="facilityCarousel" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-indicators">
                    <button type="button" data-bs-target="#facilityCarousel" data-bs-slide-to="0"
                      class="active"></button>
                    <button type="button" data-bs-target="#facilityCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#facilityCarousel" data-bs-slide-to="2"></button>
                  </div>
                  <div class="carousel-inner rounded">
                    <div class="carousel-item active">
                      <img src="assets/volleyball-court.jpg" class="d-block w-100" alt="Facility Image 1">
                    </div>
                    <div class="carousel-item">
                      <img src="assets/volleyball-court-2.jpg" class="d-block w-100" alt="Facility Image 2">
                    </div>
                    <div class="carousel-item">
                      <img src="assets/volleyball-court-3.jpg" class="d-block w-100" alt="Facility Image 3">
                    </div>
                  </div>
                  <button class="carousel-control-prev" type="button" data-bs-target="#facilityCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#facilityCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                </div>
              </div>

              <!-- Facility Details -->
              <div class="col-md-7">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <h3>Volleyball Court</h3>
                  <span class="badge bg-success fs-6">Available</span>
                </div>
                <div class="mb-4">
                  <p class="text-muted mb-3">
                    <i class="bi bi-tag-fill text-primary"></i> Sports Facility |
                    <i class="bi bi-diagram-2-fill text-primary"></i> Court |
                    <i class="bi bi-building-fill text-primary"></i> CBA
                  </p>
                  <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-fill text-primary me-2"></i>
                    <span class="text-muted">Created by: John Doe on January 15, 2024</span>
                  </div>
                  <p class="mb-0">
                    Indoor volleyball court located inside the University Gym, featuring a standard playing surface,
                    regulation net, and ample space for training, classes, or recreational games. Bleacher seating
                    available for spectators.
                  </p>
                </div>
                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                      <span>University Gym, 2nd Floor</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-people-fill text-primary me-2"></i>
                      <span>Capacity: 50 people</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-cash-coin text-primary me-2"></i>
                      <span>Rental Fee: ₱1,500.00 per hour</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-house-door-fill text-primary me-2"></i>
                      <span>Indoor Facility</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-calendar-check-fill text-primary me-2"></i>
                      <span>Bookable: Yes</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                      <i class="bi bi-building text-primary me-2"></i>
                      <span>Building Code: LHB</span>
                    </div>
                  </div>
                </div>


                <div class="d-flex gap-2">
                  <button class="btn btn-primary">
                    <i class="bi bi-pencil-square me-1"></i>Edit Facility
                  </button>
                  <button class="btn btn-secondary">
                    <i class="bi bi-calendar-plus me-1"></i>View Bookings
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" id="facilityTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="amenities-tab" data-bs-toggle="tab" data-bs-target="#amenities"
              type="button" role="tab">
              <i class="bi bi-list-check me-1"></i>Amenities
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment" type="button"
              role="tab">
              <i class="bi bi-tools me-1"></i>Equipment
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button"
              role="tab">
              <i class="bi bi-calendar-event me-1"></i>Upcoming Bookings
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button"
              role="tab">
              <i class="bi bi-clock-history me-1"></i>Usage History
            </button>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="facilityTabContent">
          <!-- Amenities Tab -->
          <div class="tab-pane fade show active" id="amenities" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Facility Amenities</h6>
                <button class="btn btn-sm btn-primary">
                  <i class="bi bi-plus me-1"></i>Add Amenity
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Amenity</th>
                        <th>Quantity</th>
                        <th>Included in Fee</th>
                        <th>Additional Cost</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Volleyball Net</td>
                        <td>1</td>
                        <td><i class="bi bi-check-circle-fill text-success"></i></td>
                        <td>₱0.00</td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td>Bleacher Seating</td>
                        <td>4 sections</td>
                        <td><i class="bi bi-check-circle-fill text-success"></i></td>
                        <td>₱0.00</td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td>Scoreboard</td>
                        <td>1</td>
                        <td><i class="bi bi-x-circle-fill text-danger"></i></td>
                        <td>₱200.00</td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Equipment Tab -->
          <div class="tab-pane fade" id="equipment" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Assigned Equipment</h6>
                <button class="btn btn-sm btn-primary">
                  <i class="bi bi-plus me-1"></i>Assign Equipment
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Equipment</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Rental Fee</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Volleyball Set (6 balls)</td>
                        <td>Sports Equipment</td>
                        <td><span class="badge bg-success">Available</span></td>
                        <td>₱300.00</td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td>Whistle Set</td>
                        <td>Sports Equipment</td>
                        <td><span class="badge bg-success">Available</span></td>
                        <td>₱50.00</td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Bookings Tab -->
          <div class="tab-pane fade" id="bookings" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Upcoming Bookings</h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Requestor</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>March 15, 2024</td>
                        <td>9:00 AM - 11:00 AM</td>
                        <td>John Smith (CBA Faculty)</td>
                        <td>PE Class - Volleyball</td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td>March 18, 2024</td>
                        <td>2:00 PM - 5:00 PM</td>
                        <td>CPU Volleyball Team</td>
                        <td>Team Practice</td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td>March 20, 2024</td>
                        <td>8:00 AM - 12:00 PM</td>
                        <td>Jane Doe (Student Org)</td>
                        <td>Intramurals Tournament</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-check"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-x"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- History Tab -->
          <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Usage History</h6>
                <div class="d-flex">
                  <input type="date" class="form-control form-control-sm me-2" style="width: 150px;">
                  <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-filter me-1"></i>Filter
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Requestor</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Revenue</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>March 10, 2024</td>
                        <td>1:00 PM - 3:00 PM</td>
                        <td>Robert Johnson</td>
                        <td>Faculty Sports Day</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>₱3,000.00</td>
                      </tr>
                      <tr>
                        <td>March 8, 2024</td>
                        <td>9:00 AM - 12:00 PM</td>
                        <td>Sarah Williams</td>
                        <td>PE Class</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>₱4,500.00</td>
                      </tr>
                      <tr>
                        <td>March 5, 2024</td>
                        <td>2:00 PM - 4:00 PM</td>
                        <td>CPU Alumni</td>
                        <td>Alumni Tournament</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>₱3,000.00</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Combined JS resources -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="js/global.js"></script>
</body>

</html>