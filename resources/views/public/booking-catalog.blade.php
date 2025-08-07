@extends('layouts.app')

@section('title', 'Booking Catalog')

@section('content')
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/public/catalog.css') }}" />
  <style>
    /* Catalog Hero Section */
    .catalog-hero-section {
    background-image: url("{{ asset('assets/homepage.jpg') }}");
    background-size: cover;
    background-position: center;
    min-height: 170px;
    display: flex;
    align-items: flex-end;
    padding-bottom: 20px;
    position: relative;
    z-index: 0;
    }
  </style>

  <section class="catalog-hero-section">
    <div class="catalog-hero-content">
    <h2 id="catalogHeroTitle">Facility & Equipment Catalog</h2>
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
        <a id="requisitionFormButton" href="reservation-form"
        class="btn btn-primary d-flex justify-content-center align-items-center position-relative mb-2">
        <i class="bi bi-receipt me-2"></i> Your Requisition Form
        <span id="requisitionBadge" class="badge bg-danger rounded-pill position-absolute d-none">
          0
        </span>
        </a>

        <button type="button" class="btn btn-outline-primary d-flex align-items-center" id="eventsCalendarBtn"
        data-bs-toggle="modal" data-bs-target="#userCalendarModal">Events Calendar</button>
      </div>

      <div class="sidebar-card">
        <h5>Browse by Category</h5>
        <div class="filter-list" id="categoryFilterList"></div>
      </div>
      </div>

      <div class="col-lg-9 col-md-8">
      <div class="right-content-header">
        <div class="dropdown">
        <button class="btn btn-link dropdown-toggle text-decoration-none" type="button" id="chooseCatalogDropdown"
          data-bs-toggle="dropdown" aria-expanded="false">
          All Facilities
        </button>
        <ul class="dropdown-menu" aria-labelledby="chooseCatalogDropdown">
          <li>
          <a class="dropdown-item" href="booking-catalog" data-catalog-type="facilities">
            Facilities
          </a>
          </li>
          <li>
          <a class="dropdown-item" href="equipment-catalog" data-catalog-type="equipment">
            Equipment
          </a>
          </li>
        </ul>
        </div>
        <div class="d-flex gap-2 filter-sort-dropdowns">
        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
          id="statusDropdown">
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
            <a class="dropdown-item status-option" href="#" data-status="Under Maintenance">Under
            Maintenance</a>
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
            <a class="dropdown-item layout-option active" href="#" data-layout="grid">Grid</a>
          </li>
          <li>
            <a class="dropdown-item layout-option" href="#" data-layout="list">List</a>
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

      <!-- Facility Detail Modal -->
      <div class="modal fade" id="facilityDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
          <h5 class="modal-title" id="facilityDetailModalLabel">Facility Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="facilityDetailContent">
          <!-- Content will be loaded dynamically -->
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
        <p class="mt-2">Loading catalog items...</p>
      </div>

      <!-- Catalog Items Container -->
      <div id="catalogItemsContainer" class="grid-layout d-none"></div>

      <div class="text-center mt-4">
        <nav>
        <ul id="pagination" class="pagination justify-content-center"></ul>
        </nav>
      </div>
      </div>
    </div>
    </div>
  </main>
@endsection

@section('scripts')
<script src="{{ asset('js/public/booking-catalog.js') }}"></script>
@endsection