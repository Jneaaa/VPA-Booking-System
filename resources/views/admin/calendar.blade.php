@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
  <!-- Combined CSS resources -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet">
  <link rel="stylesheet" href="css/admin-styles.css">
  <style>

    #miniCalendar {
    height: 300px;
    background-color: var(--light-gray);
    border: 1px solid #e0e0e0;
    }

    #showAllButton {
    margin-top: 0;
    align-self: center;
    }

    /* Bootstrap datepicker overrides */
    .datepicker {
    padding: 0;
    border: none;
    }

    .datepicker table {
    width: 100%;
    }

    .datepicker .datepicker-days table {
    margin: 0;
    }

    .datepicker-dropdown {
    box-shadow: none;
    border: 1px solid #ddd;
    }

          #layout {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    margin-top: 10px;
    }
    
  </style>
  <!-- Main Layout -->
  <div id="layout">
    <!-- Calendar Section -->
    <main id="main" style="padding: 1rem;">
    <section class="d-flex" style="height: calc(100vh - 60px);">
      <!-- Left Section: Mini Calendar and Event List -->
      <div class="me-4" style="width: 300px; flex-shrink: 0;"> <!-- Increased width -->

      <div id="miniCalendar" class="td-mini-calendar"></div>


      <div style="height: 50%; overflow-y: auto; margin-top: 20px;"> <!-- Added margin-top -->
        <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold">Event Filter</h5>
        <button class="btn btn-sm btn-outline-primary" id="showAllButton">Show All</button>
        </div>
        <ul class="list-group">
        <li class="list-group-item text-warning d-flex justify-content-between align-items-center"
          data-filter="university">
          <span><i class="bi bi-calendar-event me-2"></i> CPU Event</span>
          <input type="checkbox" class="form-check-input filter-checkbox" data-filter="university">
        </li>
        <li class="list-group-item text-primary d-flex justify-content-between align-items-center"
          data-filter="facility">
          <span><i class="bi bi-calendar-event me-2"></i> Facility Rental</span>
          <input type="checkbox" class="form-check-input filter-checkbox" data-filter="facility">
        </li>
        <li class="list-group-item text-primary d-flex justify-content-between align-items-center"
          data-filter="equipment">
          <span><i class="bi bi-calendar-event me-2"></i> Equipment Rental</span>
          <input type="checkbox" class="form-check-input filter-checkbox" data-filter="equipment">
        </li>
        <li class="list-group-item text-secondary d-flex justify-content-between align-items-center"
          data-filter="external">
          <span><i class="bi bi-calendar-event me-2"></i> External Conference</span>
          <input type="checkbox" class="form-check-input filter-checkbox" data-filter="external">
        </li>
        </ul>
      </div>

      </div>
      <!-- Right Section: Events Calendar -->
      <div style="flex-grow: 1; height: 100%;"> <!-- Reduced flex-grow -->
      <div id="calendar" class="border p-2 calendar-container" style="height: 100%;"></div>
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
@endsection

@section('script')

  <!-- Combined JS resources -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script src="js/calendar.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Initialize Mini Calendar with Bootstrap Datepicker
    $('#miniCalendar').datepicker({
      format: "mm/dd/yyyy",
      todayHighlight: true,
      weekStart: 0, // Sunday
      autoclose: true
    }).on('changeDate', function (e) {
      // When a date is selected, switch the main calendar to day view
      if (typeof calendar !== 'undefined') {
      calendar.changeView('timeGridDay', e.date);
      }
    });

    // Show the calendar immediately
    $('#miniCalendar').datepicker('show');
    });
  </script>
@endsection