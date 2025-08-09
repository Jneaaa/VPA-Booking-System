<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>
    Reservation Form Submission
  </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"
    rel="stylesheet" />
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

    #reservationContent {
      transition: max-height 0.3s ease, opacity 0.3s ease;
      overflow: hidden;
    }

    #toggleReservationBtn {
      transition: transform 0.3s ease;
    }

    #toggleReservationBtn.rotate {
      transform: rotate(180deg);
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

    /* Add these new styles */
    .form-section-card.collapsed {
      padding: 10px 20px;
    }

    .form-section-card.collapsed #reservationContent {
      height: 0;
      opacity: 0;
      margin: 0;
      padding: 0;
    }

    #reservationContent {
      height: auto;
      opacity: 1;
      transition: all 0.3s ease-in-out;
      overflow: hidden;
    }

    .chevron-icon {
      transition: transform 0.3s ease-in-out;
    }

    .chevron-icon.rotated {
      transform: rotate(180deg);
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
          <div class="form-section-card">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Complete Your Reservation</h5>
              <button id="toggleReservationBtn" class="btn btn-sm btn-outline-secondary"
                style="height: 100%; align-self: center">
                <i class="bi bi-chevron-up"></i>
              </button>
            </div>
            <div id="reservationContent" style="padding-top: 10px">
              <p class="text-muted">
                To confirm your request, please fill out the necessary details
                below. We need this information to process your booking
                efficiently and provide complete details on how to proceed. A
                confirmation email will be sent to your registered email address
                once your submission is reviewed and approved.
              </p>
              <div class="d-flex justify-content-start gap-2 mb-4">
                <a href="policies.html" class="btn btn-primary">
                  Read Policies
                </a>
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
                <label class="form-label">Applicant Type</label>
                <select id="applicantType" class="form-select mb-2" aria-label="Type of Applicant">
                  <option selected>Type of Applicant</option>
                  <option value="Internal">Internal</option>
                  <option value="External">External</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" required />
              </div>
              <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" required />
              </div>
              <div id="studentIdField" class="col-md-6">
                <label class="form-label">CPU Student ID</label>
                <input type="text" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" />
              </div>
              <div class="col-md-12">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control mb-2" required />
              </div>
              <div class="col-md-12">
                <label class="form-label">Organization Name</label>
                <input type="text" class="form-control mb-2" />
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
                <input type="date" id="startDateField" class="form-control mb-2" />
              </div>
              <div class="col-md-6">
                <label for="startTimeField" class="form-label">Start Time</label>
                <select id="startTimeField" class="form-select mb-2" onchange="adjustEndTime()">
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
                <input type="date" id="endDateField" class="form-control mb-2" />
              </div>
              <div class="col-md-6">
                <label for="endTimeField" class="form-label">End Time</label>
                <select id="endTimeField" class="form-select mb-3">
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
                <input type="number" class="form-control mb-2" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Activity/Purpose</label>
                <select id="activityPurposeField" class="form-select mb-2" aria-label="Activity/Purpose">
                  <option selected disabled>Loading...</option>
                </select>
                <script>
                  document.addEventListener('DOMContentLoaded', async function () {
                    const activityPurposeField = document.getElementById('activityPurposeField');
                    try {
                      const response = await fetch('http://127.0.0.1:8000/api/requisition-purposes');
                      const data = await response.json();
                      activityPurposeField.innerHTML = '<option selected disabled>Select Activity/Purpose</option>';
                      data.forEach(purpose => {
                        const option = document.createElement('option');
                        option.value = purpose.id; // Assuming the API returns an 'id' field
                        option.textContent = purpose.purpose_name;
                        activityPurposeField.appendChild(option);
                      });
                    } catch (error) {
                      console.error('Error fetching purposes:', error);
                      activityPurposeField.innerHTML = '<option disabled>Error loading purposes</option>';
                    }
                  });
                </script>
              </div>
              <div class="col-md-6">
                <label class="form-label">Endorser Name</label>
                <input type="text" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Date Endorsed</label>
                <input type="date" class="form-control mb-2" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Additional Requests</label>
                <textarea class="form-control mb-2" rows="3"
                  placeholder="Write a brief description of any additional requests you may have."></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label mt-1">Attach Formal Letter</label>
                <div class="position-relative">
                  <input type="file" class="form-control mb-1" id="attachLetter"
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

      // Initialize the form
      initForm();

      async function initForm() {
        try {
          await Promise.all([
            renderSelectedItems(),
            calculateAndDisplayFees()
          ]);

          setupEventListeners();
          startAutoRefresh();
        } catch (error) {
          console.error('Error initializing form:', error);
          showToast('Failed to initialize form', 'error');
        }
      }

          // Setup event listeners for the form
    function setupEventListeners() {
        // Add any form-specific event listeners here
        if (submitBtn) {
            submitBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                // Handle form submission
            });
        }
    }

      // Fetch selected items and render them
      async function renderSelectedItems() {
        try {
          const response = await fetchData('/api/requisition/get-items');
          const items = response.data || [];

          // Render facilities
          renderItemsList(facilityList, items.filter(i => i.type === 'facility'), 'facility');

          // Render equipment
          renderItemsList(equipmentList, items.filter(i => i.type === 'equipment'), 'equipment');

        } catch (error) {
          console.error('Error rendering selected items:', error);
          showToast('Failed to load selected items', 'error');
        }
      }

      // Render items list with remove functionality
      async function renderItemsList(container, items, type) {
        if (!container) return;

        container.innerHTML = '';

        if (items.length === 0) {
          container.innerHTML = `<div class="text-muted empty-message">No ${type}s added yet.</div>`;
          return;
        }

        try {
          // Fetch current data from API
          const response = await fetch(`/api/${type}s`);
          if (!response.ok) throw new Error(`Failed to fetch ${type} data`);
          const apiData = await response.json();

          // Create a map for quick lookup
          const itemsMap = new Map(apiData.data.map(item => [
            item[`${type}_id`],
            item
          ]));

          items.forEach(item => {
            const itemDetails = itemsMap.get(parseInt(item.id));
            if (!itemDetails) return;

            const card = document.createElement('div');
            card.className = 'selected-item-card';

            card.innerHTML = `
                    <div class="selected-item-details">
                        <h6>${itemDetails[`${type}_name`]}</h6>
                        <div class="fee">₱${parseFloat(itemDetails.external_fee).toLocaleString()} per ${itemDetails.rate_type || 'booking'}</div>
                        ${type === 'equipment' ? `
                            <div class="quantity-control">
                                <span class="text-muted">Quantity: ${item.quantity || 1}</span>
                            </div>
                        ` : ''}
                    </div>
                    <button type="button" class="delete-item-btn" onclick="removeSelectedItem('${item.id}', '${type}')">
                        <i class="bi bi-trash"></i>
                    </button>
                `;

            container.appendChild(card);
          });
        } catch (error) {
          console.error(`Error rendering ${type} list:`, error);
          showToast(`Failed to load ${type} details`, 'error');
        }
        // Change item.id to type-specific ID
        const idField = `${type}_id`;
        items.forEach(item => {
          const itemDetails = itemsMap.get(parseInt(item[idField]));
        });
      }

      // Calculate and display fees
      async function calculateAndDisplayFees() {
        if (!feeDisplay) return;

        try {
          // Fetch selected items and their details
          const [itemsResponse, facilitiesResponse, equipmentResponse] = await Promise.all([
            fetchData('/api/requisition/get-items'),
            fetchData('/api/facilities'),
            fetchData('/api/equipment')
          ]);

          const items = itemsResponse.data || [];
          const facilities = facilitiesResponse.data || [];
          const equipment = equipmentResponse.data || [];

          // Create lookup maps
          const facilityMap = new Map(facilities.map(f => [f.facility_id, f]));
          const equipmentMap = new Map(equipment.map(e => [e.equipment_id, e]));

          let htmlContent = '<div class="fee-items">';
          let facilityTotal = 0;
          let equipmentTotal = 0;

          // Facilities breakdown
          const facilityItems = items.filter(i => i.type === 'facility');
          if (facilityItems.length > 0) {
            htmlContent += '<div class="fee-section"><h6 class="mb-3">Facilities</h6>';

            facilityItems.forEach(item => {
              const facility = facilityMap.get(parseInt(item.id));
              if (!facility) return;

              const fee = parseFloat(facility.external_fee);
              facilityTotal += fee;

              htmlContent += `
                        <div class="fee-item d-flex justify-content-between mb-2">
                            <span>${facility.facility_name}</span>
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
              const equip = equipmentMap.get(parseInt(item.id));
              if (!equip) return;

              const unitFee = parseFloat(equip.external_fee);
              const quantity = item.quantity || 1;
              const itemTotal = unitFee * quantity;
              equipmentTotal += itemTotal;

              htmlContent += `
                        <div class="fee-item d-flex justify-content-between mb-2">
                            <span>${equip.equipment_name} ${quantity > 1 ? `(x${quantity})` : ''}</span>
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
          feeDisplay.innerHTML = '<div class="alert alert-danger">Error loading fee breakdown</div>';
        }
      }

      // Remove item from selection
      async function removeSelectedItem(id, type) {
        try {
          const response = await fetchData('/api/requisition/remove-item', {
            method: 'POST',
            body: JSON.stringify({
              type: type,
              [`${type}_id`]: parseInt(id) // Use type-specific field name
            })
          });

          if (!response.success) {
            throw new Error('Failed to remove item');
          }

          await Promise.all([
            renderSelectedItems(),
            calculateAndDisplayFees()
          ]);

          // Trigger storage event for cross-page sync
          localStorage.setItem('formUpdated', Date.now().toString());

          showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} removed successfully`, 'success');
        } catch (error) {
          console.error('Error removing item:', error);
          showToast('Failed to remove item', 'error');
        }
      }

      // Auto-refresh data
      function startAutoRefresh() {
        setInterval(async () => {
          try {
            await Promise.all([
              renderSelectedItems(),
              calculateAndDisplayFees()
            ]);
          } catch (error) {
            console.error('Error refreshing data:', error);
          }
        }, 5000); // Refresh every 5 seconds
      }

      // Listen for changes from catalog pages
      window.addEventListener('storage', async (e) => {
        if (e.key === 'formUpdated') {
          await Promise.all([
            renderSelectedItems(),
            calculateAndDisplayFees()
          ]);
        }
      });

      // Utility function to fetch data
      async function fetchData(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(url, {
          ...options,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            ...(options.headers || {}),
          },
          credentials: 'same-origin'
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
      }

      // Show toast notifications
      function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3`;
        toast.style.zIndex = '1100';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
          toast.remove();
        });
      }
    });
  </script>
</body>

</html>