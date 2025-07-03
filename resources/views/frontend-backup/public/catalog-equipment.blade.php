<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Catalog - Equipment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/public/catalog.css') }}" />
  <style>
    body {
      background-color: #f8f9fa;
    }

    .main-content {
      padding-top: 20px;
    }

    .form-section-card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    * {
      border-radius: 0 !important;
    }

    .profile-img,
    .status-indicator {
      border-radius: 50% !important;
    }

    #userCalendarModal .modal-dialog {
      max-width: 90vw;
      width: 100%;
    }

    #userCalendarModal .modal-body {
      height: 75vh;
      overflow-y: auto;
      padding: 0;
      margin: 0;
    }

    #userFullCalendar {
      width: 100%;
      height: 100%;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid #e0e0e0;
    }

    .fc .fc-button-primary {
      background-color: #007bff;
      border-color: #007bff;
      color: #fff;
    }

    .fc .fc-button-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .fc-event {
      border-radius: 4px;
      padding: 2px 4px;
      cursor: pointer;
      font-size: 0.85em;
      line-height: 1.2;
    }

    .fc-event-main,
    .fc-event-title-container {
      color: white;
    }

    .quick-links-card,
    .sidebar-card,
    .catalog-card,
    .dropdown-menu {
      border-radius: 0 !important;
      border: 1px solid #d3d3d3 !important;
    }

    .container {
      border: none !important;
      box-shadow: none !important;
    }

    /* Ensure the spinner is circular */
    .spinner-border {
      border-radius: 50% !important;
    }

    /* Add sharp edges and light grey borders to specific containers */
    .quick-links-card,
    .sidebar-card,
    .catalog-card,
    .dropdown-menu {
      border-radius: 0 !important;
      border: 1px solid #d3d3d3 !important;
    }

    /* Remove border and shadows from the main container */
    .container {
      border: none !important;
      box-shadow: none !important;
    }

    /* Style pagination to use CPU primary colors */
    .pagination .page-item.active .page-link {
      background-color: var(--cpu-primary) !important;
      border-color: var(--cpu-primary) !important;
      color: #fff !important;
    }

    .pagination .page-link {
      color: var(--cpu-primary) !important;
      border-color: lightgrey !important;
    }

    .pagination .page-item.disabled .page-link {
      color: #6c757d !important;
      border-color: #d3d3d3 !important;
    }

    /* Loading overlay styles */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .loading-content {
      background: white;
      padding: 20px;
      border-radius: 5px;
      text-align: center;
    }
  </style>
</head>

<body>
  <header class="top-header-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="cpu-brand">
            <img src="{{ asset('assets/public/cpu-logo.png') }}" alt="CPU Logo">
            <div>
                <div class="title">Central Philippine University</div>
                <div class="subtitle">Equipment and Facility Booking Services</div>
            </div>
        </div>
        <div class="admin-login">
            <span>Are you an Admin? <a href="admin pages/adminlogin.html">Login here.</a></span>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" aria-current="page" href="#" id="navbarDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Booking Catalog
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="facilities">About Facilities</a></li>
                        <li><a class="dropdown-item" href="equipmentpage">About Equipment</a></li>
                        <li><a class="dropdown-item" href="extraservicespage">About Services</a></li>
                        <li><a class="dropdown-item" href="bookingcatalog">Booking Catalog</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mybookingpage">My Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="policies">Reservation Policies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="feedbackpage">Rate Our Services</a>
                </li>
            </ul>
            <a href="bookingpage" class="btn btn-book-now ms-lg-3">Book Now</a>
        </div>
    </div>
</nav>

  <section class="catalog-hero-section">
    <div class="catalog-hero-content">
      <h2 id="catalogHeroTitle">Equipment Catalog</h2>
    </div>
  </section>

  <main class="main-catalog-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-4">
          <div class="quick-links-card mb-4">
            <p class="mb-2">
              Not sure when to book?<br />View available timeslots here.
            </p>
            <a id="requisitionFormButton" href="bookingpage.html"
              class="btn btn-primary d-flex justify-content-center align-items-center position-relative mb-2"
              style="border-radius: 0; font-size: 0.9rem; padding: 0.4rem 0.7rem; text-align: center;">
              <i class="bi bi-receipt me-2"></i> Your Requisition Form
              <span id="requisitionBadge" class="badge bg-danger rounded-pill position-absolute d-none"
                style="top: 0; right: 0; transform: translate(50%, -50%); font-size: 0.8rem;">
                0
              </span>
            </a>

            <button type="button" class="btn btn-outline-primary d-flex align-items-center"
              style="border-radius: 0; font-size: 1rem; padding: 0.4rem 0.7rem; width: 100%;" id="eventsCalendarBtn"
              data-bs-toggle="modal" data-bs-target="#userCalendarModal">Events Calendar</button>
          </div>

          <div class="sidebar-card">
            <h5>Browse by Category</h5>
            <div class="filter-list" id="categoryFilterList"></div>
          </div>
        </div>

        <div class="col-lg-9 col-md-8">
          <div class="right-content-header">
            <h4 id="currentCategoryTitle" class="d-flex align-items-center">
              <div class="dropdown">
                <button class="btn btn-link dropdown-toggle text-decoration-none" type="button"
                  id="chooseCatalogDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                  style="font-size: 1.2rem; color: inherit;">
                  All Equipment
                </button>
                <ul class="dropdown-menu" aria-labelledby="chooseCatalogDropdown">
                  <li>
                    <a class="dropdown-item" href="bookingcatalog.html" data-catalog-type="facilities">
                      Facilities
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item active" href="equipmentcatalog.html" data-catalog-type="equipment">
                      Equipment
                    </a>
                  </li>
                </ul>
              </div>
            </h4>
            <div class="d-flex gap-2 filter-sort-dropdowns">
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                  id="layoutDropdown">
                  List Layout
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item layout-option" href="#" data-layout="list">List</a>
                  </li>
                  <li>
                    <a class="dropdown-item layout-option" href="#" data-layout="grid">Grid</a>
                  </li>
                </ul>
              </div>
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Filter By
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item" href="#">Price (Low to High)</a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">Price (High to Low)</a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">Alphabetical (A-Z)</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="modal fade" id="userCalendarModal" tabindex="-1" aria-labelledby="userCalendarModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="userCalendarModalLabel">View Booking Events Calendar</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="userFullCalendar"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading Indicator -->
          <div id="loadingIndicator" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading Equipment...</p>
          </div>

          <!-- Catalog Items Container -->
          <div id="catalogItemsContainer" class="list-layout d-none"></div>

          <div class="text-center mt-4">
            <nav>
              <ul id="pagination-top" class="pagination justify-content-center"></ul>
              <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <div class="container">
      <p>&copy; 2025 Central Philippine University | All Rights Reserved</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="{{ asset('js/public/catalog-equipment.js') }}"></script>
</body>
</html>