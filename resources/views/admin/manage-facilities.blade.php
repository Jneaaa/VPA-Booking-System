<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Dashboard - Manage Facilities</title>
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
        <div class="container-fluid bg-light rounded p-4">
      <div class="container-fluid">
        <!-- Header & Controls -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Facilities</h2>
            <div>
                <a href="add-facility.html" class="btn btn-primary">
                    <i class="bi bi-plus-circle-fill me-2"></i>Add New Facility
                </a>
             </div>
        </div>

       <!-- Filters & Search Bar -->
<div class="row mb-3 g-2">  <!-- Changed gutter to g-2 (smaller gap between columns) -->
    <div class="col-sm-6 col-md-2 col-lg-2">  <!-- Added responsive breakpoints -->
        <select id="layoutSelect" class="form-select">
            <option value="grid">Grid Layout</option>
            <option value="list">List Layout</option>
        </select>
    </div>
    <div class="col-sm-6 col-md-2 col-lg-2">
        <select id="statusFilter" class="form-select">
            <option value="all">All Statuses</option>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
            <option value="reserved">Reserved</option>
        </select>
    </div>
    <div class="col-sm-6 col-md-2 col-lg-2">
        <select id="departmentFilter" class="form-select">
            <option value="all">All Departments</option>
            <option value="CCS">CCS Department</option>
            <option value="CBA">CBA Department</option>
            <option value="CAS">CAS Department</option>
        </select>
    </div>
    <div class="col-sm-6 col-md-2 col-lg-2">
        <select id="categoryFilter" class="form-select">
            <option value="all">All Categories</option>
            <option value="Sports">Sports Venues</option>
            <option value="Rooms">Rooms</option>
            <option value="Buildings">Buildings</option>
        </select>
    </div>
    <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="search-container">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Search Facilities...">
        </div>
    </div>
</div>

        <!-- Facilities List -->
        <div id="facilityContainer" class="row g-3">
            <!-- Volleyball Court -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CBA" data-category="Sports" data-title="Volleyball Court">
                <div class="card">
                    <img src="assets/volleyball-court.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Volleyball Court</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Sports Facility | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Court | 
                                <i class="bi bi-building-fill text-primary"></i> CBA
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Indoor volleyball court located inside the University Gym, featuring a standard playing surface, regulation net, and ample space for training, classes, or recreational games. Bleacher seating available for spectators.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex" onclick="window.location.href='manage-facility.html';">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conference Room -->
            <div class="col-md-4 facility-card" data-status="reserved" data-department="CCS" data-category="Rooms" data-title="Conference Room (2nd Floor)">
                <div class="card">
                    <img src="assets/conference-room.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Conference Room (2nd Floor)</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Conference | 
                                <i class="bi bi-building-fill text-primary"></i> CCS
                            </p>
                            <p class="status-reserved">Reserved</p>
                            <p class="card-text mb-3">A formal meeting space equipped with tables, chairs, projector, and whiteboard. Ideal for seminars, presentations, academic discussions, and organizational meetings. Located in Henrilous Library, 2nd Floor.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Church -->
            <div class="col-md-4 facility-card" data-status="unavailable" data-department="CAS" data-category="Buildings" data-title="University Church">
                <div class="card">
                    <img src="assets/uni-church.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">University Church</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Building | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Church | 
                                <i class="bi bi-building-fill text-primary"></i> CAS
                            </p>
                            <p class="status-unavailable">Unavailable</p>
                            <p class="card-text mb-3">A spacious, multi-purpose venue within campus grounds used for religious services, ceremonies, and university events. Equipped with audio systems, seating, and a peaceful atmosphere suitable for gatherings and reflections.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Facility Cards (9 more to make 12 total) -->
            <!-- Basketball Court -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CBA" data-category="Sports" data-title="Basketball Court">
                <div class="card">
                    <img src="assets/basketball-court.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Basketball Court</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Sports Facility | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Court | 
                                <i class="bi bi-building-fill text-primary"></i> CBA
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Outdoor basketball court with hardwood flooring, regulation hoops, and scoreboard. Suitable for official games and practice sessions. Located near the University Gymnasium.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Computer Lab 1 -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CCS" data-category="Rooms" data-title="Computer Lab 1">
                <div class="card">
                    <img src="assets/computer-lab.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Computer Lab 1</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Laboratory | 
                                <i class="bi bi-building-fill text-primary"></i> CCS
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Modern computer laboratory equipped with 30 high-performance workstations, projector, and whiteboard. Ideal for programming classes, research, and workshops. Located in CCS Building, Room 201.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audio Visual Room -->
            <div class="col-md-4 facility-card" data-status="reserved" data-department="CAS" data-category="Rooms" data-title="Audio Visual Room">
                <div class="card">
                    <img src="assets/av-room.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Audio Visual Room</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Media | 
                                <i class="bi bi-building-fill text-primary"></i> CAS
                            </p>
                            <p class="status-reserved">Reserved</p>
                            <p class="card-text mb-3">Specialized room with high-quality audio and video equipment, including projector, sound system, and theater-style seating. Perfect for film screenings, presentations, and multimedia classes. Capacity: 50 persons.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tennis Court -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CBA" data-category="Sports" data-title="Tennis Court">
                <div class="card">
                    <img src="assets/tennis-court.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Tennis Court</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Sports Facility | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Court | 
                                <i class="bi bi-building-fill text-primary"></i> CBA
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Outdoor tennis court with synthetic grass surface and professional net. Available for classes, training, and recreational use. Equipment can be rented from the Sports Office.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faculty Lounge -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CCS" data-category="Rooms" data-title="Faculty Lounge">
                <div class="card">
                    <img src="assets/faculty-lounge.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Faculty Lounge</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Lounge | 
                                <i class="bi bi-building-fill text-primary"></i> CCS
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Comfortable lounge area for faculty members, equipped with sofas, tables, coffee machine, and refrigerator. Ideal for meetings, breaks, and informal discussions. Located on the 3rd floor of the CCS Building.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Science Laboratory -->
            <div class="col-md-4 facility-card" data-status="unavailable" data-department="CAS" data-category="Rooms" data-title="Science Laboratory">
                <div class="card">
                    <img src="assets/science-lab.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Science Laboratory</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Laboratory | 
                                <i class="bi bi-building-fill text-primary"></i> CAS
                            </p>
                            <p class="status-unavailable">Unavailable</p>
                            <p class="card-text mb-3">Fully equipped science laboratory for chemistry and biology experiments. Contains fume hoods, microscopes, and all necessary equipment for undergraduate research and classes. Under maintenance until next week.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Swimming Pool -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CBA" data-category="Sports" data-title="Swimming Pool">
                <div class="card">
                    <img src="assets/swimming-pool.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Swimming Pool</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Sports Facility | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Pool | 
                                <i class="bi bi-building-fill text-primary"></i> CBA
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Olympic-sized swimming pool with 8 lanes, diving boards, and spectator seating. Used for physical education classes, swimming competitions, and recreational swimming. Lifeguard on duty during open hours.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Library Study Room -->
            <div class="col-md-4 facility-card" data-status="reserved" data-department="CCS" data-category="Rooms" data-title="Library Study Room">
                <div class="card">
                    <img src="assets/study-room.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Library Study Room</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Study | 
                                <i class="bi bi-building-fill text-primary"></i> CCS
                            </p>
                            <p class="status-reserved">Reserved</p>
                            <p class="card-text mb-3">Quiet study room in the library with tables, chairs, and power outlets. Ideal for group study sessions or individual work. Capacity: 8 persons. Reservations required during exam periods.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amphitheater -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CAS" data-category="Buildings" data-title="Amphitheater">
                <div class="card">
                    <img src="assets/amphitheater.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Amphitheater</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Building | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Outdoor | 
                                <i class="bi bi-building-fill text-primary"></i> CAS
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Open-air amphitheater with stone seating for 200 people. Used for outdoor performances, lectures, and special events. Equipped with basic sound system and lighting for evening events.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Music Room -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CBA" data-category="Rooms" data-title="Music Room">
                <div class="card">
                    <img src="assets/music-room.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Music Room</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Arts | 
                                <i class="bi bi-building-fill text-primary"></i> CBA
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Soundproof room equipped with piano, music stands, and audio equipment. Used for music classes, choir practice, and individual rehearsals. Instruments available for checkout.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lecture Hall A -->
            <div class="col-md-4 facility-card" data-status="reserved" data-department="CCS" data-category="Rooms" data-title="Lecture Hall A">
                <div class="card">
                    <img src="assets/lecture-hall.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Lecture Hall A</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Lecture | 
                                <i class="bi bi-building-fill text-primary"></i> CCS
                            </p>
                            <p class="status-reserved">Reserved</p>
                            <p class="card-text mb-3">Large lecture hall with tiered seating for 120 students. Equipped with projector, sound system, and multiple whiteboards. Ideal for large classes, guest lectures, and departmental meetings.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Art Studio -->
            <div class="col-md-4 facility-card" data-status="available" data-department="CAS" data-category="Rooms" data-title="Art Studio">
                <div class="card">
                    <img src="assets/art-studio.jpg" class="card-img-top" alt="Facility Cover">
                    <div class="card-body d-flex flex-column">
                        <div>
                            <h5 class="card-title">Art Studio</h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-tag-fill text-primary"></i> Room | 
                                <i class="bi bi-diagram-2-fill text-primary"></i> Studio | 
                                <i class="bi bi-building-fill text-primary"></i> CAS
                            </p>
                            <p class="status-available">Available</p>
                            <p class="card-text mb-3">Bright studio space with north-facing windows, easels, and art supplies. Suitable for painting, drawing, and sculpture classes. Sink and storage cabinets available for student use.</p>
                        </div>
                        <div class="facility-actions mt-auto pt-3">
                            <button class="btn btn-manage btn-flex">Manage</button>
                            <button class="btn btn-outline-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Facilities pagination">
                <ul class="pagination">
                    <li class="page-item disabled" id="prevPage">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#" data-page="1">1</a></li>
                    <li class="page-item"><a class="page-link" href="#" data-page="2">2</a></li>
                    <li class="page-item" id="nextPage">
                        <a class="page-link" href="#" data-page="2">Next</a>
                    </li>
                </ul>
            </nav>
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
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const facilityContainer = document.getElementById('facilityContainer');
        const searchInput = document.getElementById('searchInput');
        const layoutSelect = document.getElementById('layoutSelect');
        const statusFilter = document.getElementById('statusFilter');
        const departmentFilter = document.getElementById('departmentFilter');
        const categoryFilter = document.getElementById('categoryFilter');
        
        // Pagination variables
        const itemsPerPage = 9;
        let currentPage = 1;
        const allFacilityCards = Array.from(document.querySelectorAll('.facility-card'));
        const pageLinks = document.querySelectorAll('.page-link[data-page]');
        const prevPageBtn = document.getElementById('prevPage');
        const nextPageBtn = document.getElementById('nextPage');
        
        // Initialize pagination
        function initializePagination() {
            updatePagination();
            showPage(1);
            
            // Add event listeners to page links
            pageLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    showPage(page);
                });
            });
            
            // Previous page button
            prevPageBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!this.classList.contains('disabled')) {
                    showPage(currentPage - 1);
                }
            });
            
            // Next page button
            nextPageBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!this.classList.contains('disabled')) {
                    showPage(currentPage + 1);
                }
            });
        }
        
        // Show a specific page
        function showPage(page) {
            currentPage = page;
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            
            // Hide all cards first
            allFacilityCards.forEach(card => {
                card.style.display = 'none';
            });
            
            // Show cards for current page
            const visibleCards = getFilteredFacilities();
            for (let i = startIndex; i < endIndex && i < visibleCards.length; i++) {
                visibleCards[i].style.display = 'block';
            }
            
            updatePagination();
        }
        
        // Update pagination controls
        function updatePagination() {
            const totalPages = Math.ceil(getFilteredFacilities().length / itemsPerPage);
            
            // Update active state of page links
            pageLinks.forEach(link => {
                const page = parseInt(link.getAttribute('data-page'));
                link.parentElement.classList.toggle('active', page === currentPage);
                
                // Hide page links that are beyond total pages
                if (page > totalPages) {
                    link.parentElement.style.display = 'none';
                } else {
                    link.parentElement.style.display = 'block';
                }
            });
            
            // Disable/enable previous and next buttons
            prevPageBtn.classList.toggle('disabled', currentPage === 1);
            nextPageBtn.classList.toggle('disabled', currentPage === totalPages);
        }
        
        // Get filtered facilities based on search and filters
        function getFilteredFacilities() {
            const searchTerm = searchInput.value.toLowerCase();
            const status = statusFilter.value;
            const department = departmentFilter.value;
            const category = categoryFilter.value;
            
            return allFacilityCards.filter(card => {
                const cardStatus = card.dataset.status;
                const cardDept = card.dataset.department;
                const cardCategory = card.dataset.category;
                const cardTitle = card.dataset.title.toLowerCase();
                const cardText = card.querySelector('.card-text').textContent.toLowerCase();
                
                const matchesSearch = cardTitle.includes(searchTerm) || 
                                    cardText.includes(searchTerm);
                const matchesStatus = (status === 'all' || cardStatus === status);
                const matchesDept = (department === 'all' || cardDept === department);
                const matchesCategory = (category === 'all' || cardCategory === category);
                
                return matchesSearch && matchesStatus && matchesDept && matchesCategory;
            });
        }
        
        // Filter facilities based on all criteria
        function filterFacilities() {
            const searchTerm = searchInput.value.toLowerCase();
            const status = statusFilter.value;
            const department = departmentFilter.value;
            const category = categoryFilter.value;
            const layout = layoutSelect.value;
            
            // Apply layout view
            facilityContainer.className = `row g-3 ${layout === 'list' ? 'list-view' : ''}`;
            
            // Update pagination after filtering
            showPage(1);
        }
        
        // Add event listeners for all filter controls
        [searchInput, layoutSelect, statusFilter, departmentFilter, categoryFilter].forEach(control => {
            control.addEventListener('change', filterFacilities);
        });
        searchInput.addEventListener('input', filterFacilities);
        
        // Add confirmation dialog for delete buttons
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this facility?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Add functionality to the "Manage" buttons
        document.querySelectorAll('.btn-manage').forEach(button => {
            button.addEventListener('click', function() {
                const card = this.closest('.facility-card');
                const facilityName = card.querySelector('.card-title').textContent;
                alert(`Managing facility: ${facilityName}`);
                // In a real implementation, this would open an edit modal or redirect
                // window.location.href = `/manage-facility?id=${card.dataset.facilityId}`;
            });
        });
        
        // Initialize the view
        initializePagination();
    });
</script>

<!-- Modal for Facility Management -->
<div class="modal fade" id="manageFacilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Facility</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Facility management form would go here -->
                <form id="facilityForm">
                    <div class="mb-3">
                        <label for="facilityName" class="form-label">Facility Name</label>
                        <input type="text" class="form-control" id="facilityName">
                    </div>
                    <div class="mb-3">
                        <label for="facilityDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="facilityDescription" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="facilityCategory" class="form-label">Category</label>
                            <select class="form-select" id="facilityCategory">
                                <option value="Sports">Sports Facility</option>
                                <option value="Conference">Conference Room</option>
                                <option value="Religious">Religious Facility</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="facilitySubcategory" class="form-label">Subcategory</label>
                            <input type="text" class="form-control" id="facilitySubcategory">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="facilityDepartment" class="form-label">Department</label>
                            <select class="form-select" id="facilityDepartment">
                                <option value="IT">IT Department</option>
                                <option value="HR">HR Department</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="facilityStatus" class="form-label">Status</label>
                            <select class="form-select" id="facilityStatus">
                                <option value="available">Available</option>
                                <option value="reserved">Reserved</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
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
</body>
</html>