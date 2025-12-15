<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>
    Reservation Form Submission
  </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"
    rel="stylesheet" />
  <style>
    .endorser-tooltip {
      font-size: 0.9rem;
      opacity: 0.75;
      cursor: pointer;
    }

    .endorser-tooltip:hover {
      opacity: 1;
    }

    .btn-outline-primary {
      border: none !important;
      background: transparent !important;
      color: #003366 !important;
    }

    .btn-outline-primary:hover {
      background: #e6e6e6 !important;
      color: #003366 !important;
    }


    /* Loading Spinner Styles */
    .loading-spinner {
      display: inline-block;
      width: 1rem;
      height: 1rem;
      border: 2px solid rgba(0, 0, 0, 0.1);
      border-radius: 50%;
      border-top-color: #0d4581ff;
      animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .loading-overlay {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      color: #6c757d;
    }

    .loading-overlay .loading-spinner {
      width: 2rem;
      height: 2rem;
      margin-bottom: 1rem;
    }

    .fee-loading {
      min-height: 100px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .selected-items-loading {
      min-height: 150px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Conflict Modal Styles */
    #conflictModal .modal-header {
      background-color: var(--cpu-primary) !important;
    }

    #conflictModal .modal-title {
      color: white !important;
      font-weight: 600;
    }

    #conflictModal .modal-body {
      max-height: 60vh;
      /* controls modal size */
      overflow-y: auto;
      /* scroll inside */
      overflow-x: hidden;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
      margin-bottom: 5px;
    }

    .summary-item:last-child {
      border-bottom: none;
    }

    .fee-breakdown-section {
      display: none;
    }

    label.required::after {
      content: " *";
      color: red;
      margin-left: 2px;
      font-weight: bold;
    }

    .selected-items-container .empty-message {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      min-height: 200px;
      margin: 0;
      flex-grow: 1;
    }


    body {
      font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif;
    }

    #termsModal .modal-header {
      padding-top: 0.5rem;
      padding-bottom: 0.25rem;
    }


    #termsModal .modal-title {
      margin-top: 0.55rem;
      justify-content: center;
      position: relative;
    }


    .modal-header .btn-close {
      position: absolute;
      right: 1rem;
      top: 0.75rem;
    }

    /* Add this to your existing CSS */
    #school_id:disabled {
      background-color: #f8f9fa;
      color: #6c757d;
      cursor: not-allowed;
    }

    #confirmSubmitBtn .btn-text {
      display: inline-block;
    }

    #confirmSubmitBtn .btn-loading {
      display: none;
    }

    #confirmSubmitBtn.loading .btn-text {
      display: none;
    }

    #confirmSubmitBtn.loading .btn-loading {
      display: inline-block;
    }

    .modal-header {
      border-bottom: none !important;
    }

    /* Center header visually with modal body */
    .modal-title {
      margin: 0 auto;
      font-weight: 600;
    }

    /* Add spacing and highlight for each term number */
    .terms-content ol li {
      margin-bottom: 1rem;
    }

    .terms-content ol li strong {
      color: var(--bs-primary);
      display: block;
      margin-bottom: 0.25rem;
    }

    /* Make all scrollbars thin and subtle across browsers */
    * {
      scrollbar-width: thin;
      /* Firefox */
      scrollbar-color: #adb5bd transparent;
    }

    *::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }

    *::-webkit-scrollbar-track {
      background: transparent;
    }

    *::-webkit-scrollbar-thumb {
      background-color: #adb5bd;
      border-radius: 10px;
    }

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

    .btn-outline-secondary {
      background-color: #ffdc95;
      color: #aa7400;
      border-color: transparent;
      box-shadow: none;
    }

    .btn-outline-secondary:hover,
    .btn-outline-secondary:focus {
      background-color: #f5c26a;
      color: #7a5500;
      border-color: transparent;
      box-shadow: none;
    }

    .btn-outline-secondary:active,
    .btn-outline-secondary.active,
    .btn-outline-secondary:focus:not(:hover) {
      background-color: #e0aa3d !important;
      /* deeper gold/orange */
      color: #5a3900 !important;
      /* even darker text for contrast */
      border-color: transparent !important;
      box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
      /* subtle pressed effect */
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
              color: #6c757d;
              transition: transform 0.25s ease-in-out;
            }

            button.btn-transparent[aria-expanded="true"] i.bi-chevron-down {
              transform: rotate(0deg);
            }

            button.btn-transparent[aria-expanded="false"] i.bi-chevron-down {
              transform: rotate(180deg);
            }

            .step-section {
              display: none;
            }

            .step-section.active {
              display: block;
            }

            .navigation-buttons {
              display: flex;
              justify-content: space-between;
              padding: 15px;
              background-color: #fff;
              border: 1px solid #dee2e6;
              border-radius: 0.25rem;
              box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
              margin-bottom: 1rem;
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
              <div class="d-flex justify-content-start gap-2">
                <a href="policies" class="btn btn-primary">Reservation Policies</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Step 1: Requested Items & Booking Schedule -->
      <div class="step-section active" id="step1">
        <div class="row">
          <div class="col-md-6">
            <div class="form-section-card" style="height: 400px; overflow-y: auto;">

              <!-- Requested Facilities -->
              <!-- Requested Facilities -->
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Requested Facilities</h5>
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" type="button"
                  onclick="navigateToCatalog('facility')">
                  <i class="bi bi-plus"></i>
                  <span>Add item</span>
                </button>
              </div>
              <div id="facilityList" class="selected-items-container mb-3">
                <!-- Facility items will be dynamically added here -->
                <div class="text-muted empty-message">No facilities added yet.</div>
              </div>

              <!-- Requested Equipment -->
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Requested Equipment</h5>
                <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" type="button"
                  onclick="navigateToCatalog('equipment')">
                  <i class="bi bi-plus"></i>
                  <span>Add item</span>
                </button>
              </div>
              <div id="equipmentList" class="selected-items-container">
                <!-- Equipment items will be dynamically added here -->
                <div class="text-muted empty-message">No equipment added yet.</div>
              </div>
            </div>
          </div>


          <div class="col-md-6">
            <div class="form-section-card flex-grow-1" style="height: 400px; overflow-y: auto; padding-bottom: 15px;">
              <div class="d-flex justify-content-between align-items-center">
                <h5>Step 1: Booking Schedule</h5>
              </div>
              <p id="selectedDateTime" class="text-muted">
                Add items to form first in order to check schedule availability.
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
                <button id="checkAvailabilityBtn" type="button" class="btn btn-primary" onclick="checkAvailability()">
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

        <!-- Navigation Buttons for Step 1 -->
        <div class="navigation-buttons">
          <button type="button" class="btn btn-secondary" disabled>Previous</button>
          <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next</button>
        </div>
      </div>

      <!-- Step 2: Contact Information & Reservation Details -->
      <div class="step-section" id="step2">
        <div class="row">
          <div class="col-md-6 d-flex flex-column">
            <div class="form-section-card flex-grow-1" style="height: 485px;">
              <h5>Step 2: Your Contact Information</h5>
              <div class="row">
                <div class="col-md-12">
                  <label class="form-label">Applicant Type <span style="color: red;">*</span></label>
                  <select id="applicantType" name="user_type" class="form-select mb-2" aria-label="Type of Applicant"
                    required>
                    <option value="" selected disabled>Type of Applicant</option>
                    <option value="Internal">Internal</option>
                    <option value="External">External</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">
                    First Name <span style="color: red;">*</span>
                  </label>
                  <input name="first_name" type="text" class="form-control" placeholder="First Name" required
                    maxlength="50" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">
                    Last Name <span style="color: red;">*</span>
                  </label>
                  <input name="last_name" type="text" class="form-control" placeholder="Last Name" required
                    maxlength="50" />
                </div>
                <div id="studentIdField" class="col-md-6">
                  <label class="form-label">CPU School ID <span id="schoolIdRequired"
                      style="color:red;display:none">*</span></label>
                  <input name="school_id" id="school_id" type="text" class="form-control" placeholder="School ID"
                    maxlength="20" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Contact Number</label>
                  <input name="contact_number" type="text" class="form-control" placeholder="Contact Number"
                    maxlength="15" pattern="\d{1,15}" inputmode="numeric" id="contactNumberField" autocomplete="off" />
                </div>
                <div class="col-md-12">
                  <label class="form-label">Email Address <span style="color: red;">*</span></label>
                  <input name="email" type="email" class="form-control mb-2" placeholder="Email Address" required
                    maxlength="100" />
                </div>
                <div class="col-md-12">
                  <label class="form-label">Organization Name</label>
                  <input name="organization_name" type="text" class="form-control mb-2" placeholder="Organization Name"
                    maxlength="100" />
                </div>
              </div>
            </div>
          </div>

          <div class="col-6">
            <div class="form-section-card" style="height: 485px;">
              <h5>Step 3: Reservation Details</h5>
              <div class="row align-items-end g-2">
                <div class="col-md-6">
                  <label class="form-label required">Activity/Purpose</label>
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
                  <label class="form-label mt-1">Attach Formal Letter</label>
                  <div class="position-relative">
                    <input type="file" class="form-control mb-1" id="attachLetter" onchange="uploadToCloudinary(this)"
                      required />
                    <input type="hidden" name="formal_letter_url" id="formal_letter_url">
                    <input type="hidden" name="formal_letter_public_id" id="formal_letter_public_id">
                    <button type="button" id="removeAttachLetterBtn"
                      class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none"
                      style="color: black; background: none; border: none"
                      onclick="removeFile('attachLetter', 'removeAttachLetterBtn')">
                      x
                    </button>
                  </div>
                  <div id="uploadProgress" class="progress mt-2 d-none">
                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                  </div>
                </div>

                <div class="col-4">
                  <label class="form-label required">No. of Participants</label>
                  <input name="num_participants" type="number" class="form-control mb-2" value="1" min="1" required />
                </div>
                <div class="col-4">
                  <label class="form-label required">No. of Chairs</label>
                  <input name="num_chairs" type="number" class="form-control mb-2" value="0" min="0" required />
                </div>
                <div class="col-4">
                  <label class="form-label required">No. of Tables</label>
                  <input name="num_tables" type="number" class="form-control mb-2" value="0" min="0" required />
                </div>

                <div class="col-md-6">
                  <label class="form-label d-flex align-items-center">
                    Endorser Name
                    <i class="bi bi-question-circle ms-1 endorser-tooltip" data-bs-toggle="tooltip"
                      data-bs-placement="right" data-bs-custom-class="custom-tooltip"
                      title="Provide the name of the individual who endorsed or authorized your planned event."></i>
                  </label>

                  <input name="endorser" type="text" class="form-control" placeholder="Endorser Name" maxlength="50" />
                </div>


                <div class="col-md-6">
                  <label class="form-label">Date Endorsed</label>
                  <input name="date_endorsed" type="date" class="form-control mb-2" style="padding-top: 0.5rem;" />
                </div>
                <div class="col-md-12">
                  <label class="form-label">Additional Requests</label>
                  <textarea name="additional_requests" class="form-control mb-2" rows="4" maxlength="250"
                    placeholder="Write a brief description of any additional requests you may have (e.g., WiFi, special seating arrangement, security personnel, technical support, logistics, etc.)."
                    style="padding-top: 0.70rem;"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Buttons for Step 2 -->
        <div class="navigation-buttons">
          <button type="button" class="btn btn-secondary" onclick="previousStep(1)">Previous</button>
          <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next</button>
        </div>
      </div>

      <!-- Step 3: Form Summary -->
      <div class="step-section" id="step3">
        <div class="row">
          <div class="col-12">
            <div class="form-section-card">
              <h5 class="fw-bold text-center">Requisition Summary</h5>
              <small class="d-block text-center text-muted" style="margin-bottom: 2rem;">
                Please review all information carefully. Submitted requests cannot be edited.
              </small>
              <div class="row">
                <!-- Contact Information Summary -->
                <div class="col-md-6">
                  <h6 class="border-bottom pb-2">Contact Information</h6>
                  <div class="summary-item">
                    <strong>Applicant Type:</strong>
                    <span id="summary-applicant-type"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Name:</strong>
                    <span id="summary-name"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Email:</strong>
                    <span id="summary-email"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Contact Number:</strong>
                    <span id="summary-contact"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Organization:</strong>
                    <span id="summary-organization"></span>
                  </div>
                  <div class="summary-item" id="summary-school-id-container">
                    <strong>School ID:</strong>
                    <span id="summary-school-id"></span>
                  </div>
                </div>

                <!-- Reservation Details Summary -->
                <div class="col-md-6">
                  <h6 class="border-bottom pb-2">Reservation Details</h6>
                  <div class="summary-item">
                    <strong>Activity/Purpose:</strong>
                    <span id="summary-purpose"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Start Date & Time:</strong>
                    <span id="summary-start"></span>
                  </div>
                  <div class="summary-item">
                    <strong>End Date & Time:</strong>
                    <span id="summary-end"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Participants:</strong>
                    <span id="summary-participants"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Chairs/Tables:</strong>
                    <span id="summary-furniture"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Endorser:</strong>
                    <span id="summary-endorser"></span>
                  </div>
                  <div class="summary-item">
                    <strong>Additional Requests:</strong>
                    <span id="summary-requests"></span>
                  </div>
                </div>
              </div>

              <!-- Selected Items and Fee Breakdown in the same row -->
              <div class="row">
                <!-- Fee Breakdown Summary (Right Column) -->
                <div class="col-12">
                  <h6 class="border-bottom pb-2">Fee Breakdown</h6>
                  <div id="summary-fees">
                    <!-- Fees will be dynamically populated -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Buttons for Step 3 -->
        <div class="navigation-buttons">
          <button type="button" class="btn btn-secondary" onclick="previousStep(2)">Previous</button>
          <button type="button" class="btn btn-primary" onclick="openTermsModal(event)">Submit Form</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="padding: 0.25rem 1rem;">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            style="width: 1rem; height: 1rem; margin-top: 0.2rem;"></button>
        </div>
        <div class="modal-body text-center">
          <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
          <h5 class="mt-3">Request Submitted Successfully!</h5>
          <small class="text-muted d-block mt-2">
            A confirmation email has been sent to <span id="userEmail" class="fw-bold"></span>.
          </small>
          <small class="text-muted d-block">
            Please monitor your email for updates on your request status.
          </small>
          <p id="successDetails" class="text-muted mt-3 mb-0"></p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-primary" onclick="window.location.href='{{ asset('index') }}'">
            Back to Home
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Conflict Items Modal -->
  <div class="modal fade" id="conflictModal" tabindex="-1" aria-labelledby="conflictModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="conflictModalLabel">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Scheduling Conflict Detected
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-3">
            The selected time slot conflicts with the following items that are already booked:
          </p>

          <div id="conflictItemsList" class="mb-3">
            <!-- Conflict items will be dynamically inserted here -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Close
          </button>
          <button type="button" class="btn btn-primary"
            onclick="window.location.href='{{ asset("facility-catalog") }}'">
            View Catalog
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content text-center">
        <div class="modal-header">
          <h5 class="modal-title text-primary mb-0" id="termsModalLabel">Terms and Conditions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            style="width: 1rem; height: 1rem; margin-top: 0.2rem;"></button>
        </div>



        <div class="modal-body">
          <div class="terms-content mb-0 mx-auto" style="max-height: 50vh; overflow-y: auto; text-align: center;">
            <small class="d-block text-start mb-3" style="padding-left: 17px;">
              By using our booking service, you agree to comply with the following terms and conditions, as well as all
              campus policies set by Central Philippine University (CPU):
            </small>


            <ol class="text-start">
              <li>
                <strong class="text-primary">Approval Process</strong>
                <small>All booking requests are subject to review and approval by the CPU Administration. Submission of
                  a
                  requisition form does not guarantee approval.</small>
              </li>

              <li>
                <strong class="text-primary">Confirmation and Payment</strong>
                <small>
                  Requesters will receive a confirmation email after submitting their form. Once the booking has been
                  reviewed and approved, a follow-up notification will be sent containing finalized booking details and
                  payment instructions. All payments must be settled
                  <span class="fw-bold">in person</span> at the CPU Business Office within
                  <span class="fw-bold">three (3) business days</span> after approval.
                </small>
              </li>

              <li>
                <strong class="text-primary">Cancellations</strong>
                <small>
                  Requesters may cancel their booking <span class="fw-bold">up to five (5) days before the scheduled
                    event</span>
                  through the system using the access code provided via email after submission. Cancellations made
                  beyond
                  this period may not be honored.
                </small>
              </li>


              <li>
                <strong class="text-primary">Facility and Equipment Responsibility</strong>
                <small>Requesters are responsible for the proper use and care of all facilities and equipment. Any
                  damage,
                  loss, or misuse may incur corresponding repair or replacement fees.</small>
              </li>

              <li>
                <strong class="text-primary">Return Policy and Penalties</strong>
                <small>
                  All borrowed equipment must be returned within the specified booking period. A
                  <span class="fw-bold">grace period of up to 4 hours</span> after the event may be allowed for clean-up
                  or coordination.
                  Failure to return items within this timeframe may result in <span class="fw-bold">late penalty
                    fees</span> or temporary suspension of booking privileges.
                </small>
              </li>



              <li>
                <strong class="text-primary">Prohibited Acts</strong>
                <small> Alcohol consumption and smoking are strictly prohibited within the campus premises. External
                  users must
                  present valid identification when required. </small>
              </li>

              <li>
                <strong class="text-primary">Administrative Rights</strong>
                <small> CPU reserves the right to cancel or revoke bookings for policy violations or non-compliance with
                  these
                  terms and conditions. </small>
              </li>
            </ol>
            <div class="form-check mt-4 d-flex justify-content-center">
              <input class="form-check-input me-2" type="checkbox" id="agreeTerms">
              <label class="form-check-label" for="agreeTerms">
                I have read and agree to the terms and conditions.
              </label>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-end">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmSubmitBtn" class="btn btn-primary" disabled>
            <span class="btn-text">Accept & Submit</span>
            <span class="btn-loading">
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>
              Submitting Request...
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>


  <footer class="footer-container">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Central Philippine University | All Rights Reserved</p>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ========== GLOBAL VARIABLES ==========
    let currentStep = 1;
    const facilityList = document.getElementById('facilityList');
    const equipmentList = document.getElementById('equipmentList');
    const feeDisplay = document.getElementById('feeDisplay');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;


    // Ensure the form always starts at Step 1 when page loads
    window.addEventListener('load', function () {
      showStep(1);
    });

    // ========== LOCAL STORAGE AUTO-SAVE ==========
    class LocalStorageAutoSave {
      constructor(options = {}) {
        // Configuration
        this.formSelector = options.formSelector || '#reservationForm';
        this.formId = options.formId || 'reservation_form_' + window.location.pathname.replace(/\//g, '_');
        this.saveInterval = options.saveInterval || 2000; // 2 seconds
        this.excludedFields = options.excludedFields || ['password', 'confirm_password', 'token', '_token', 'csrf_token'];

        // State
        this.debounceTimer = null;
        this.isSaving = false;
        this.storageKey = `reservation_form_data_${this.formId}`;

        this.init();
      }

      init() {
        // Get form element
        this.form = document.querySelector(this.formSelector);
        if (!this.form) {
          console.warn('Form not found with selector:', this.formSelector);
          return;
        }

        // Check if form was recently submitted (using sessionStorage)
        const recentlySubmitted = sessionStorage.getItem('form_recently_submitted');

        if (recentlySubmitted === 'true') {
          // Form was recently submitted, clear everything and start fresh
          sessionStorage.removeItem('form_recently_submitted');
          localStorage.removeItem(this.storageKey);
          showStep(1);
          console.log('Starting fresh (recent submission detected)');
        } else {
          // Normal behavior: restore saved data
          setTimeout(() => this.restoreProgress(), 100);
        }

        // Set up event listeners
        this.setupEventListeners();

        // Auto-save on page unload
        window.addEventListener('beforeunload', () => {
          this.saveProgress(true);
        });

        // Periodically save (as backup)
        setInterval(() => {
          this.saveProgress();
        }, this.saveInterval * 2);
      }

      setupEventListeners() {
        // Listen to all form inputs
        const saveHandler = (e) => {
          if (this.shouldExcludeField(e.target)) return;
          this.debouncedSave();
        };

        this.form.addEventListener('input', saveHandler);
        this.form.addEventListener('change', saveHandler);

        // Handle special cases
        this.form.addEventListener('blur', saveHandler, true);
      }

      shouldExcludeField(field) {
        if (!field) return false;

        const fieldName = (field.name || '').toLowerCase();
        const fieldType = (field.type || '').toLowerCase();

        return this.excludedFields.some(excluded =>
          fieldName.includes(excluded) ||
          fieldType.includes('password') ||
          fieldType.includes('file')
        );
      }

      debouncedSave() {
        if (this.debounceTimer) {
          clearTimeout(this.debounceTimer);
        }

        this.debounceTimer = setTimeout(() => {
          this.saveProgress();
        }, 500); // 500ms debounce
      }

      getFormData() {
        const data = {};

        // Get all form elements
        const elements = this.form.querySelectorAll('input, select, textarea, .selected-items-container');

        elements.forEach(element => {
          if (this.shouldExcludeField(element)) return;

          const name = element.name;
          if (!name) return;

          const type = element.type || 'text';
          const tagName = element.tagName.toLowerCase();

          if (tagName === 'input') {
            if (type === 'checkbox' || type === 'radio') {
              // Handle checkboxes and radios
              const checkboxes = this.form.querySelectorAll(`[name="${name}"]`);
              if (checkboxes.length > 1) {
                data[name] = [];
                checkboxes.forEach(cb => {
                  if (cb.checked) {
                    data[name].push(cb.value);
                  }
                });
              } else {
                data[name] = element.checked ? element.value : '';
              }
            } else {
              data[name] = element.value;
            }
          } else if (tagName === 'select') {
            if (element.multiple) {
              data[name] = Array.from(element.selectedOptions).map(option => option.value);
            } else {
              data[name] = element.value;
            }
          } else if (tagName === 'textarea') {
            data[name] = element.value;
          }
        });

        // Save current step
        data._currentStep = currentStep;
        data._timestamp = new Date().toISOString();

        return data;
      }

      saveProgress(immediate = false) {
        if (this.isSaving) return;

        this.isSaving = true;
        const formData = this.getFormData();

        try {
          // Save to localStorage
          localStorage.setItem(this.storageKey, JSON.stringify(formData));

          return true;
        } catch (error) {
          console.error('Error saving to localStorage:', error);
          return false;
        } finally {
          this.isSaving = false;
        }
      }

      restoreProgress() {
        try {
          const savedData = localStorage.getItem(this.storageKey);
          if (!savedData) return false;

          const data = JSON.parse(savedData);

          if (!data || Object.keys(data).length === 0) {
            return false;
          }

          // Restore form data (excluding metadata)
          Object.entries(data).forEach(([fieldName, value]) => {
            if (fieldName.startsWith('_')) return;

            const elements = this.form.querySelectorAll(`[name="${fieldName}"]`);

            if (elements.length === 0) return;

            elements.forEach(element => {
              const type = element.type || 'text';
              const tagName = element.tagName.toLowerCase();

              if (tagName === 'input') {
                if (type === 'checkbox' || type === 'radio') {
                  if (Array.isArray(value)) {
                    element.checked = value.includes(element.value);
                  } else {
                    element.checked = element.value == value;
                  }
                } else {
                  element.value = value || '';
                }
              } else if (tagName === 'select') {
                if (element.multiple && Array.isArray(value)) {
                  Array.from(element.options).forEach(option => {
                    option.selected = value.includes(option.value);
                  });
                } else {
                  element.value = value || '';
                }
              } else if (tagName === 'textarea') {
                element.value = value || '';
              }
            });
          });

          // Show restore notification
          this.showRestoreNotification();

          return true;
        } catch (error) {
          console.error('Error restoring from localStorage:', error);
          return false;
        }
      }

      clearProgress() {
        try {
          localStorage.removeItem(this.storageKey);
          this.form.reset();

          // Reset to step 1
          currentStep = 1;
          showStep(1);

          return true;
        } catch (error) {
          console.error('Error clearing localStorage:', error);
          return false;
        }
      }

      showRestoreNotification() {
        const notification = document.createElement('div');
        notification.id = 'restore-notification';
        notification.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: #2196F3;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        `;

        const savedData = localStorage.getItem(this.storageKey);
        const data = savedData ? JSON.parse(savedData) : null;
        const timeText = data && data._timestamp ?
          `Last saved: ${new Date(data._timestamp).toLocaleString()}` : '';

        showToast(`Draft restored<br><small>${timeText}</small>`, 'success', 5000);


        notification.querySelector('button').addEventListener('click', () => {
          notification.remove();
        });

        document.body.appendChild(notification);

        setTimeout(() => {
          if (notification.parentElement) {
            notification.remove();
          }
        }, 5000);
      }
    }

    // ========== STEP NAVIGATION FUNCTIONS ==========
    function showStep(stepNumber) {
      document.querySelectorAll('.step-section').forEach(section => {
        section.classList.remove('active');
      });

      const stepElement = document.getElementById('step' + stepNumber);
      if (stepElement) {
        stepElement.classList.add('active');
      }

      currentStep = stepNumber;
    }

    function nextStep(nextStepNumber) {
      if (nextStepNumber === 2 && !validateStep1()) {
        return;
      }

      if (nextStepNumber === 3 && !validateStep2()) {
        return;
      }

      if (nextStepNumber === 3) {
        populateFormSummary();
      }
      showStep(nextStepNumber);
    }

    function previousStep(prevStepNumber) {
      const reservationForm = document.getElementById('reservationForm');
      reservationForm.querySelectorAll('.is-invalid').forEach(input => clearFieldError(input));
      showStep(prevStepNumber);
    }

    // ========== VALIDATION FUNCTIONS ==========
    function validateStep1() {
      const startDate = document.getElementById('startDateField').value;
      const endDate = document.getElementById('endDateField').value;
      const startTime = document.getElementById('startTimeField').value;
      const endTime = document.getElementById('endTimeField').value;

      const errors = [];

      if (!startDate) errors.push('Start Date is required');
      if (!endDate) errors.push('End Date is required');
      if (!startTime) errors.push('Start Time is required');
      if (!endTime) errors.push('End Time is required');

      if (errors.length > 0) {
        showToast('Please complete the booking schedule:\n• ' + errors.join('\n• '), 'error');
        return false;
      }

      if (endDate < startDate) {
        showToast('End date cannot be before start date.', 'error');
        return false;
      }

      if (startDate === endDate) {
        const startDateTime = new Date(`${startDate}T${convertTo24Hour(startTime)}:00`);
        const endDateTime = new Date(`${endDate}T${convertTo24Hour(endTime)}:00`);

        if (endDateTime <= startDateTime) {
          showToast('End time must be after start time when using the same date.', 'error');
          return false;
        }
      }

      return true;
    }

    function validateStep2() {
      const reservationForm = document.getElementById('reservationForm');
      let valid = true;
      let firstInvalid = null;

      // Add num_chairs and num_tables to required fields
      const requiredFields = [
        'user_type', 'first_name', 'last_name', 'email', 'num_participants', 'purpose_id', 'num_chairs', 'num_tables'
      ];

      // Clear existing errors
      reservationForm.querySelectorAll('.is-invalid').forEach(input => clearFieldError(input));

      // Validate required fields
      requiredFields.forEach(name => {
        const input = reservationForm.querySelector(`[name="${name}"]`);
        if (input) {
          // Special handling for numeric fields that should be >= 0
          if (name === 'num_chairs' || name === 'num_tables') {
            if (input.value === '' || input.value === null || parseInt(input.value) < 0) {
              showFieldError(input, 'Please enter a valid number (0 or greater).');
              valid = false;
              if (!firstInvalid) firstInvalid = input;
            }
          }
          else if (!input.value ||
            (name === 'user_type' && input.value === '') ||
            (name === 'purpose_id' && (input.value === '' || input.value === null))) {
            showFieldError(input, 'Please fill in this field.');
            valid = false;
            if (!firstInvalid) firstInvalid = input;
          }
        }
      });

      // School ID validation for Internal applicants
      const applicantType = document.getElementById('applicantType');
      const schoolIdInput = document.getElementById('school_id');
      if (applicantType.value === 'Internal') {
        clearFieldError(schoolIdInput);
        if (!schoolIdInput.value) {
          showFieldError(schoolIdInput, 'Please fill in this field.');
          valid = false;
          if (!firstInvalid) firstInvalid = schoolIdInput;
        }
      } else {
        clearFieldError(schoolIdInput);
      }

      // Email format validation
      const emailInput = reservationForm.querySelector('[name="email"]');
      if (emailInput && emailInput.value) {
        clearFieldError(emailInput);
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value)) {
          showFieldError(emailInput, 'Please enter a valid email address.');
          valid = false;
          if (!firstInvalid) firstInvalid = emailInput;
        }
      }

      // Contact number validation
      const contactNumberField = document.getElementById('contactNumberField');
      if (contactNumberField && contactNumberField.value) {
        clearFieldError(contactNumberField);
        if (!/^\d{1,15}$/.test(contactNumberField.value)) {
          showFieldError(contactNumberField, 'Contact number must be numbers only (max 15 digits).');
          valid = false;
          if (!firstInvalid) firstInvalid = contactNumberField;
        }
      } else {
        clearFieldError(contactNumberField);
      }

      // Additional validation for numeric fields to ensure they're not negative
      const numChairsInput = document.querySelector('input[name="num_chairs"]');
      const numTablesInput = document.querySelector('input[name="num_tables"]');

      if (numChairsInput && numChairsInput.value !== '' && parseInt(numChairsInput.value) < 0) {
        showFieldError(numChairsInput, 'Number of chairs cannot be negative.');
        valid = false;
        if (!firstInvalid) firstInvalid = numChairsInput;
      }

      if (numTablesInput && numTablesInput.value !== '' && parseInt(numTablesInput.value) < 0) {
        showFieldError(numTablesInput, 'Number of tables cannot be negative.');
        valid = false;
        if (!firstInvalid) firstInvalid = numTablesInput;
      }

      // Scroll to first invalid field
      if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalid.focus();
      }

      return valid;
    }

    // ========== HELPER FUNCTIONS ==========
    function showFieldError(input, message) {
      input.classList.add('is-invalid');
      input.setAttribute('title', message);
      input.setAttribute('data-bs-toggle', 'tooltip');
      input.setAttribute('data-bs-placement', 'top');

      if (window.bootstrap) {
        new bootstrap.Tooltip(input);
        input.addEventListener('focus', function () {
          bootstrap.Tooltip.getInstance(input)?.show();
        });
      }
    }

    function clearFieldError(input) {
      input.classList.remove('is-invalid');
      input.removeAttribute('title');
      input.removeAttribute('data-bs-toggle');
      input.removeAttribute('data-bs-placement');

      if (window.bootstrap) {
        const tooltip = bootstrap.Tooltip.getInstance(input);
        if (tooltip) tooltip.dispose();
      }
    }

    window.convertTo24Hour = function (time12h) {
      if (!time12h) return '';

      if (time12h.includes(':')) {
        const [timePart, modifier] = time12h.split(' ');
        if (!modifier) return timePart;

        let [hours, minutes] = timePart.split(':');
        hours = parseInt(hours, 10);

        if (modifier === 'PM' && hours !== 12) {
          hours += 12;
        } else if (modifier === 'AM' && hours === 12) {
          hours = 0;
        }

        return `${hours.toString().padStart(2, '0')}:${minutes}`;
      }

      return time12h;
    };

    // ========== FORM SUMMARY FUNCTIONS ==========
    function populateFormSummary() {
      // Contact Information
      const applicantType = document.getElementById('applicantType');
      const firstName = document.querySelector('input[name="first_name"]');
      const lastName = document.querySelector('input[name="last_name"]');
      const email = document.querySelector('input[name="email"]');
      const contactNumber = document.querySelector('input[name="contact_number"]');
      const organizationName = document.querySelector('input[name="organization_name"]');
      const schoolId = document.getElementById('school_id');

      document.getElementById('summary-applicant-type').textContent = applicantType.value || 'Not specified';
      document.getElementById('summary-name').textContent = `${firstName.value || ''} ${lastName.value || ''}`.trim() || 'Not specified';
      document.getElementById('summary-email').textContent = email.value || 'Not specified';
      document.getElementById('summary-contact').textContent = contactNumber.value || 'Not specified';
      document.getElementById('summary-organization').textContent = organizationName.value || 'Not specified';

      const schoolIdContainer = document.getElementById('summary-school-id-container');
      if (applicantType.value === 'Internal' && schoolId.value) {
        document.getElementById('summary-school-id').textContent = schoolId.value;
        schoolIdContainer.style.display = 'flex';
      } else {
        schoolIdContainer.style.display = 'none';
      }

      // Reservation Details
      const purposeSelect = document.getElementById('activityPurposeField');
      const purposeText = purposeSelect.options[purposeSelect.selectedIndex]?.text || 'Not specified';
      const startDate = document.getElementById('startDateField').value;
      const endDate = document.getElementById('endDateField').value;
      const startTime = document.getElementById('startTimeField').value;
      const endTime = document.getElementById('endTimeField').value;
      const numParticipants = document.querySelector('input[name="num_participants"]');
      const numChairs = document.querySelector('input[name="num_chairs"]');
      const numTables = document.querySelector('input[name="num_tables"]');
      const endorser = document.querySelector('input[name="endorser"]');
      const additionalRequests = document.querySelector('textarea[name="additional_requests"]');

      document.getElementById('summary-purpose').textContent = purposeText;

      // Format dates to "January 1, 2025" format and split into start/end
      const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      };

      if (startDate && startTime) {
        const formattedStartDate = formatDate(startDate);
        document.getElementById('summary-start').textContent = `${formattedStartDate} ${startTime}`;
      } else {
        document.getElementById('summary-start').textContent = 'Not specified';
      }

      if (endDate && endTime) {
        const formattedEndDate = formatDate(endDate);
        document.getElementById('summary-end').textContent = `${formattedEndDate} ${endTime}`;
      } else {
        document.getElementById('summary-end').textContent = 'Not specified';
      }

      document.getElementById('summary-participants').textContent = numParticipants.value || '0';
      document.getElementById('summary-furniture').textContent = `${numChairs.value || '0'} chairs, ${numTables.value || '0'} tables`;
      document.getElementById('summary-endorser').textContent = endorser.value || 'Not specified';
      document.getElementById('summary-requests').textContent = additionalRequests.value || 'None';

      // Generate fee breakdown
      generateFeeBreakdownForSummary();
    }

    // ========== TOAST FUNCTION ==========
    window.showToast = function (message, type = 'success', duration = 3000) {
      const toast = document.createElement('div');
      toast.className = `toast align-items-center border-0 position-fixed start-0 mb-2`;
      toast.style.zIndex = '1100';
      toast.style.bottom = '0';
      toast.style.left = '0';
      toast.style.margin = '1rem';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(20px)';
      toast.style.transition = 'transform 0.4s ease, opacity 0.4s ease';

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

      const bsToast = new bootstrap.Toast(toast, { autohide: false });
      bsToast.show();

      requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
      });

      const loadingBar = toast.querySelector('.loading-bar');
      requestAnimationFrame(() => {
        loadingBar.style.width = '0%';
      });

      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => {
          bsToast.hide();
          toast.remove();
        }, 400);
      }, duration);
    };

    // ========== INITIALIZATION ==========
    document.addEventListener('DOMContentLoaded', function () {

      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      tooltipTriggerList.map(el => new bootstrap.Tooltip(el))

      // Initialize step system
      showStep(1);

      // ========== INITIALIZE LOCAL STORAGE AUTO-SAVE ==========
      window.autoSave = new LocalStorageAutoSave({
        formSelector: '#reservationForm',
        formId: 'reservation_form',
        saveInterval: 2000,
        excludedFields: ['password', '_token', 'csrf_token']
      });

      // Clear localStorage on successful form submission
      window.addEventListener('formSubmitted', () => {
        if (window.autoSave) {
          window.autoSave.clearProgress();
        }
      });

      // Set up applicant type change handler
      const applicantType = document.getElementById('applicantType');
      const schoolIdInput = document.getElementById('school_id');
      const schoolIdRequired = document.getElementById('schoolIdRequired');

      applicantType.addEventListener('change', function () {
        if (this.value === 'Internal') {
          schoolIdInput.required = true;
          schoolIdInput.disabled = false;
          schoolIdRequired.style.display = '';
          schoolIdInput.placeholder = 'School ID';
        } else {
          schoolIdInput.required = false;
          schoolIdInput.disabled = true;
          schoolIdRequired.style.display = 'none';
          schoolIdInput.value = '';
          schoolIdInput.placeholder = 'School ID';
        }
      });

      // Initialize applicant type state
      if (applicantType.value === 'Internal') {
        schoolIdInput.required = true;
        schoolIdInput.disabled = false;
        schoolIdRequired.style.display = '';
      } else {
        schoolIdInput.required = false;
        schoolIdInput.disabled = true;
        schoolIdRequired.style.display = 'none';
        schoolIdInput.value = '';
      }

      // Set up clear selection button
      document.getElementById('clearSelectionBtn').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('startDateField').value = '';
        document.getElementById('endDateField').value = '';
        document.getElementById('startTimeField').selectedIndex = 0;
        document.getElementById('endTimeField').selectedIndex = 0;
        document.getElementById('availabilityResult').textContent = '';
        document.getElementById('availabilityResult').style.color = '';

        if (typeof calculateAndDisplayFees === 'function') {
          calculateAndDisplayFees();
        }

        showToast('Booking schedule cleared successfully', 'success');
      });

      // Prevent non-numeric input in contact number field
      const contactNumberField = document.getElementById('contactNumberField');
      contactNumberField.addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '');
      });

      // Initialize dropdowns
      const dropdownElements = document.querySelectorAll('.dropdown-toggle');
      dropdownElements.forEach(dropdown => {
        new bootstrap.Dropdown(dropdown);
      });

      console.log('Form validation initialized successfully');
    });
    // ========== CATALOG NAVIGATION ==========
    function navigateToCatalog(type) {
      const formData = new FormData(document.getElementById('reservationForm'));
      const formObject = Object.fromEntries(formData.entries());
      sessionStorage.setItem('reservationFormData', JSON.stringify(formObject));

      if (type === 'facility') {
        window.location.href = '{{ asset("facility-catalog") }}';
      } else {
        window.location.href = '{{ asset("equipment-catalog") }}';
      }
    }

    // ========== AVAILABILITY CHECK ==========
    window.checkAvailability = async function () {
      const checkBtn = document.getElementById('checkAvailabilityBtn');
      const originalText = checkBtn.innerHTML;

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const startDate = document.getElementById('startDateField').value;
        const endDate = document.getElementById('endDateField').value;
        const startTime = convertTo24Hour(document.getElementById('startTimeField').value);
        const endTime = convertTo24Hour(document.getElementById('endTimeField').value);

        if (!startDate || !endDate || !startTime || !endTime) {
          showToast('Please select all date and time fields', 'error');
          return;
        }

        if (endDate < startDate) {
          showToast('End date cannot be before start date.', 'error');
          return;
        }

        // Show loading state
        checkBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...';
        checkBtn.disabled = true;

        // Get selected items from session
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

        if (items.length === 0) {
          showToast('Please add items to check availability', 'error');
          checkBtn.innerHTML = originalText;
          checkBtn.disabled = false;
          return;
        }

        // Prepare request data
        const requestData = {
          start_date: startDate,
          end_date: endDate,
          start_time: startTime,
          end_time: endTime,
          items: items.map(item => {
            const itemData = {
              type: item.type
            };
            if (item.type === 'facility') {
              itemData.facility_id = item.facility_id;
            } else {
              itemData.equipment_id = item.equipment_id;
            }
            return itemData;
          })
        };

        // Call API
        const checkResponse = await fetch('/requisition/check-availability', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(requestData)
        });

        if (!checkResponse.ok) {
          let errorData;
          try {
            errorData = await checkResponse.json();
          } catch {
            errorData = {};
          }
          if (errorData.errors) {
            Object.values(errorData.errors).flat().forEach(msg => showToast(msg, 'error'));
          } else if (errorData.message) {
            showToast(errorData.message, 'error');
          } else {
            showToast('Availability check failed', 'error');
          }
          return;
        }

        const result = await checkResponse.json();

        if (!result.success) {
          showToast(result.message || 'Availability check failed', 'error');
          return;
        }

        const availabilityResult = document.getElementById('availabilityResult');
        if (result.data.available) {
          availabilityResult.innerHTML = `
        <span class="text-success">
          <i class="bi bi-check-circle-fill" style="margin-right:5px;"></i>
          Available
        </span>
      `;
          showToast('Time slot is available!', 'success');
        } else {
          availabilityResult.innerHTML = `
        <span class="text-danger">
          <i class="bi bi-x-circle-fill" style="margin-right:5px;"></i>
          Conflict found!
        </span>
      `;

          // Instead of toast, show modal with conflict details
          showConflictModal(result.data.conflict_items);
        }

      } catch (error) {
        console.error('Error checking availability:', error);
        showToast(error.message || 'Failed to check availability', 'error');
      } finally {
        checkBtn.innerHTML = originalText;
        checkBtn.disabled = false;
      }
    };

    // ========== CONFLICT MODAL FUNCTION ==========
    function showConflictModal(conflictItems) {
      // Get modal elements
      const modalElement = document.getElementById('conflictModal');
      const conflictItemsList = document.getElementById('conflictItemsList');

      if (!conflictItemsList) {
        console.error('Conflict items list element not found');
        showToast(`Conflict with: ${conflictItems.map(item => item.name).join(', ')}`, 'error');
        return;
      }

      // Clear previous content
      conflictItemsList.innerHTML = '';

      // Group conflicts by type
      const facilities = conflictItems.filter(item => item.type === 'facility');
      const equipment = conflictItems.filter(item => item.type === 'equipment');

      // Create conflict list HTML
      let htmlContent = '';

      if (facilities.length > 0) {
        htmlContent += `
      <div class="mb-3">
        <ul class="list-group">
        `;

        facilities.forEach(item => {
          htmlContent += `
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>${item.name}</strong>
            <div class="text-muted small">
              <i class="bi bi-calendar-event me-1"></i>
              ${item.conflicting_booking_date || 'Booked'}
            </div>
          </div>
          <span class="badge bg-danger rounded-pill">Facility</span>
        </li>
      `;
        });

        htmlContent += '</ul></div>';
      }

      if (equipment.length > 0) {
        htmlContent += `
      <div class="mb-3">
        <ul class="list-group">
    `;

        equipment.forEach(item => {
          htmlContent += `
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>${item.name}</strong>
            <div class="text-muted small">
              <i class="bi bi-calendar-event me-1"></i>
              ${item.conflicting_booking_date || 'Booked'}
            </div>
          </div>
          <span class="badge bg-danger text-white rounded-pill">Equipment</span>
        </li>
      `;
        });

        htmlContent += '</ul></div>';
      }

      // Add summary
      htmlContent += `
    <div class="alert alert-danger">
      <div class="d-flex">
        <div class="flex-shrink-0">
          <i class="bi bi-exclamation-circle fs-4"></i>
        </div>
        <div class="flex-grow-1 ms-3">
          <h6 class="alert-warning">Total Conflicts: ${conflictItems.length}</h6>
          <p class="mb-0 small">
            <strong>What to do:</strong><br>
            1. Select a different date/time<br>
            2. Remove conflicting items from your selection<br>
            3. Check with facility/equipment administrators
          </p>
        </div>
      </div>
    </div>
  `;

      // Insert into modal
      conflictItemsList.innerHTML = htmlContent;

      // Show the modal
      const modal = new bootstrap.Modal(modalElement);
      modal.show();

      // Add event listener to close button
      const closeBtn = modalElement.querySelector('.btn-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => {
          modal.hide();
        });
      }
    }

    // ========== FEE BREAKDOWN FOR SUMMARY ==========
    async function generateFeeBreakdownForSummary() {
      const summaryFees = document.getElementById('summary-fees');

      if (!summaryFees) return;

      // Show loading state
      summaryFees.innerHTML = `
    <div class="fee-loading">
      <div class="loading-overlay">
        <div class="loading-spinner"></div>
        <small>Calculating fees...</small>
      </div>
    </div>
  `;

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
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

        if (items.length === 0) {
          summaryFees.innerHTML = '<div class="text-muted">No items added yet.</div>';
          return;
        }

        // Get schedule information
        const startDate = document.getElementById('startDateField').value;
        const endDate = document.getElementById('endDateField').value;
        const startTime = document.getElementById('startTimeField').value;
        const endTime = document.getElementById('endTimeField').value;

        // Format dates to "January 1, 2025" format
        let formattedStartDate = '';
        let formattedEndDate = '';

        if (startDate) {
          formattedStartDate = new Date(startDate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
        }

        if (endDate) {
          formattedEndDate = new Date(endDate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
        }

        // Calculate duration in hours
        let durationHours = 0;
        if (startDate && endDate && startTime && endTime) {
          const startDateTime = new Date(`${startDate}T${convertTo24Hour(startTime)}:00`);
          const endDateTime = new Date(`${endDate}T${convertTo24Hour(endTime)}:00`);
          durationHours = (endDateTime - startDateTime) / (1000 * 60 * 60);
          durationHours = Math.max(0, durationHours);
        }

        let facilityTotal = 0;
        let equipmentTotal = 0;
        let htmlContent = '<div class="fee-items">';

        // Facilities breakdown
        const facilityItems = items.filter(i => i.type === 'facility');
        if (facilityItems.length > 0) {
          htmlContent += '<div class="fee-section"><p class="mb-3 text-primary">Facilities</p>';
          facilityItems.forEach(item => {
            let fee = parseFloat(item.external_fee);
            if (item.rate_type === 'Per Hour' && durationHours > 0) {
              fee = fee * durationHours;
              htmlContent += `
                <div class="fee-item d-flex justify-content-between mb-2">
                    <span>${item.name} (${durationHours.toFixed(1)} hrs)</span>
                    <div class="text-end">
                        <small>₱${parseFloat(item.external_fee).toLocaleString()}/hr</small>
                        <div><strong>₱${fee.toLocaleString()}</strong></div>
                    </div>
                </div>
            `;
            } else {
              htmlContent += `
                <div class="fee-item d-flex justify-content-between mb-2">
                    <span>${item.name}</span>
                    <span>₱${fee.toLocaleString()}</span>
                </div>
            `;
            }
            facilityTotal += fee;
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
          htmlContent += '<div class="fee-section mt-3"><p class="mb-3 text-primary">Equipment</p>';
          equipmentItems.forEach(item => {
            let unitFee = parseFloat(item.external_fee);
            const quantity = item.quantity || 1;
            let itemTotal = unitFee * quantity;
            if (item.rate_type === 'Per Hour' && durationHours > 0) {
              itemTotal = itemTotal * durationHours;
              htmlContent += `
                <div class="fee-item d-flex justify-content-between mb-2">
                    <span>${item.name} ${quantity > 1 ? `(x${quantity})` : ''} (${durationHours.toFixed(1)} hrs)</span>
                    <div class="text-end">
                        <small>₱${unitFee.toLocaleString()}/hr × ${quantity}</small>
                        <div><strong>₱${itemTotal.toLocaleString()}</strong></div>
                    </div>
                </div>
            `;
            } else {
              htmlContent += `
                <div class="fee-item d-flex justify-content-between mb-2">
                    <span>${item.name} ${quantity > 1 ? `(x${quantity})` : ''}</span>
                    <div class="text-end">
                        <div>₱${unitFee.toLocaleString()} × ${quantity}</div>
                        <strong>₱${itemTotal.toLocaleString()}</strong>
                    </div>
                </div>
            `;
            }
            equipmentTotal += itemTotal;
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
        summaryFees.innerHTML = htmlContent;

      } catch (error) {
        console.error('Error generating fee breakdown for summary:', error);
        summaryFees.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Error loading fee breakdown. Please try again.
      </div>
    `;
      }
    }

    // ========== ITEM MANAGEMENT FUNCTIONS ==========
    function pluralizeType(type) {
      if (type.toLowerCase() === 'facility') return 'facilities';
      if (type.toLowerCase() === 'equipment') return 'equipment';
      return type + 's';
    }

    function renderItemsList(container, items, type) {
      if (!container) return;

      // If container is showing loading state, clear it first
      container.innerHTML = '';

      if (items.length === 0) {
        container.innerHTML = `
      <div class="empty-message text-center py-4">
        <p class="text-muted mb-0">No ${pluralizeType(type)} added yet.</p>
      </div>
    `;
        return;
      }

      const cardContainer = document.createElement('div');
      cardContainer.className = 'row row-cols-1 g-3';

      items.forEach(item => {
        const card = document.createElement('div');
        card.className = 'col';
        const displayImage = item.images?.find(img => img.image_type === "Primary") || item.images?.[0];

        card.innerHTML = `
      <div class="card h-100 border-0 shadow-sm mb-2">
        <div class="card-body p-2 d-flex align-items-center">
          ${displayImage ? `
            <img src="${displayImage.image_url}" 
              alt="${item.name}" 
              style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
          ` : `
            <div style="width: 80px; height: 80px; background: #e9ecef; border-radius: 8px; margin-right: 12px;"></div>
          `}
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <h6 class="mb-1 fw-semibold">${item.name}</h6>
              <button type="button" class="btn btn-sm btn-danger ms-2" 
                onclick="removeSelectedItem(${type === 'facility' ? item.facility_id : item.equipment_id}, '${type}')">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <div>
                <span class="badge bg-primary me-2">${item.rate_type || 'booking'}</span>
                ${type === 'equipment' ? `<span class="badge bg-secondary">Qty: ${item.quantity || 1}</span>` : ''}
              </div>
              <div class="text-end">
                <div class="fw-bold text-success">
                  ₱${parseFloat(item.external_fee * (type === 'equipment' ? (item.quantity || 1) : 1)).toLocaleString()}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
        cardContainer.appendChild(card);
      });

      container.appendChild(cardContainer);
    }

    window.renderSelectedItems = async function () {
      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Show loading state for facilities
        if (facilityList) {
          facilityList.innerHTML = `
        <div class="selected-items-loading">
          <div class="loading-overlay">
            <div class="loading-spinner"></div>
            <small>Loading facilities...</small>
          </div>
        </div>
      `;
        }

        // Show loading state for equipment
        if (equipmentList) {
          equipmentList.innerHTML = `
        <div class="selected-items-loading">
          <div class="loading-overlay">
            <div class="loading-spinner"></div>
            <small>Loading equipment...</small>
          </div>
        </div>
      `;
        }

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

        // Render facilities and equipment with actual data
        renderItemsList(facilityList, items.filter(i => i.type === 'facility'), 'facility');
        renderItemsList(equipmentList, items.filter(i => i.type === 'equipment'), 'equipment');

      } catch (error) {
        console.error('Error rendering selected items:', error);
        showToast('Failed to load selected items', 'error');

        // Show error state in both containers
        if (facilityList) {
          facilityList.innerHTML = `
        <div class="alert alert-danger mt-3">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Failed to load facilities
        </div>
      `;
        }

        if (equipmentList) {
          equipmentList.innerHTML = `
        <div class="alert alert-danger mt-3">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Failed to load equipment
        </div>
      `;
        }
      }
    };

    window.calculateAndDisplayFees = async function () {
      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
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
        const feeDisplay = document.getElementById('feeDisplay');

        if (!feeDisplay) return;

        if (items.length === 0) {
          feeDisplay.innerHTML = '<div class="text-muted">No items added yet.</div>';
          return;
        }

        // Get schedule information
        const startDate = document.getElementById('startDateField').value;
        const endDate = document.getElementById('endDateField').value;
        const startTime = document.getElementById('startTimeField').value;
        const endTime = document.getElementById('endTimeField').value;

        let durationHours = 0;
        if (startDate && endDate && startTime && endTime) {
          const startDateTime = new Date(`${startDate}T${convertTo24Hour(startTime)}:00`);
          const endDateTime = new Date(`${endDate}T${convertTo24Hour(endTime)}:00`);
          durationHours = (endDateTime - startDateTime) / (1000 * 60 * 60);
          durationHours = Math.max(0, durationHours);
        }

        let facilityTotal = 0;
        let equipmentTotal = 0;
        let htmlContent = '<div class="fee-items">';

        // Facilities breakdown
        const facilityItems = items.filter(i => i.type === 'facility');
        if (facilityItems.length > 0) {
          htmlContent += '<div class="fee-section"><h6 class="mb-3">Facilities</h6>';
          facilityItems.forEach(item => {
            let fee = parseFloat(item.external_fee);
            if (item.rate_type === 'Per Hour' && durationHours > 0) {
              fee = fee * durationHours;
              htmlContent += `
                            <div class="fee-item d-flex justify-content-between mb-2">
                                <span>${item.name} (${durationHours.toFixed(1)} hrs)</span>
                                <div class="text-end">
                                    <small>₱${parseFloat(item.external_fee).toLocaleString()}/hr</small>
                                    <div><strong>₱${fee.toLocaleString()}</strong></div>
                                </div>
                            </div>
                        `;
            } else {
              htmlContent += `
                            <div class="fee-item d-flex justify-content-between mb-2">
                                <span>${item.name}</span>
                                <span>₱${fee.toLocaleString()}</span>
                            </div>
                        `;
            }
            facilityTotal += fee;
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
            let unitFee = parseFloat(item.external_fee);
            const quantity = item.quantity || 1;
            let itemTotal = unitFee * quantity;
            if (item.rate_type === 'Per Hour' && durationHours > 0) {
              itemTotal = itemTotal * durationHours;
              htmlContent += `
                            <div class="fee-item d-flex justify-content-between mb-2">
                                <span>${item.name} ${quantity > 1 ? `(x${quantity})` : ''} (${durationHours.toFixed(1)} hrs)</span>
                                <div class="text-end">
                                    <small>₱${unitFee.toLocaleString()}/hr × ${quantity}</small>
                                    <div><strong>₱${itemTotal.toLocaleString()}</strong></div>
                                </div>
                            </div>
                        `;
            } else {
              htmlContent += `
                            <div class="fee-item d-flex justify-content-between mb-2">
                                <span>${item.name} ${quantity > 1 ? `(x${quantity})` : ''}</span>
                                <div class="text-end">
                                    <div>₱${unitFee.toLocaleString()} × ${quantity}</div>
                                    <strong>₱${itemTotal.toLocaleString()}</strong>
                                </div>
                            </div>
                        `;
            }
            equipmentTotal += itemTotal;
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
        const feeDisplay = document.getElementById('feeDisplay');
        if (feeDisplay) {
          feeDisplay.innerHTML = '<div class="alert alert-danger">Error loading fee breakdown</div>';
        }
      }
    };

    window.removeSelectedItem = async function (id, type) {
      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const requestBody = {
          type: type,
          equipment_id: type === 'equipment' ? id : undefined,
          facility_id: type === 'facility' ? id : undefined
        };

        const cleanedRequestBody = Object.fromEntries(
          Object.entries(requestBody).filter(([_, v]) => v !== undefined)
        );

        const response = await fetch('/api/requisition/remove-item', {
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
            window.renderSelectedItems(),
            window.calculateAndDisplayFees()
          ]);
          showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} removed successfully`, 'success');

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

    // ========== FORM SUBMISSION FUNCTIONS ==========
    window.openTermsModal = function (event) {
      if (event) event.preventDefault();

      // Check if modal already exists
      let modalEl = document.getElementById('termsModal');
      let modalInstance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;

      // Function to attach event listeners
      const attachModalEventListeners = () => {
        const agreeTerms = document.getElementById('agreeTerms');
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

        if (agreeTerms && confirmSubmitBtn) {
          // Remove any existing listeners first to avoid duplicates
          agreeTerms.replaceWith(agreeTerms.cloneNode(true));
          confirmSubmitBtn.replaceWith(confirmSubmitBtn.cloneNode(true));

          // Re-get references after cloning
          const newAgreeTerms = document.getElementById('agreeTerms');
          const newConfirmSubmitBtn = document.getElementById('confirmSubmitBtn');

          // Attach new listeners
          newAgreeTerms.addEventListener('change', function () {
            newConfirmSubmitBtn.disabled = !this.checked;
          });

          newConfirmSubmitBtn.addEventListener('click', async function () {
            await submitForm();
          });

          // Ensure button starts disabled
          newConfirmSubmitBtn.disabled = true;
        }
      };

      if (!modalEl) {
        // Create modal HTML
        const modalHTML = `
          <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="terms-content mb-4" style="max-height: 50vh; overflow-y: auto;">
                    <h6>Booking Terms and Conditions</h6>
                    <ol>
                      <li>All bookings are subject to approval by CPU Administration.</li>
                      <li>Payment must be made within 3 business days after approval.</li>
                      <li>Cancellations must be made at least 5 days before the event.</li>
                      <li>Damage to facilities/equipment will incur additional charges.</li>
                      <li>Alcohol and smoking are strictly prohibited on campus.</li>
                      <li>External users must provide valid identification.</li>
                      <li>CPU reserves the right to cancel bookings for violations.</li>
                    </ol>
                    <div class="form-check mt-3 text-center">
                      <input class="form-check-input me-2" type="checkbox" id="agreeTerms">
                      <label class="form-check-label" for="agreeTerms">
                        I agree to the terms and conditions
                      </label>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-end">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmSubmitBtn" class="btn btn-primary" disabled>
            <span class="btn-text">Submit Request</span>
            <span class="btn-loading">
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>
              Submitting Request...
            </span>
          </button>
                </div>
              </div>
            </div>
          </div>

              `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        modalEl = document.getElementById('termsModal');

        // Initialize new modal instance
        modalInstance = new bootstrap.Modal(modalEl, {
          backdrop: true,
          keyboard: true,
          focus: true
        });

        // Add event listeners for modal lifecycle
        modalEl.addEventListener('hidden.bs.modal', function () {
          modalInstance.dispose();
          modalEl.remove();
        });

        // Attach event listeners for checkbox and button
        attachModalEventListeners();

      } else if (!modalInstance) {
        // If modal element exists but no instance, create one
        modalInstance = new bootstrap.Modal(modalEl, {
          backdrop: true,
          keyboard: true,
          focus: true
        });

        // Attach event listeners since they might be missing
        attachModalEventListeners();
      } else {
        // Modal exists and has instance, but ensure listeners are attached
        attachModalEventListeners();
      }

      // Reset checkbox state each time modal opens
      const agreeTerms = document.getElementById('agreeTerms');
      const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
      if (agreeTerms && confirmSubmitBtn) {
        agreeTerms.checked = false;
        confirmSubmitBtn.disabled = true;
      }

      // Show the modal
      if (modalInstance) {
        modalInstance.show();
      } else {
        console.error('Modal instance could not be created');
        showToast('Failed to open terms modal. Please try again.', 'error');
      }
    };

    window.submitForm = async function () {
      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const modal = document.getElementById('termsModal');
        const confirmBtn = document.getElementById('confirmSubmitBtn');

        let termsModalInstance = bootstrap.Modal.getInstance(modal);
        if (!termsModalInstance) {
          termsModalInstance = new bootstrap.Modal(modal);
        }

        // Show loading state
        confirmBtn.classList.add('loading');
        confirmBtn.disabled = true;

        // Define all form field variables INSIDE the function
        const schoolIdInput = document.querySelector('input[name="school_id"]');
        const numParticipantsInput = document.querySelector('input[name="num_participants"]');
        const numChairsInput = document.querySelector('input[name="num_chairs"]');
        const numTablesInput = document.querySelector('input[name="num_tables"]');
        const additionalRequestsInput = document.querySelector('textarea[name="additional_requests"]');
        const firstNameInput = document.querySelector('input[name="first_name"]');
        const lastNameInput = document.querySelector('input[name="last_name"]');
        const emailInput = document.querySelector('input[name="email"]');
        const contactNumberInput = document.querySelector('input[name="contact_number"]');
        const organizationNameInput = document.querySelector('input[name="organization_name"]');
        const endorserInput = document.querySelector('input[name="endorser"]');
        const dateEndorsedInput = document.querySelector('input[name="date_endorsed"]');

        if (!firstNameInput || !lastNameInput || !emailInput) {
          throw new Error('Required contact fields are missing in the form.');
        }

        const formData = {
          start_date: document.getElementById('startDateField').value,
          end_date: document.getElementById('endDateField').value,
          start_time: convertTo24Hour(document.getElementById('startTimeField').value),
          end_time: convertTo24Hour(document.getElementById('endTimeField').value),
          purpose_id: document.getElementById('activityPurposeField').value,
          num_participants: numParticipantsInput ? numParticipantsInput.value : 1,
          // ADD THESE LINES - ensure they're included in the form data
          num_chairs: numChairsInput ? parseInt(numChairsInput.value) || 0 : 0,
          num_tables: numTablesInput ? parseInt(numTablesInput.value) || 0 : 0,
          endorser: endorserInput?.value || null,
          date_endorsed: dateEndorsedInput?.value || null,
          additional_requests: additionalRequestsInput ? additionalRequestsInput.value : '',
          formal_letter_url: document.getElementById('formal_letter_url').value || null,
          formal_letter_public_id: document.getElementById('formal_letter_public_id').value || null,
          facility_layout_url: document.getElementById('facility_layout_url')?.value || null,
          facility_layout_public_id: document.getElementById('facility_layout_public_id')?.value || null,
          first_name: firstNameInput.value,
          last_name: lastNameInput.value,
          email: emailInput.value,
          contact_number: contactNumberInput?.value || null,
          organization_name: organizationNameInput?.value || null,
          user_type: document.getElementById('applicantType').value,
          school_id: document.getElementById('applicantType').value === 'Internal'
            ? (schoolIdInput ? schoolIdInput.value : null)
            : null
        };

        console.log('Submitting form data:', formData);

        // Submit form
        const submitResponse = await fetch('/requisition/submit', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(formData),
          credentials: 'include'
        });

        if (!submitResponse.ok) {
          const errorData = await submitResponse.json().catch(() => ({}));
          throw new Error(errorData.message || 'Submission failed');
        }

        const result = await submitResponse.json();

        if (!result.success) {
          throw new Error(result.message || 'Submission failed');
        }

        // Hide the terms modal
        termsModalInstance.hide();

        // Show success details
        document.getElementById('successDetails').innerHTML =
          `Your request ID: ${result.data.request_id}<br>Access Code: ${result.data.access_code}`;

        document.getElementById('userEmail').textContent = formData.email;

        // Show the success modal
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        // ========== CLEAR ALL STORAGE DATA ==========
        // Clear session storage
        await fetch('/requisition/clear-session', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken
          }
        });

        // Clear local storage (form progress data)
        if (typeof localStorage !== 'undefined') {
          // Clear all reservation-related local storage keys
          const keysToRemove = [];
          for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && (
              key.includes('reservation') ||
              key.includes('booking') ||
              key.includes('form') ||
              key.includes('requisition')
            )) {
              keysToRemove.push(key);
            }
          }

          keysToRemove.forEach(key => localStorage.removeItem(key));
          console.log('Cleared local storage keys:', keysToRemove);
        }

        // Clear session storage (client-side)
        if (typeof sessionStorage !== 'undefined') {
          // Clear all reservation-related session storage keys
          const sessionKeysToRemove = [];
          for (let i = 0; i < sessionStorage.length; i++) {
            const key = sessionStorage.key(i);
            if (key && (
              key.includes('reservation') ||
              key.includes('booking') ||
              key.includes('form') ||
              key.includes('requisition') ||
              key.includes('selected_items')
            )) {
              sessionKeysToRemove.push(key);
            }
          }

          sessionKeysToRemove.forEach(key => sessionStorage.removeItem(key));
          console.log('Cleared session storage keys:', sessionKeysToRemove);
        }

        // Reset form to clean state
        document.getElementById('reservationForm').reset();

        // Reset step navigation to step 1
        showStep(1);

        // Clear selected items display
        await renderSelectedItems();
        await calculateAndDisplayFees();

        // Clear any form validation states
        const form = document.getElementById('reservationForm');
        form.querySelectorAll('.is-invalid').forEach(input => {
          input.classList.remove('is-invalid');
          input.removeAttribute('title');
          input.removeAttribute('data-bs-toggle');
          input.removeAttribute('data-bs-placement');
        });

        // Clear any displayed fees or availability results
        const availabilityResult = document.getElementById('availabilityResult');
        if (availabilityResult) {
          availabilityResult.textContent = '';
          availabilityResult.style.color = '';
        }

        // Clear summary fields
        const summaryFields = [
          'summary-applicant-type', 'summary-name', 'summary-email',
          'summary-contact', 'summary-organization', 'summary-school-id',
          'summary-purpose', 'summary-start', 'summary-end',
          'summary-participants', 'summary-furniture', 'summary-endorser',
          'summary-requests'
        ];

        summaryFields.forEach(id => {
          const element = document.getElementById(id);
          if (element) element.textContent = '';
        });

        const summaryFees = document.getElementById('summary-fees');
        if (summaryFees) summaryFees.innerHTML = '';

        console.log('Form completely reset and storage cleared');

      } catch (error) {
        console.error('Error submitting form:', error);
        showToast(error.message || 'Failed to submit form', 'error');

        // Reset button state
        const confirmBtn = document.getElementById('confirmSubmitBtn');
        if (confirmBtn) {
          confirmBtn.classList.remove('loading');
          confirmBtn.disabled = false;
        }
      }
    };

    // ========== FILE UPLOAD FUNCTIONS ==========
    async function uploadToCloudinary(input) {
      const file = input.files[0];
      if (!file) return;

      const progressBar = document.getElementById('progressBar');
      const uploadProgress = document.getElementById('uploadProgress');
      uploadProgress.classList.remove('d-none');

      const formData = new FormData();
      formData.append('file', file);
      formData.append('upload_preset', 'formal-letters');
      formData.append('folder', 'user-uploads/user-letters');

      if (file.type === 'application/pdf') {
        formData.append('resource_type', 'raw');
      } else {
        formData.append('resource_type', 'auto');
      }

      try {
        const response = await fetch(`https://api.cloudinary.com/v1_1/dn98ntlkd/auto/upload`, {
          method: 'POST',
          body: formData
        });

        if (!response.ok) {
          throw new Error('Upload failed with status: ' + response.status);
        }

        const data = await response.json();
        console.log('Upload successful:', data);

        document.getElementById('formal_letter_url').value = data.secure_url;
        document.getElementById('formal_letter_public_id').value = data.public_id;

        showToast('File uploaded successfully!', 'success');
        document.getElementById('removeAttachLetterBtn').classList.remove('d-none');

      } catch (error) {
        console.error('Upload error:', error);
        showToast('File upload failed: ' + error.message, 'error');
        input.value = '';
      } finally {
        uploadProgress.classList.add('d-none');
        progressBar.style.width = '0%';
      }
    }

    function removeFile(inputId, buttonId) {
      const input = document.getElementById(inputId);
      const button = document.getElementById(buttonId);

      input.value = '';
      document.getElementById('formal_letter_url').value = '';
      document.getElementById('formal_letter_public_id').value = '';
      button.classList.add('d-none');

      showToast('File removed', 'info');
    }

    // ========== INITIALIZE FORM ITEMS ==========
    async function initForm() {
      try {
        await Promise.all([
          window.renderSelectedItems(),
          window.calculateAndDisplayFees()
        ]);
      } catch (error) {
        console.error('Error initializing form:', error);
        showToast('Failed to initialize form', 'error');
      }
    }

    // Initialize form when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {


      // Add this to initialize form items
      initForm();

      // Set up schedule field change listeners for fee calculation
      const scheduleFields = [
        'startDateField', 'endDateField', 'startTimeField', 'endTimeField'
      ];
      scheduleFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
          field.addEventListener('change', function () {
            calculateAndDisplayFees();
          });
        }
      });
    });
  </script>
</body>

</html>