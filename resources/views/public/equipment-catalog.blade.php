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

    .toast {
      border-radius: 0 !important;
      margin-bottom: 10px;
    }

    .catalog-card {
      display: flex;
      position: relative;
      padding: 15px;
      margin: 0;
      box-sizing: border-box;
      background-color: #fff;
      border: 1px solid #d3d3d3;
      gap: 15px; /* Add spacing between the image and content */
    }

    .catalog-card .catalog-card-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 4px;
      background-color: #f8f9fa;
    }

    .catalog-card .row.g-0 {
      margin: 0; /* Remove row-level spacing */
    }

    .catalog-card .col-md-3 {
      padding: 0 5px; /* Reduce left padding to match the right padding of the container */
    }

    .catalog-card .col-md-7 {
      padding-left: 15px; /* Maintain padding between the image and content */
    }

    .catalog-card .col-md-2 {
      position: absolute;
      top: 0;
      right: 0;
      height: 100%;
      padding: 15px;
      border-left: 1px solid #d3d3d3;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background-color: #fff;
    }

    /* Adjusted grid card styling */
    .catalog-card.grid-layout {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 15px;
      background-color: #fff;
      border: 1px solid #d3d3d3;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      height: auto; /* Adjust height dynamically to fit content */
      min-height: 200px; /* Ensure minimum height */
      padding-bottom: 20px; /* Add padding to prevent overflow */
    }

    .catalog-card.grid-layout:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .catalog-card.grid-layout img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 4px;
      margin-bottom: 10px;
    }

    .catalog-card.grid-layout .card-body {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .catalog-card.grid-layout .card-title {
      font-size: 1.1rem;
      font-weight: bold;
      color: #333;
    }

    .catalog-card.grid-layout .badge {
      font-size: 0.8rem;
      padding: 5px 10px;
      border-radius: 12px;
    }

    .catalog-card.grid-layout .card-text {
      font-size: 0.9rem;
      color: #666;
      text-align: left;
    }

    .catalog-card.grid-layout .rental-fee {
      font-size: 1.2rem;
      font-weight: bold;
      text-align: left;
      color: var(--cpu-primary);
    }

    .catalog-card.grid-layout .btn {
      font-size: 0.85rem;
      padding: 0.5rem 0.75rem;
    }

    .catalog-card.grid-layout .btn-outline-secondary {
      color: #6c757d;
      border-color: #6c757d;
    }

    .catalog-card.grid-layout .btn-outline-secondary:hover {
      background-color: #6c757d;
      color: #fff;
    }

    .catalog-card.grid-layout .btn-primary {
      background-color: var(--cpu-primary);
      border-color: var(--cpu-primary);
    }

    .catalog-card.grid-layout .btn-primary:hover {
      background-color: var(--cpu-primary-hover);
      border-color: var(--cpu-primary-hover);
    }

    .catalog-card.grid-layout .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    .catalog-card.grid-layout .btn-danger:hover {
      background-color: #a71d2a;
      border-color: #a71d2a;
    }

    .catalog-card.grid-layout .d-flex.flex-wrap.gap-2 span {
      font-size: 0.9rem; /* Match text size with location note */
      color: #666; /* Ensure consistent color */
      gap: 5px; /* Reduced gap between tags */
    }
  </style>
</head>

<body>
<body>
@extends('layouts.app')

@section('title', 'Equipment Catalog')


@section('content')

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
                    <a class="dropdown-item" href="equipmentcatalog.html" data-catalog-type="equipment">
                      Equipment
                    </a>
                  </li>
                </ul>
              </div>
            </h4>
            <div class="d-flex gap-2 filter-sort-dropdowns">
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="statusDropdown">
                  Status: Available
                </button>
                <ul class="dropdown-menu" id="statusFilterMenu">
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="All">All</a>
                  </li>
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="Available">Available</a>
                  </li>
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="Unavailable">Unavailable</a>
                  </li>
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="Under Maintenance">Under Maintenance</a>
                  </li>
                  <li>
                    <a class="dropdown-item status-option" href="#" data-status="Closed">Closed</a>
                  </li>
                </ul>
              </div>
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                  id="layoutDropdown">
                  Grid Layout
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
            <p class="mt-2">Loading equipment...</p>
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
@endsection

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="{{ asset('js/public/equipmentcatalog.js') }}"></script>
</body>

</html>