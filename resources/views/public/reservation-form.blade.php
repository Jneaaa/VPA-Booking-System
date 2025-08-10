<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>
    Reservation Form Submission
  </title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"
    rel="stylesheet" />
  <style>
    /* Add to your existing styles */
    .card {
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .card-title {
      color: #003366;
      font-weight: 600;
    }

    .badge {
      font-weight: 500;
      padding: 5px 8px;
    }

    .btn-outline-danger {
      border: none;
      padding: 0.25rem 0.5rem;
    }

    .btn-outline-danger:hover {
      background-color: rgba(220, 53, 69, 0.1);
    }

    body {
      background-color: #f8f9fa;
    }

    .main-content {
      padding-top: 20px;
    }

    .form-section-card {
      background: white;
      padding: 20px;
      border: 1px solid #ddd;
      /* Replace shadow with solid stroke */
      border-radius: 0;
      /* Remove rounded corners */
      margin-bottom: 20px;
    }

    .form-section-card h5,
    .form-section-card h6 {
      color: #003366;
      margin-bottom: 15px;
    }

    .form-section-card .form-control,
    .form-section-card .form-select {
      margin-bottom: 10px;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .summary-item:last-child {
      border-bottom: none;
    }

    .summary-item .item-details {
      flex-grow: 1;
    }

    .summary-item .item-price {
      font-weight: bold;
    }

    .calendar-header {
      background-color: #e9ecef;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 5px;
      text-align: center;
    }

    .calendar-day-name {
      font-weight: bold;
      padding: 5px;
      background-color: #f0f0f0;
    }

    .calendar-day {
      padding: 10px 5px;
      border: 1px solid #ddd;
      border-radius: 5px;
      cursor: pointer;
      background-color: #fff;
    }

    .calendar-day.selected {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }

    .calendar-day.disabled {
      background-color: #f8f9fa;
      color: #ccc;
      cursor: not-allowed;
    }

    .selected-schedule {
      background-color: #e9f5ff;
      padding: 10px;
      border-radius: 5px;
      margin-top: 15px;
    }

    .availability-row {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .availability-row .form-control {
      flex-grow: 1;
      margin-right: 10px;
    }

    .availability-status {
      color: green;
      font-weight: bold;
    }

    .slideshow-placeholder {
      background-color: #e9ecef;
      height: 200px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #6c757d;
      border-radius: 5px;
      margin-bottom: 15px;
    }

    .included-equipment-list {
      list-style: none;
      padding: 0;
    }

    .included-equipment-list li {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .quantity-control {
      display: flex;
      align-items: center;
    }

    .quantity-control .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
    }

    .quantity-control input {
      width: 50px;
      text-align: center;
      margin: 0 5px;
    }

    .total-price {
      font-size: 1.25em;
      font-weight: bold;
    }

    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      z-index: 1050;
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 80%;
      max-width: 700px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      max-height: 90vh;
      overflow-y: auto;
    }

    .popup.show {
      display: block;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1040;
    }

    .overlay.show {
      display: block;
    }

    .col-md-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
    }

    .facility-card,
    .equipment-card {
      position: relative;
      /* Enable positioning for the trash bin */
      border: none;
      /* Remove grey border */
      border-radius: 0;
      /* Remove rounded corners */
      padding: 10px;
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .facility-card .btn-outline-danger,
    .equipment-card .btn-outline-danger {
      position: absolute;
      top: 10px;
      right: 10px;
      /* Move trash bin to top-right */
      z-index: 1;
      /* Ensure it stays above other elements */
      border: none;
      /* Remove red border */
    }

    .facility-card img,
    .equipment-card img {
      width: 120px;
      /* Ensure consistent image size */
      height: 120px;
      object-fit: cover;
      border-radius: 0;
      /* Remove rounded corners from images */
      margin-right: 15px;
    }

    .facility-card .facility-details,
    .equipment-card .equipment-details {
      flex-grow: 1;
      margin-right: 30px;
      /* Further reduce right margin */
    }

    .selected-item-card {
      background: #fff;
      border: 1px solid #dee2e6;
      padding: 15px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .selected-item-image {
      width: 80px;
      height: 80px;
      flex-shrink: 0;
    }

    .selected-item-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .selected-item-details {
      flex-grow: 1;
    }

    .selected-item-details h6 {
      margin-bottom: 5px;
      color: #333;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 5px;
    }

    .quantity-control input {
      width: 60px;
      text-align: center;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      padding: 2px;
    }

    .quantity-control button {
      padding: 0 8px;
      font-size: 14px;
    }

    .delete-item-btn {
      align-self: flex-start;
    }

    .selected-items-container .selected-item-card {
      background: #fff;
      border: 1px solid #dee2e6;
      padding: 15px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .selected-item-details {
      flex-grow: 1;
    }

    .selected-item-details h6 {
      margin-bottom: 5px;
      color: #333;
    }

    .selected-item-details .fee {
      color: #28a745;
      font-weight: 500;
    }

    .selected-item-details .quantity-control {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 8px;
    }

    .delete-item-btn {
      color: #dc3545;
      background: none;
      border: none;
      padding: 5px;
      cursor: pointer;
    }

    .delete-item-btn:hover {
      color: #bd2130;
    }
  </style>
</head>

<body>
  <header class="top-header-bar">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="cpu-brand">
        <img src="{{ asset('assets/cpu-logo.png') }}" alt="CPU Logo">
        <div>
          <div class="title">Central Philippine University</div>
          <div class="subtitle">Equipment and Facility Booking Services</div>
        </div>
      </div>
      <div class="admin-login">
        <span>Are you an Admin? <a href="{{ url('admin/admin-login') }}">Login here.</a></span>
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
            <a class="nav-link {{ Request::is('index') ? 'active' : '' }}" href="{{ url('index') }}">Home</a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('facility-catalog*') ? 'active' : '' }}" href="#"
              role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Booking Catalog
            </a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item {{ Request::is('facility-catalog') ? 'active' : '' }}"
                  href="{{ asset('facility-catalog') }}">
                  Facility Catalog
                </a>
              </li>
              <li>
                <a class="dropdown-item {{ Request::is('equipment-catalog') ? 'active' : '' }}"
                  href="{{ asset('equipment-catalog') }}">
                  Equipment Catalog
                </a>
              </li>
            </ul>

          </li>

          <li class="nav-item">
            <a class="nav-link {{ Request::is('your-bookings') ? 'active' : '' }}"
              href="{{ url('your-bookings') }}">Your Bookings</a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ Request::is('policies') ? 'active' : '' }}" href="{{ url('policies') }}">Reservation
              Policies</a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('about-facilities', 'about-equipment', 'about-services') ? 'active' : '' }}"
              href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              About Services
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item {{ Request::is('about-facilities') ? 'active' : '' }}"
                  href="{{ url('about-facilities') }}">Facilities</a></li>
              <li><a class="dropdown-item {{ Request::is('about-equipment') ? 'active' : '' }}"
                  href="{{ url('about-equipment') }}">Equipment</a></li>
              <li><a class="dropdown-item {{ Request::is('about-services') ? 'active' : '' }}"
                  href="{{ url('about-services') }}">Services</a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ Request::is('user-feedback') ? 'active' : '' }}"
              href="{{ url('user-feedback') }}">Rate Our Services</a>
          </li>
        </ul>

        <a href="{{ url('facility-catalog') }}" class="btn btn-book-now ms-lg-3">Back To Catalog</a>
      </div>
    </div>
  </nav>

  <script>
    // Initialize Bootstrap dropdowns
    document.addEventListener('DOMContentLoaded', function () {
      const dropdownElements = document.querySelectorAll('.dropdown-toggle');
      dropdownElements.forEach(dropdown => {
        new bootstrap.Dropdown(dropdown);
      });
    });
  </script>

  <div class="container main-content">
    <form id="reservationForm" method="POST">
      @csrf
      <!-- Complete Your Reservation Section -->
      <div class="row">
        <div class="col-12">
          <style>
            .btn-transparent {
              background-color: transparent !important;
              border: none !important;
              box-shadow: none !important;
            }

            .btn-transparent i {
              display: inline-block;
              /* Needed for transform to work */
              color: #6c757d;
              transition: transform 0.25s ease-in-out;
            }

            button.btn-transparent[aria-expanded="true"] i.bi-chevron-down {
              transform: rotate(0deg);
            }

            button.btn-transparent[aria-expanded="false"] i.bi-chevron-down {
              transform: rotate(180deg);
            }
          </style>

          <div class="form-section-card">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Complete Your Reservation</h5>
              <button id="toggleReservationBtn" type="button" class="btn btn-sm btn-secondary btn-transparent"
                style="height: 100%; align-self: center" data-bs-toggle="collapse" data-bs-target="#reservationContent"
                aria-expanded="true" aria-controls="reservationContent">
                <i class="bi bi-chevron-down"></i>
              </button>
            </div>

            <div id="reservationContent" class="collapse show" style="padding-top: 10px">
              <p class="text-muted">
                To confirm your request, please fill out the necessary details below.
                We need this information to process your booking efficiently and provide
                complete details on how to proceed. A confirmation email will be sent
                to your registered email address once your submission is reviewed and approved.
              </p>
              <div class="d-flex justify-content-start gap-2 mb-4">
                <a href="policies.html" class="btn btn-primary">Read Policies</a>
              </div>
            </div>
          </div>
        </div>
      </div>



      <!-- Two-column grid layout -->
      <div class="row">
        <div class="col-md-6 d-flex flex-column">
          <div class="form-section-card flex-grow-1">
            <h5>Your Contact Information</h5>
            <div class="row">
              <div class="col-md-12">
                <label class="form-label">Applicant Type <span style="color: red;">*</span></label>
                <select id="applicantType" class="form-select mb-2" aria-label="Type of Applicant">
                  <option selected>Type of Applicant</option>
                  <option value="Internal">Internal</option>
                  <option value="External">External</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">
                  First Name <span style="color: red;">*</span>
                </label>
                <input name="first_name" type="text" class="form-control" placeholder="First Name" required />
              </div>
              <div class="col-md-6">
                <label class="form-label">
                  Last Name <span style="color: red;">*</span>
                </label>
                <input name="last_name" type="text" class="form-control" placeholder="Last Name" required />
              </div>
              <div id="studentIdField" class="col-md-6">
                <label class="form-label">CPU Student ID</label>
                <input type="text" class="form-control" placeholder="Student ID" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input name="contact_number" type="text" class="form-control" placeholder="Contact Number" />
              </div>
              <div class="col-md-12">
                <label class="form-label">Email Address <span style="color: red;">*</span></label>
                <input name="email" type="email" class="form-control mb-2" placeholder="Email Address" required />
              </div>
              <div class="col-md-12">
                <label class="form-label">Organization Name</label>
                <input name="organization_name" type="text" class="form-control mb-2" placeholder="Organization Name" />
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 d-flex flex-column">
          <div class="form-section-card flex-grow-1" style="padding-bottom: 15px;">
            <div class="d-flex justify-content-between align-items-center">
              <h5>Booking Schedule</h5>
            </div>
            <div class="d-flex justify-content-center mt-2">
              <i class="bi bi-calendar-check" style="font-size: 3rem; color:var(--cpu-primary);"></i>
            </div>
            <p id="selectedDateTime" class="text-muted">
              No date and time selected.
            </p>
            <div class="row">
              <div class="col-md-6">
                <label for="startDateField" class="form-label">Start Date</label>
                <input name="start_date" type="date" id="startDateField" class="form-control mb-2" />
              </div>
              <div class="col-md-6">
                <label for="startTimeField" class="form-label">Start Time</label>
                <select id="startTimeField" name="start_time" class="form-select mb-2" onchange="adjustEndTime()">
                  <!-- Predefined 12-hour intervals -->
                  <option value="12:00 AM">12:00 AM</option>
                  <option value="12:30 AM">12:30 AM</option>
                  <option value="01:00 AM">01:00 AM</option>
                  <option value="01:30 AM">01:30 AM</option>
                  <option value="02:00 AM">02:00 AM</option>
                  <option value="02:30 AM">02:30 AM</option>
                  <option value="03:00 AM">03:00 AM</option>
                  <option value="03:30 AM">03:30 AM</option>
                  <option value="04:00 AM">04:00 AM</option>
                  <option value="04:30 AM">04:30 AM</option>
                  <option value="05:00 AM">05:00 AM</option>
                  <option value="05:30 AM">05:30 AM</option>
                  <option value="06:00 AM">06:00 AM</option>
                  <option value="06:30 AM">06:30 AM</option>
                  <option value="07:00 AM">07:00 AM</option>
                  <option value="07:30 AM">07:30 AM</option>
                  <option value="08:00 AM">08:00 AM</option>
                  <option value="08:30 AM">08:30 AM</option>
                  <option value="09:00 AM">09:00 AM</option>
                  <option value="09:30 AM">09:30 AM</option>
                  <option value="10:00 AM">10:00 AM</option>
                  <option value="10:30 AM">10:30 AM</option>
                  <option value="11:00 AM">11:00 AM</option>
                  <option value="11:30 AM">11:30 AM</option>
                  <option value="12:00 PM">12:00 PM</option>
                  <option value="12:30 PM">12:30 PM</option>
                  <option value="01:00 PM">01:00 PM</option>
                  <option value="01:30 PM">01:30 PM</option>
                  <option value="02:00 PM">02:00 PM</option>
                  <option value="02:30 PM">02:30 PM</option>
                  <option value="03:00 PM">03:00 PM</option>
                  <option value="03:30 PM">03:30 PM</option>
                  <option value="04:00 PM">04:00 PM</option>
                  <option value="04:30 PM">04:30 PM</option>
                  <option value="05:00 PM">05:00 PM</option>
                  <option value="05:30 PM">05:30 PM</option>
                  <option value="06:00 PM">06:00 PM</option>
                  <option value="06:30 PM">06:30 PM</option>
                  <option value="07:00 PM">07:00 PM</option>
                  <option value="07:30 PM">07:30 PM</option>
                  <option value="08:00 PM">08:00 PM</option>
                  <option value="08:30 PM">08:30 PM</option>
                  <option value="09:00 PM">09:00 PM</option>
                  <option value="09:30 PM">09:30 PM</option>
                  <option value="10:00 PM">10:00 PM</option>
                  <option value="10:30 PM">10:30 PM</option>
                  <option value="11:00 PM">11:00 PM</option>
                  <option value="11:30 PM">11:30 PM</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="endDateField" class="form-label">End Date</label>
                <input name="end_date" type="date" id="endDateField" class="form-control mb-2" />
              </div>
              <div class="col-md-6">
                <label for="endTimeField" class="form-label">End Time</label>
                <select id="endTimeField" name="end_time" class="form-select mb-3">
                  <!-- Predefined 12-hour intervals -->
                  <option value="12:00 AM">12:00 AM</option>
                  <option value="12:30 AM">12:30 AM</option>
                  <option value="01:00 AM">01:00 AM</option>
                  <option value="01:30 AM">01:30 AM</option>
                  <option value="02:00 AM">02:00 AM</option>
                  <option value="02:30 AM">02:30 AM</option>
                  <option value="03:00 AM">03:00 AM</option>
                  <option value="03:30 AM">03:30 AM</option>
                  <option value="04:00 AM">04:00 AM</option>
                  <option value="04:30 AM">04:30 AM</option>
                  <option value="05:00 AM">05:00 AM</option>
                  <option value="05:30 AM">05:30 AM</option>
                  <option value="06:00 AM">06:00 AM</option>
                  <option value="06:30 AM">06:30 AM</option>
                  <option value="07:00 AM">07:00 AM</option>
                  <option value="07:30 AM">07:30 AM</option>
                  <option value="08:00 AM">08:00 AM</option>
                  <option value="08:30 AM">08:30 AM</option>
                  <option value="09:00 AM">09:00 AM</option>
                  <option value="09:30 AM">09:30 AM</option>
                  <option value="10:00 AM">10:00 AM</option>
                  <option value="10:30 AM">10:30 AM</option>
                  <option value="11:00 AM">11:00 AM</option>
                  <option value="11:30 AM">11:30 AM</option>
                  <option value="12:00 PM">12:00 PM</option>
                  <option value="12:30 PM">12:30 PM</option>
                  <option value="01:00 PM">01:00 PM</option>
                  <option value="01:30 PM">01:30 PM</option>
                  <option value="02:00 PM">02:00 PM</option>
                  <option value="02:30 PM">02:30 PM</option>
                  <option value="03:00 PM">03:00 PM</option>
                  <option value="03:30 PM">03:30 PM</option>
                  <option value="04:00 PM">04:00 PM</option>
                  <option value="04:30 PM">04:30 PM</option>
                  <option value="05:00 PM">05:00 PM</option>
                  <option value="05:30 PM">05:30 PM</option>
                  <option value="06:00 PM">06:00 PM</option>
                  <option value="06:30 PM">06:30 PM</option>
                  <option value="07:00 PM">07:00 PM</option>
                  <option value="07:30 PM">07:30 PM</option>
                  <option value="08:00 PM">08:00 PM</option>
                  <option value="08:30 PM">08:30 PM</option>
                  <option value="09:00 PM">09:00 PM</option>
                  <option value="09:30 PM">09:30 PM</option>
                  <option value="10:00 PM">10:00 PM</option>
                  <option value="10:30 PM">10:30 PM</option>
                  <option value="11:00 PM">11:00 PM</option>
                  <option value="11:30 PM">11:30 PM</option>
                </select>
              </div>
            </div>
            <div class="d-flex justify-content-start gap-2">
              <button id="clearSelectionBtn" class="btn btn-outline-secondary">
                Clear Selection
              </button>
              <button id="checkAvailabilityBtn" class="btn btn-primary">
                Check Availability
              </button>
              <span id="availabilityResult" style="margin-left: 1px; font-weight: bold;"></span>
            </div>
            <p class="text-muted mt-4" style="font-size: 0.875rem;">
              In case of emergency, please ensure to cancel reservations at least 5 days before the scheduled date to
              avoid complications.
            </p>
          </div>
        </div>
      </div>

      <!-- Single column for Reservation Details -->
      <div class="row">
        <div class="col-12">
          <div class="form-section-card">
            <h5>Reservation Details</h5>
            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Number of Participants</label>
                <input name="num_participants" type="number" class="form-control mb-2" value="1" min="1" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Activity/Purpose</label>
                <select id="activityPurposeField" name="purpose_id" class="form-select mb-2"
                  aria-label="Activity/Purpose" required>
                  <option value="" selected disabled>Select Activity/Purpose</option>
                  <option value="8">Alumni - Class Reunion</option>
                  <option value="9">Alumni - Personal Events</option>
                  <option value="7">Alumni-Organized Events</option>
                  <option value="5">CPU Organization Led Activity</option>
                  <option value="2">Equipment Rental</option>
                  <option value="10">External Event</option>
                  <option value="1">Facility Rental</option>
                  <option value="6">Student-Organized Activity</option>
                  <option value="3">Subject Requirement - Class, Seminar, Conference</option>
                  <option value="4">University Program/Activity</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Endorser Name</label>
                <input name="endorser" type="text" class="form-control" placeholder="Endorser Name" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Date Endorsed</label>
                <input name="date_endorsed" type="date" class="form-control mb-2" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Additional Requests</label>
                <textarea name="additional_requests" class="form-control mb-2" rows="3"
                  placeholder="Write a brief description of any additional requests you may have."></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label mt-1">Attach Formal Letter</label>
                <div class="position-relative">
                  <input name="formal_letter_url" type="file" class="form-control mb-1" id="attachLetter"
                    onchange="toggleRemoveButton('attachLetter', 'removeAttachLetterBtn')" />
                  <button type="button" id="removeAttachLetterBtn"
                    class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none"
                    style="color: black; background: none; border: none"
                    onclick="removeFile('attachLetter', 'removeAttachLetterBtn')">
                    x
                  </button>
                </div>
                <small class="text-muted" style="margin-top: -5px;">
                  This file is required to explain the requisition's intent and serves as a formal request to the Vice
                  President of Administration.
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom 2-column grid -->
      <div class="row">
        <div class="col-md-6">
          <div class="form-section-card">
            <h5>Requested Facilities</h5>
            <div id="facilityList" class="selected-items-container">
              <!-- Facility items will be dynamically added here -->
              <div class="text-muted empty-message">No facilities added yet.</div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-section-card">
            <h5>Requested Equipment</h5>
            <div id="equipmentList" class="selected-items-container">
              <!-- Equipment items will be dynamically added here -->
              <div class="text-muted empty-message">No equipment added yet.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fee Breakdown Section -->
      <div class="row">
        <div class="col-12">
          <div class="form-section-card">
            <h5>Fee Breakdown</h5>
            <div id="feeDisplay" class="fee-breakdown">
              <!-- Fee details will be dynamically added here -->
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-center my-4">
        <button id="submitFormBtn" class="btn btn-primary me-2">
          Submit Form
        </button>
        <button type="reset" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>

  <div id="facilityPopup" class="popup">
    <h5>Select Available Facilities</h5>
    <p>
      This is a placeholder for facility selection content. Here you would
      list available facilities, possibly with a date/time filter.
    </p>
    <div class="d-flex justify-content-end">
      <button onclick="togglePopup('facilityPopup')" class="btn btn-danger">
        Close
      </button>
    </div>
  </div>

  <div id="equipmentPopup" class="popup">
    <h5>Select Equipment</h5>
    <p>
      This is a placeholder for equipment selection content. Here you would
      list available equipment based on the selected date/time.
    </p>
    <div class="d-flex justify-content-end">
      <button onclick="togglePopup('equipmentPopup')" class="btn btn-danger">
        Close
      </button>
    </div>
  </div>
  <footer class="footer-container">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Central Philippine University | All Rights Reserved</p>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // DOM Elements
      const facilityList = document.getElementById('facilityList');
      const equipmentList = document.getElementById('equipmentList');
      const feeDisplay = document.getElementById('feeDisplay');
      const submitBtn = document.getElementById('submitFormBtn');
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      // Initialize the form
      initForm();


      async function initForm() {
        try {
          await Promise.all([
            renderSelectedItems(),
            calculateAndDisplayFees()
          ]);
        } catch (error) {
          console.error('Error initializing form:', error);
          showToast('Failed to initialize form', 'error');
        }
      }

      // Fetch and render selected items
      async function renderSelectedItems() {
        try {
          const response = await fetch('/requisition/get-items', {
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            }
          });

          if (!response.ok) throw new Error('Failed to fetch items');

          const data = await response.json();
          const items = data.data?.selected_items || [];

          renderItemsList(facilityList, items.filter(i => i.type === 'facility'), 'facility');
          renderItemsList(equipmentList, items.filter(i => i.type === 'equipment'), 'equipment');

        } catch (error) {
          console.error('Error rendering selected items:', error);
          showToast('Failed to load selected items', 'error');
        }
      }

      function pluralizeType(type) {
        if (type.toLowerCase() === 'facility') return 'facilities';
        if (type.toLowerCase() === 'equipment') return 'equipment'; // same plural
        return type + 's'; // fallback
      }

      // Render items list with card layout
      function renderItemsList(container, items, type) {
        if (!container) return;

        container.innerHTML = '';

        if (items.length === 0) {
          container.innerHTML = `<div class="text-muted empty-message">No ${pluralizeType(type)} added yet.</div>`;
          return;
        }

        const cardContainer = document.createElement('div');
        cardContainer.className = 'row row-cols-1 g-3';

        items.forEach(item => {
          const card = document.createElement('div');
          card.className = 'col';

          // Find primary image or first available image
          const displayImage = item.images?.find(img => img.image_type === "Primary") || item.images?.[0];

          card.innerHTML = `
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">${item.name}</h5>
                            <p class="card-text text-muted mb-2">
                                <small>${item.description || 'No description available'}</small>
                            </p>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="removeSelectedItem(${type === 'facility' ? item.facility_id : item.equipment_id}, '${type}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <div>
                            <span class="badge bg-primary me-2">${item.rate_type || 'booking'}</span>
                            ${type === 'equipment' ? `
                                <span class="badge bg-secondary">Qty: ${item.quantity || 1}</span>
                            ` : ''}
                        </div>
                        <div class="text-end">
                            <div class="text-success fw-bold">
                                ₱${parseFloat(item.external_fee * (type === 'equipment' ? (item.quantity || 1) : 1)).toLocaleString()}
                            </div>
                            ${type === 'equipment' ? `
                                <small class="text-muted">${item.quantity || 1} × ₱${parseFloat(item.external_fee).toLocaleString()}</small>
                            ` : ''}
                        </div>
                    </div>
                </div>
                ${displayImage ? `
                    <img src="${displayImage.image_url}" class="card-img-bottom" 
                        alt="${item.name}" style="height: 150px; object-fit: cover;">
                ` : ''}
            </div>
        `;
          cardContainer.appendChild(card);
        });

        container.appendChild(cardContainer);
      }

      // remove items from selection
      async function removeSelectedItem(id, type) {
        try {
          const requestBody = {
            type: type,
            equipment_id: type === 'equipment' ? id : undefined,
            facility_id: type === 'facility' ? id : undefined
          };

          // Remove undefined fields from the request body
          const cleanedRequestBody = Object.fromEntries(
            Object.entries(requestBody).filter(([_, v]) => v !== undefined)
          );

          const response = await fetch('/api/requisition/remove-item', { // Updated endpoint
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify(cleanedRequestBody)
          });

          if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to remove item');
          }

          const result = await response.json();

          if (result.success) {
            await Promise.all([
              renderSelectedItems(),
              calculateAndDisplayFees()
            ]);
            showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} removed successfully`, 'success');

            // Update cart badge if exists
            if (typeof updateCartBadge === 'function') {
              updateCartBadge();
            }
          } else {
            throw new Error(result.message || 'Failed to remove item');
          }
        } catch (error) {
          console.error('Error removing item:', error);
          showToast(error.message || 'Failed to remove item', 'error');
        }
      }

      // Calculate and display fees
      async function calculateAndDisplayFees() {
        try {
          const response = await fetch('/api/requisition/get-items', {
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            }
          });

          if (!response.ok) throw new Error('Failed to fetch items');

          const data = await response.json();
          const items = data.data?.selected_items || []; // Changed to access selected_items

          let facilityTotal = 0;
          let equipmentTotal = 0;
          let htmlContent = '<div class="fee-items">';

          // Facilities breakdown
          const facilityItems = items.filter(i => i.type === 'facility');
          if (facilityItems.length > 0) {
            htmlContent += '<div class="fee-section"><h6 class="mb-3">Facilities</h6>';

            facilityItems.forEach(item => {
              const fee = parseFloat(item.external_fee);
              facilityTotal += fee;

              htmlContent += `
                    <div class="fee-item d-flex justify-content-between mb-2">
                        <span>${item.name}</span>
                        <span>₱${fee.toLocaleString()}</span>
                    </div>
                `;
            });

            htmlContent += `
                <div class="subtotal d-flex justify-content-between mt-2 pt-2 border-top">
                    <strong>Subtotal</strong>
                    <strong>₱${facilityTotal.toLocaleString()}</strong>
                </div>
            </div>`;
          }

          // Equipment breakdown
          const equipmentItems = items.filter(i => i.type === 'equipment');
          if (equipmentItems.length > 0) {
            htmlContent += '<div class="fee-section mt-3"><h6 class="mb-3">Equipment</h6>';

            equipmentItems.forEach(item => {
              const unitFee = parseFloat(item.external_fee);
              const quantity = item.quantity || 1;
              const itemTotal = unitFee * quantity;
              equipmentTotal += itemTotal;

              htmlContent += `
                    <div class="fee-item d-flex justify-content-between mb-2">
                        <span>${item.name} ${quantity > 1 ? `(x${quantity})` : ''}</span>
                        <div class="text-end">
                            <div>₱${unitFee.toLocaleString()} × ${quantity}</div>
                            <strong>₱${itemTotal.toLocaleString()}</strong>
                        </div>
                    </div>
                `;
            });

            htmlContent += `
                <div class="subtotal d-flex justify-content-between mt-2 pt-2 border-top">
                    <strong>Subtotal</strong>
                    <strong>₱${equipmentTotal.toLocaleString()}</strong>
                </div>
            </div>`;
          }

          // Total
          const total = facilityTotal + equipmentTotal;
          if (total > 0) {
            htmlContent += `
                <div class="total-fee d-flex justify-content-between mt-4 pt-3 border-top">
                    <h6 class="mb-0">Total Amount</h6>
                    <h6 class="mb-0">₱${total.toLocaleString()}</h6>
                </div>
            `;
          } else {
            htmlContent += '<div class="text-muted text-center">No items added yet.</div>';
          }

          htmlContent += '</div>';
          feeDisplay.innerHTML = htmlContent;

        } catch (error) {
          console.error('Error calculating fees:', error);
          showToast('Failed to calculate fees', 'error');
          feeDisplay.innerHTML = '<div class="alert alert-danger">Error loading fee breakdown</div>';
        }
      }
      // Make removeSelectedItem available globally
      window.removeSelectedItem = removeSelectedItem;

      // Helper function to show toast notifications
      function showToast(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');

        // Toast base styles
        toast.className = `toast align-items-center border-0 position-fixed start-0 mb-2`;
        toast.style.zIndex = '1100';
        toast.style.bottom = '0';
        toast.style.left = '0';
        toast.style.margin = '1rem';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        toast.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        // Colors
        const bgColor = type === 'success' ? '#004183ff' : '#dc3545';
        toast.style.backgroundColor = bgColor;
        toast.style.color = '#fff';
        toast.style.minWidth = '250px';
        toast.style.borderRadius = '0.3rem';

        toast.innerHTML = `
        <div class="d-flex align-items-center px-3 py-1"> 
            <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i>
            <div class="toast-body flex-grow-1" style="padding: 0.25rem 0;">${message}</div>
            <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="loading-bar" style="
            height: 3px;
            background: rgba(255,255,255,0.7);
            width: 100%;
            transition: width ${duration}ms linear;
        "></div>
    `;

        document.body.appendChild(toast);

        // Bootstrap toast instance
        const bsToast = new bootstrap.Toast(toast, { autohide: false });
        bsToast.show();

        // Float up appear animation
        requestAnimationFrame(() => {
          toast.style.opacity = '1';
          toast.style.transform = 'translateY(0)';
        });

        // Start loading bar animation
        const loadingBar = toast.querySelector('.loading-bar');
        requestAnimationFrame(() => {
          loadingBar.style.width = '0%';
        });

        // Remove after duration
        setTimeout(() => {
          // Float down disappear animation
          toast.style.opacity = '0';
          toast.style.transform = 'translateY(20px)';

          setTimeout(() => {
            bsToast.hide();
            toast.remove();
          }, 400); // matches animation time
        }, duration);
      }


    });
  </script>
</body>

</html>