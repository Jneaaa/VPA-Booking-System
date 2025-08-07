<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>
    Central Philippine University - Equipment and Facility Booking Services
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

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('booking-catalog') ? 'active' : '' }}"
                        href="{{ url('booking-catalog') }}">Booking Catalog</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('your-bookings') ? 'active' : '' }}"
                        href="{{ url('your-bookings') }}">Your Bookings</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('policies') ? 'active' : '' }}"
                        href="{{ url('policies') }}">Reservation Policies</a>
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

            <a href="{{ url('booking-catalog') }}" class="btn btn-book-now ms-lg-3">Back To Catalog</a>
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
  {{-- Remove all inline JS related to reservation form logic --}}
  {{-- Reference reservation-form.js --}}
  <script src="{{ asset('js/public/reservation-form.js') }}"></script>
</body>
</html>