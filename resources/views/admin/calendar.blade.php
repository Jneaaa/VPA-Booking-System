{{-- calendar.blade.php --}}
@extends('layouts.admin')
@section('title', 'Ongoing Events')
@section('content')
<style>
  .card {
    border: 0 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    border-radius: 0.75rem;
    /* optional, for smoother corners */
  }

  .fc .fc-toolbar-chunk .fc-button:focus,
  .fc .fc-toolbar-chunk .fc-button:active {
    outline: none !important;
    box-shadow: none !important;
  }

  /* FullCalendar Toolbar Buttons */
  .fc .fc-toolbar-chunk .fc-button {
    background-color: #ffffff !important;
    /* White background */
    color: #6c757d !important;
    /* Gray text */
    border: none !important;
    /* No border */
    font-weight: 500;
    border-radius: 6px !important;
  }

  /* Hover state */
  .fc .fc-toolbar-chunk .fc-button:hover {
    background-color: #f8f9fa !important;
    /* Slightly off-white hover */
    color: #495057 !important;
    /* Darker gray text on hover */
    border: none !important;
  }

  /* Active/Pressed state */
  .fc .fc-toolbar-chunk .fc-button.fc-button-active {
    background-color: #4272b1ff !important;
    color: #ffffffff !important;
    border: none !important;
  }

  .fc .fc-today-button {
    text-transform: capitalize !important;
  }

  /* Base checkbox style */
  .form-check-input {
    width: 1.1em;
    height: 1.1em;
    cursor: pointer;
  }


  .scheduled-checkbox:checked {
    background-color: #1e7941ff;
    border-color: #1e7941ff;
  }

  .ongoing-checkbox:focus {
    box-shadow: 0 0 0 0.2rem #1461314d
  }

  .ongoing-checkbox:checked {
    background-color: #ac7a0fff;
    border-color: #ac7a0fff;
  }

  .ongoing-checkbox:focus {
    box-shadow: 0 0 0 0.2rem #75530941;
  }

  /* Late = red */
  .late-checkbox:checked {
    background-color: #8f2a2aff;
    border-color: #8f2a2aff;
  }

  .late-checkbox:focus {
    box-shadow: 0 0 0 0.2rem #701a1a59;
  }

  /* Remove or update the skeleton filter height limit */
.col-lg-3 .card:last-child .skeleton-container {
  max-height: none !important; /* Remove the height restriction */
  min-height: 200px; /* Ensure minimum height */
}

/* Make sure skeleton container is visible when loading */
.loading .skeleton-container {
  display: block !important; /* Force display */
  visibility: visible !important;
  opacity: 1 !important;
}

/* Make sure calendar content is hidden when loading */
.loading .calendar-content {
  display: none !important; /* Force hide */
  visibility: hidden !important;
  opacity: 0 !important;
}

/* Ensure skeleton containers fill available space */
.col-lg-3 .card:last-child .skeleton-container,
.col-lg-3 .card:last-child .calendar-content {
  width: 100%;
  height: 100%;
}


  /* Ensure skeleton days don't take too much space */
  #miniCalendarDaysSkeleton {
    max-height: 120px;
    overflow: hidden;
  }

  /* Make skeleton days grid more compact */
  #miniCalendarDaysSkeleton .skeleton-day {
    height: 20px !important;
    /* Reduced from 32px */
    margin: 1px;
  }

  /* Make the row stretch full height */
  .row.g-3 {
    align-items: stretch;
  }

  /* Make both cards in left column fill height */
  .col-lg-3 {
    display: flex;
    flex-direction: column;
  }

  .col-lg-3 .card {
    flex-grow: 1;
  }

  /* Mini calendar takes minimal height, event filter fills remaining */
  .col-lg-3 .card:first-child {
    flex: 0 0 auto;
  }

  .col-lg-3 .card:last-child {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
  }

  /* Ensure events filter card body stretches */
  .col-lg-3 .card:last-child .card-body {
    flex-grow: 1;
  }

  /* Ensure calendar matches height */
  #calendar {
    height: 450px !important;
  }

  .mini-calendar .calendar-days {
    display: flex;
    flex-wrap: wrap;
  }

  .mini-calendar .calendar-day {
    min-height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border-radius: 4px;
  }

  .mini-calendar .calendar-day:hover {
    background-color: #d3dbe4ff;
    cursor: pointer;
  }

  .mini-calendar .day-header {
    min-height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .mini-calendar .calendar-day.has-events {
    font-weight: bold;
    color: #004183;
    position: relative;
  }

  .mini-calendar .calendar-day.has-events::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 4px;
    background-color: #004183;
    border-radius: 50%;
  }

  .mini-calendar .calendar-day.today {
    background-color: #366eaaff;
    color: white;
  }

  .mini-calendar .calendar-day.today.has-events::after {
    background-color: white;
  }

  /* Mini Calendar Grid */
  #miniCalendarDays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    /* 7 days per week */
    gap: 4px;
  }

  /* Each day cell */
  #miniCalendarDays .day {
    aspect-ratio: 1 / 1;
    /* Make them perfect squares */
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: monospace;
    /* Equal number width */
    font-size: 0.9rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  /* Optional hover/active styling */
  #miniCalendarDays .day:hover {
    background-color: #f0f0f0;
  }

  /* Example for active day */
  #miniCalendarDays .day.active {
    background-color: #007bff;
    color: white;
  }

  /* Loading Skeleton Styles */
  .skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 4px;
  }

  .skeleton-text {
    height: 12px;
    margin-bottom: 8px;
  }

  .skeleton-title {
    height: 20px;
    margin-bottom: 16px;
  }

  .skeleton-button {
    height: 32px;
    width: 32px;
    border-radius: 4px;
  }

  .skeleton-day {
    height: 32px;
    border-radius: 4px;
  }

  .skeleton-checkbox {
    height: 16px;
    width: 16px;
    border-radius: 3px;
    margin-right: 8px;
  }

  .skeleton-badge {
    height: 12px;
    width: 12px;
    border-radius: 2px;
    margin-right: 8px;
  }

  @keyframes loading {
    0% {
      background-position: 200% 0;
    }

    100% {
      background-position: -200% 0;
    }
  }

  .skeleton-container {
    display: none;
  }

  .loading .skeleton-container {
    display: block;
  }

  .loading .calendar-content {
    display: none;
  }

  /* Event Modal Edit Mode Styles */
  #modalCalendarTitle:not([readonly]),
  #modalCalendarDescription:not([readonly]) {
    color: #000 !important;
    background-color: #fff !important;
    border-color: #4272b1ff !important;
    box-shadow: 0 0 0 0.2rem rgba(66, 114, 177, 0.25) !important;
  }

  /* Make sure the readonly state is properly styled */
  #modalCalendarTitle[readonly],
  #modalCalendarDescription[readonly] {
    color: #6c757d !important;
    background-color: #f8f9fa !important;
    cursor: default;
  }

  /* Focus state for better UX */
  #modalCalendarTitle:focus,
  #modalCalendarDescription:focus {
    color: #000 !important;
    border-color: #4272b1ff !important;
    box-shadow: 0 0 0 0.2rem rgba(66, 114, 177, 0.25) !important;
    outline: 0;
  }


  .facility-item .form-check-label {
    font-size: 0.85rem;
    cursor: pointer;
  }

  .facility-item .form-check-input:checked+.form-check-label {
    font-weight: bold;
    color: #004183;
  }

  #facilityFilterList::-webkit-scrollbar {
    width: 6px;
  }

  #facilityFilterList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }

  #facilityFilterList::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }

  #facilityFilterList::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }

 
</style>

<main id="main">
  <div class="container-fluid">
    <div class="row g-3">
      <!-- Left Column: Mini Calendar & Filters -->
      <div class="col-lg-3">
        <!-- Mini Calendar Card -->
        <div class="card mb-3">
          <div class="card-body">
            <!-- Skeleton for Mini Calendar -->
            <div class="skeleton-container">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="skeleton skeleton-title flex-grow-1 mx-3" style="height: 24px;"></div>
              </div>
              <div class="calendar-header d-flex mb-2">
                <div class="skeleton skeleton-text flex-fill mx-1" style="height: 16px;"></div>
                <div class="skeleton skeleton-text flex-fill mx-1" style="height: 16px;"></div>
                <div class="skeleton skeleton-text flex-fill mx-1" style="height: 16px;"></div>
                <div class="skeleton skeleton-text flex-fill mx-1" style="height: 16px;"></div>
                <div class="skeleton skeleton-text flex-fill mx-1" style="height: 16px;"></div>
                <div class="skeleton skeleton-text flex-fill mx-1" style="height: 16px;"></div>
                <div class="skeleton skeleton-text flex-fill mx-1" style="height: 16px;"></div>
              </div>
              <div class="calendar-days" id="miniCalendarDaysSkeleton">
                <!-- Skeleton days will be populated by JavaScript -->
              </div>
            </div>

            <!-- Actual Mini Calendar Content -->
            <div class="calendar-content">
              <div class="mini-calendar">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <button class="btn btn-sm btn-secondary prev-month" type="button">
                    <i class="bi bi-chevron-left"></i>
                  </button>
                  <h6 class="mb-0 month-year" id="currentMonthYear">October 2024</h6>
                  <button class="btn btn-sm btn-secondary next-month" type="button">
                    <i class="bi bi-chevron-right"></i>
                  </button>
                </div>
                <div class="calendar-header d-flex mb-2">
                  <div class="day-header text-center flex-fill small text-muted">S</div>
                  <div class="day-header text-center flex-fill small text-muted">M</div>
                  <div class="day-header text-center flex-fill small text-muted">T</div>
                  <div class="day-header text-center flex-fill small text-muted">W</div>
                  <div class="day-header text-center flex-fill small text-muted">T</div>
                  <div class="day-header text-center flex-fill small text-muted">F</div>
                  <div class="day-header text-center flex-fill small text-muted">S</div>
                </div>
                <div class="calendar-days" id="miniCalendarDays">
                  <!-- Days populated by JavaScript -->
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Events Filter Card -->
        <div class="card">
          <div class="card-body d-flex flex-column">
            <!-- Skeleton for Events Filter -->
            <div class="skeleton-container flex-grow-1">
              <div class="skeleton skeleton-title mb-3" style="height: 20px; width: 60%;"></div>
              <div class="d-flex align-items-center mb-2">
                <div class="skeleton skeleton-checkbox"></div>
                <div class="skeleton skeleton-text flex-grow-1" style="height: 14px;"></div>
              </div>
              <div class="d-flex align-items-center mb-2">
                <div class="skeleton skeleton-checkbox"></div>
                <div class="skeleton skeleton-text flex-grow-1" style="height: 14px;"></div>
              </div>
              <div class="skeleton skeleton-title mt-4" style="height: 20px; width: 60%;"></div>
              <div class="filter-list mt-2">
                <div class="d-flex align-items-center mb-2">
                  <div class="skeleton skeleton-checkbox"></div>
                  <div class="skeleton skeleton-text flex-grow-1" style="height: 14px;"></div>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <div class="skeleton skeleton-checkbox"></div>
                  <div class="skeleton skeleton-text flex-grow-1" style="height: 14px;"></div>
                </div>
              </div>
            </div>

            <!-- Actual Events Filter Content -->
            <div class="calendar-content d-flex flex-column flex-grow-1">
              <h6 class="fw-bold mb-3">Filter by Status</h6>

              <div class="form-check mb-2">
                <input class="form-check-input event-filter-checkbox scheduled-checkbox" type="checkbox"
                  value="Scheduled" id="filterScheduled" checked>
                <label class="form-check-label" for="filterScheduled">Scheduled Events</label>
              </div>

              <div class="form-check mb-2">
                <input class="form-check-input event-filter-checkbox ongoing-checkbox" type="checkbox" value="Ongoing"
                  id="filterOngoing" checked>
                <label class="form-check-label" for="filterOngoing">Ongoing Events</label>
              </div>

              <div class="form-check mb-2">
                <input class="form-check-input event-filter-checkbox late-checkbox" type="checkbox" value="Late"
                  id="filterLate" checked>
                <label class="form-check-label" for="filterLate">Late Events</label>
              </div>

              <hr class="my-3">
              <h6 class="fw-bold mb-2">Filter by Venue</h6>
              <div class="filter-list flex-grow-1" id="facilityFilterList" style="overflow-y: auto;">
                <!-- Facilities will be populated by JavaScript -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: FullCalendar -->
      <div class="col-lg-9 d-flex flex-column">
        <div class="card flex-grow-1">
          <div class="card-body p-3 d-flex flex-column">
            <!-- Calendar Skeleton -->
            <div class="skeleton-container flex-grow-1">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                  <div class="skeleton skeleton-button" style="width: 200px;"></div>
                  <div class="skeleton skeleton-button" style="width: 80px;"></div>
                  <div class="skeleton skeleton-button" style="width: 80px;"></div>
                  <div class="skeleton skeleton-button" style="width: 100px;"></div>
                  <div class="skeleton skeleton-button" style="width: 100px;"></div>
                  <div class="skeleton skeleton-button" style="width: 100px;"></div>
                </div>
              </div>
              <div class="skeleton flex-grow-1" style="border-radius: 8px;"></div>
            </div>
            <!-- Actual Calendar Content -->
            <div class="calendar-content flex-grow-1 d-flex flex-column">
              <div id="calendar" class="flex-grow-1"></div>
            </div>
          </div>
        </div>
      </div>
<!-- Admin Reservations Card -->
<div class="col-12 mt-3">
  <div class="card">
    <div class="card-body">
      <!-- Skeleton for Admin Reservations -->
      <div class="skeleton-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="skeleton skeleton-title" style="height: 20px; width: 40%;"></div>
          <div class="skeleton skeleton-button" style="height: 28px; width: 100px;"></div>
        </div>
        
        <!-- Random length skeleton lines -->
        <div class="mb-3">
          <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 90%;"></div>
          <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 70%;"></div>
          <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 85%;"></div>
          <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 60%;"></div>
          <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 75%;"></div>
          <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 80%;"></div>
          <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 65%;"></div>
        </div>
      </div>

      <!-- Actual Admin Reservations Content -->
      <div class="calendar-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Admin Reservations</h6>
          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
            <i class="bi bi-plus-circle me-1"></i> Add new
          </button>
        </div>
        
        <div id="adminReservationsList">
          <!-- Will be populated by JavaScript -->
          <div class="text-center py-5">
            <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
            <p class="text-muted mb-0">No reservations added yet.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
      
    </div> <!-- Close row.g-3 -->
  </div> <!-- Close container-fluid -->

  <!-- Event Details Modal -->
  <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 800px;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="eventModalBody">
          <!-- Calendar Title & Description Section -->
          <div class="card border-0 shadow-none mb-0 py-1 px-3">
            <div class="row">
              <div class="col-12">
                <!-- Calendar Title -->
                <div class="mb-2">
                  <label class="form-label fw-bold d-flex align-items-center mb-2">
                    Calendar Title
                    <i class="bi bi-pencil text-secondary ms-2" id="editCalendarTitleBtn" style="cursor: pointer;"></i>
                    <div class="edit-actions ms-2 d-none" id="calendarTitleActions">
                      <button type="button" class="btn btn-sm btn-success me-1" id="saveCalendarTitleBtn">
                        <i class="bi bi-check"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" id="cancelCalendarTitleBtn">
                        <i class="bi bi-x"></i>
                      </button>
                    </div>
                  </label>
                  <input type="text" class="form-control text-secondary" id="modalCalendarTitle" readonly>
                </div>

                <!-- Calendar Description -->
                <div class="mb-0">
                  <label class="form-label fw-bold d-flex align-items-center mb-2">
                    Calendar Description
                    <i class="bi bi-pencil text-secondary ms-2" id="editCalendarDescriptionBtn"
                      style="cursor: pointer;"></i>
                    <div class="edit-actions ms-2 d-none" id="calendarDescriptionActions">
                      <button type="button" class="btn btn-sm btn-success me-1" id="saveCalendarDescriptionBtn">
                        <i class="bi bi-check"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" id="cancelCalendarDescriptionBtn">
                        <i class="bi bi-x"></i>
                      </button>
                    </div>
                  </label>
                  <textarea class="form-control text-secondary" id="modalCalendarDescription" rows="2"
                    readonly></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="card border-0 shadow-none mb-3 p-3">
            <table class="table table-bordered mb-0 w-100" style="table-layout: fixed; border: 1px solid #dee2e6;">
              <thead>
                <tr>
                  <th class="bg-light p-2" style="width: 50%; border: 1px solid #dee2e6;">
                    Event Information
                  </th>
                  <th class="bg-light p-2" style="width: 50%; border: 1px solid #dee2e6;">
                    Requested Items
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="border: 1px solid #dee2e6; padding: 0;">
                    <table class="table mb-0 w-100" style="border-collapse: collapse;">
                      <tbody>
                        <tr>
                          <th class="bg-light text-nowrap p-2"
                            style="width: 40%; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            Requester
                          </th>
                          <td id="modalRequester" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                        </tr>
                        <tr>
                          <th class="bg-light text-nowrap p-2"
                            style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            Purpose
                          </th>
                          <td id="modalPurpose" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                        </tr>
                        <tr>
                          <th class="bg-light text-nowrap p-2"
                            style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            Participants
                          </th>
                          <td id="modalParticipants" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                        </tr>
                        <tr>
                          <th class="bg-light text-nowrap p-2"
                            style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            Status
                          </th>
                          <td id="modalStatus" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                        </tr>
                        <tr>
                          <th class="bg-light text-nowrap p-2"
                            style="border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            Approved Fee
                          </th>
                          <td id="modalFee" class="p-2" style="border-bottom: 1px solid #dee2e6;"></td>
                        </tr>
                        <tr>
                          <th class="bg-light text-nowrap p-2" style="border-right: 1px solid #dee2e6;">Approvals</th>
                          <td id="modalApprovals" class="p-2"></td>
                        </tr>
                        <tr>
                          <th class="bg-light text-nowrap p-2" style="border-right: 1px solid #dee2e6;">Rejections</th>
                          <td id="modalRejections" class="p-2"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="border: 1px solid #dee2e6; vertical-align: top; padding: 0;">
                    <div id="modalItems" class="p-3" style="min-height: 100%;">
                      <!-- JS will insert items here -->
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="modalViewDetails">View Full Details</button>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Add Reservation Modal -->
<div class="modal fade" id="addReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-plus me-3 fs-4"></i>
                    <div>
                        <h5 class="modal-title mb-0 fw-semibold">Create New Reservation</h5>
                        <small class="opacity-75">Add a reservation on behalf of a user</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <!-- Progress Steps -->
                <div class="px-4 pt-4">
                    <div class="steps">
                        <div class="step active">
                            <div class="step-circle">1</div>
                            <div class="step-label">User Info</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">2</div>
                            <div class="step-label">Details</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">3</div>
                            <div class="step-label">Schedule</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">4</div>
                            <div class="step-label">Resources</div>
                        </div>
                    </div>
                </div>

                <form id="addReservationForm" class="p-4">
                    <!-- User Information Card -->
                    <div class="card card-border mb-4">
                        <div class="card-header bg-light-subtle">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-person-circle me-2"></i>User Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">User Type <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-md" name="user_type" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="Internal">Internal User</option>
                                        <option value="External">External User</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="first_name" placeholder="Enter first name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="last_name" placeholder="Enter last name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="user@example.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number" placeholder="+1 (123) 456-7890">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Organization</label>
                                    <input type="text" class="form-control" name="organization_name" placeholder="Company / Institution name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details Card -->
                    <div class="card card-border mb-4">
                        <div class="card-header bg-light-subtle">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-clipboard-data me-2"></i>Request Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Purpose <span class="text-danger">*</span></label>
                                    <select class="form-select" name="purpose_id" id="purposeSelect" required>
                                        <option value="" disabled selected>Select purpose of reservation</option>
                                        <!-- Will be populated by JavaScript -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Participants <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-people"></i></span>
                                        <input type="number" class="form-control" name="num_participants" min="1" max="500" placeholder="Number of attendees" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Access Code <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control font-monospace" name="access_code" id="accessCodeInput" placeholder="Click generate" readonly>
                                        <button type="button" class="btn btn-outline-primary" id="generateAccessCode">
                                            <i class="bi bi-key me-1"></i> Generate
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="copyAccessCode" title="Copy to clipboard">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Unique code for event access. Click generate to create.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Card -->
                    <div class="card card-border mb-4">
                        <div class="card-header bg-light-subtle">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-calendar-event me-2"></i>Schedule
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Start Date & Time <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            <input type="datetime-local" class="form-control" name="start_datetime" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">End Date & Time <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            <input type="datetime-local" class="form-control" name="end_datetime" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info py-2">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <small>Duration: <span id="durationDisplay">0 hours</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resources Selection Card -->
                    <div class="card card-border mb-4">
                        <div class="card-header bg-light-subtle">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-building me-2"></i>Resources
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Facilities -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fw-medium mb-0">Select Facilities</label>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearFacilities">
                                        Clear All
                                    </button>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="selectAllFacilities">
                                    <label class="form-check-label fw-medium" for="selectAllFacilities">
                                        Select All Available Facilities
                                    </label>
                                </div>
                                <div class="facilities-grid" id="facilitiesList">
                                    <!-- Facilities will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Equipment -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fw-medium mb-0">Select Equipment</label>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearEquipment">
                                        Clear All
                                    </button>
                                </div>
                                <div class="equipment-grid" id="equipmentList">
                                    <!-- Equipment will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Card -->
                    <div class="card card-border mb-4">
                        <div class="card-header bg-light-subtle">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-sticky me-2"></i>Additional Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Calendar Title</label>
                                    <input type="text" class="form-control" name="calendar_title" placeholder="e.g., Quarterly Meeting - Sales Team">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Calendar Description</label>
                                    <textarea class="form-control" name="calendar_description" rows="2" placeholder="Brief description visible on calendar..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Additional Requests & Notes</label>
                                    <textarea class="form-control" name="additional_requests" rows="3" placeholder="Any special requirements, setup needs, or additional information..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light-subtle">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveReservation">
                    <i class="bi bi-check-circle me-1"></i> Create Reservation
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styles for Modern Look */
    .modal-xl {
        max-width: 1000px;
    }
    
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    }
    
    .card-border {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: #e0e0e0;
        z-index: 1;
    }
    
    .step {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin: 0 auto 0.5rem;
    }
    
    .step.active .step-circle {
        background: #4361ee;
        border-color: #4361ee;
        color: white;
    }
    
    .step-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .step.active .step-label {
        color: #4361ee;
        font-weight: 600;
    }
    
    .facilities-grid,
    .equipment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        max-height: 250px;
        overflow-y: auto;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .form-check-input:checked {
        background-color: #4361ee;
        border-color: #4361ee;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
    }
    
    .alert-info {
        background-color: #e7f1ff;
        border-color: #d0e2ff;
        color: #084298;
    }
    
    .font-monospace {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.9rem;
        letter-spacing: 1px;
    }
</style>

<script>
    // Optional: Add JavaScript for enhanced functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Generate random access code
        document.getElementById('generateAccessCode').addEventListener('click', function() {
            const code = 'RES-' + Math.random().toString(36).substr(2, 8).toUpperCase();
            document.getElementById('accessCodeInput').value = code;
        });
        
        // Copy access code to clipboard
        document.getElementById('copyAccessCode').addEventListener('click', function() {
            const codeInput = document.getElementById('accessCodeInput');
            codeInput.select();
            document.execCommand('copy');
            // Show feedback (you can add toast notification)
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check2"></i> Copied!';
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
        
        // Calculate duration between dates
        const startInput = document.querySelector('input[name="start_datetime"]');
        const endInput = document.querySelector('input[name="end_datetime"]');
        
        function calculateDuration() {
            if (startInput.value && endInput.value) {
                const start = new Date(startInput.value);
                const end = new Date(endInput.value);
                const diffMs = end - start;
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffDays = Math.floor(diffHours / 24);
                
                let display = '';
                if (diffDays > 0) {
                    display = `${diffDays} day${diffDays > 1 ? 's' : ''} ${diffHours % 24} hour${(diffHours % 24) > 1 ? 's' : ''}`;
                } else {
                    display = `${diffHours} hour${diffHours > 1 ? 's' : ''}`;
                }
                
                document.getElementById('durationDisplay').textContent = display;
            }
        }
        
        startInput.addEventListener('change', calculateDuration);
        endInput.addEventListener('change', calculateDuration);
    });
</script>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    let currentDate = new Date();
    let allRequests = [];
    let filteredRequests = [];
    let currentRequestId = null;
    let originalCalendarTitle = '';
    let originalCalendarDescription = '';
    let allFacilities = [];
    let filteredFacilities = [];
    let selectedFacilityIds = [];


    const adminToken = localStorage.getItem('adminToken');
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

    // Add loading class to body initially
    document.body.classList.add('loading');

    // Generate skeleton days for mini calendar
    function generateSkeletonDays() {
      const skeletonContainer = document.getElementById('miniCalendarDaysSkeleton');
      if (!skeletonContainer) return;

      skeletonContainer.innerHTML = '';

      // Generate 35 skeleton days (5 weeks instead of 6)
      for (let i = 0; i < 35; i++) {
        const skeletonDay = document.createElement('div');
        skeletonDay.className = 'skeleton skeleton-day';
        skeletonDay.style.height = '20px'; // Compact height
        skeletonDay.style.margin = '1px'; // Reduced spacing
        skeletonContainer.appendChild(skeletonDay);
      }
    }

    // Hide loading skeletons and show content
    function hideSkeletons() {
      document.body.classList.remove('loading');
    }

    // Initialize mini calendar
    function initializeMiniCalendar() {
      updateMiniCalendar();

      // Event listeners for navigation
      document.querySelector('.prev-month').addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateMiniCalendar();
      });

      document.querySelector('.next-month').addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateMiniCalendar();
      });
    }

    function updateMiniCalendar() {
      const monthYearElement = document.getElementById('currentMonthYear');
      const daysContainer = document.getElementById('miniCalendarDays');

      // Update month year display
      const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
      ];
      monthYearElement.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

      // Clear existing days
      daysContainer.innerHTML = '';

      // Get first/last day of current month
      const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
      const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
      const daysInMonth = lastDay.getDate();
      const startingDay = firstDay.getDay(); // 0 = Sunday

      // Get previous and next month details
      const prevMonthLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
      const daysInPrevMonth = prevMonthLastDay.getDate();

      const today = new Date();

      // Add previous month's days
      for (let i = startingDay - 1; i >= 0; i--) {
        const prevDay = daysInPrevMonth - i;
        const prevDayElement = document.createElement('div');
        prevDayElement.className = 'calendar-day text-center flex-fill small text-muted';
        prevDayElement.style.opacity = '0.4';
        prevDayElement.textContent = prevDay;

        // Clicking these jumps to the previous month
        prevDayElement.addEventListener('click', function() {
          const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, prevDay);
          navigateToDate(selectedDate);
        });

        daysContainer.appendChild(prevDayElement);
      }

      // Add current month's days
      for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day text-center flex-fill small p-1';
        dayElement.style.cursor = 'pointer';

        const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
        const hasEvents = checkDayHasEvents(dayDate);

        if (hasEvents) {
          dayElement.classList.add('has-events');
          dayElement.title = 'Click to view events on this day';
        }

        // Highlight today
        if (
          day === today.getDate() &&
          currentDate.getMonth() === today.getMonth() &&
          currentDate.getFullYear() === today.getFullYear()
        ) {
          dayElement.classList.add('today');
        }

        dayElement.textContent = day;

        // Jump to date regardless of event
        dayElement.addEventListener('click', function() {
          const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
          navigateToDate(selectedDate);
        });

        daysContainer.appendChild(dayElement);
      }

      // Add next month's days to complete the grid
      const totalCells = daysContainer.children.length;
      const remainingCells = 42 - totalCells; // ensures 6x7 grid
      for (let i = 1; i <= remainingCells; i++) {
        const nextDayElement = document.createElement('div');
        nextDayElement.className = 'calendar-day text-center flex-fill small text-muted';
        nextDayElement.style.opacity = '0.4';
        nextDayElement.textContent = i;

        // Clicking these jumps to the next month
        nextDayElement.addEventListener('click', function() {
          const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, i);
          navigateToDate(selectedDate);
        });

        daysContainer.appendChild(nextDayElement);
      }
    }

    // Check if a date has events
    function checkDayHasEvents(date) {
      // Create a date string in local timezone to avoid UTC conversion issues
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const dateString = `${year}-${month}-${day}`;

      return filteredRequests.some(request => {
        const eventStartDate = request.schedule.start_date;
        const eventEndDate = request.schedule.end_date;
        return dateString >= eventStartDate && dateString <= eventEndDate;
      });
    }

    // Navigate FullCalendar to specific date
    function navigateToDate(date) {
      if (calendar) {
        calendar.gotoDate(date);
        calendar.changeView('timeGridDay');
      }
    }


    // Initialize main calendar
    function initializeCalendar() {
      const calendarEl = document.getElementById('calendar');
      if (!calendarEl) return;

      calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
          today: 'Today',
          month: 'Month',
          week: 'Week',
          day: 'Day'
        },
        titleFormat: {
          year: 'numeric',
          month: 'short'
        },
        height: '100%',
        handleWindowResize: true,
        windowResizeDelay: 200,
        aspectRatio: null,
        expandRows: true,
        events: [],
        eventClick: function(info) {
          const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);
          if (request) {
            showEventModal(request);
          }
        },
        eventDidMount: function(info) {
          // Add custom styling based on status
          const status = info.event.extendedProps.status;
          const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);

          if (request) {
            info.el.style.backgroundColor = request.form_details.status.color;
            info.el.style.borderColor = request.form_details.status.color;
            info.el.style.color = '#fff';
            info.el.style.fontWeight = 'bold';
          }
        },
        datesSet: function(info) {
          calendar.updateSize();
        },
        viewDidMount: function(info) {
          setTimeout(() => calendar.updateSize(), 0);
        },
        eventTimeFormat: {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        },
        slotMinTime: '00:00:00',
        slotMaxTime: '24:00:00',
        allDaySlot: false,
        nowIndicator: true,
        navLinks: true,
        dayHeaderFormat: {
          weekday: 'long',
          month: 'short',
          day: 'numeric'
        },
        views: {
          dayGridMonth: {
            dayHeaderFormat: {
              weekday: 'short'
            },
            eventContent: function(arg) {
              // Create a wrapper that preserves the default content but adds wrapping
              const arrayOfDomNodes = [];

              // Add time element for timed events
              if (arg.event.start && !arg.event.allDay) {
                // Format start time without leading zeros
                const startTime = arg.event.start.toLocaleTimeString('en-US', {
                  hour: 'numeric',
                  minute: '2-digit',
                  hour12: true
                }).replace(/^0/, ''); // Remove leading zero if present

                let timeText = startTime;

                // Add end time if it exists
                if (arg.event.end) {
                  // Format end time without leading zeros
                  const endTime = arg.event.end.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                  }).replace(/^0/, ''); // Remove leading zero if present
                  timeText = `${startTime} - ${endTime}`;
                }

                const timeEl = document.createElement('div');
                timeEl.classList.add('fc-event-time');
                timeEl.style.fontSize = '0.75em';
                timeEl.style.fontWeight = '500';
                timeEl.style.marginBottom = '1px';
                timeEl.style.opacity = '0.9';
                timeEl.innerText = timeText;
                arrayOfDomNodes.push(timeEl);
              }

              // Add title with wrapping
              const titleEl = document.createElement('div');
              titleEl.classList.add('fc-event-title');
              titleEl.style.whiteSpace = 'normal';
              titleEl.style.wordWrap = 'break-word';
              titleEl.style.fontSize = '0.85em';
              titleEl.style.lineHeight = '1.2';

              if (arg.event.title) {
                titleEl.innerText = arg.event.title;
              }
              arrayOfDomNodes.push(titleEl);

              return {
                domNodes: arrayOfDomNodes
              };
            }
          }
        },
        eventDisplay: 'block',
        dayMaxEvents: false,
      });

      calendar.render();
      updateCalendarEvents();
    }

    // Filter and update calendar events
    function updateCalendarEvents() {
      if (!calendar) return;

      calendar.removeAllEvents();

      // Filter requests by status AND facility
      filteredRequests = allRequests.filter(req => {
        // Status filtering (existing logic)
        const statusName = req.form_details.status.name;
        const showScheduled = document.getElementById('filterScheduled').checked;
        const showOngoing = document.getElementById('filterOngoing').checked;
        const showLate = document.getElementById('filterLate').checked;

        const statusMatch = (statusName === 'Scheduled' && showScheduled) ||
          (statusName === 'Ongoing' && showOngoing) ||
          (statusName === 'Late' && showLate);

        if (!statusMatch) return false;

        // Facility filtering - check if "All Facilities" is selected
        const allFacilitiesCheckbox = document.getElementById('allFacilities');
        if (allFacilitiesCheckbox && allFacilitiesCheckbox.checked) {
          return true; // Show all events when "All Facilities" is checked
        }

        // If specific facilities are selected
        if (selectedFacilityIds.length === 0) {
          return true; // Show all events when no specific facilities selected
        }

        // Check if this request includes any of the selected facilities
        // Now we have facility_id in the response
        const requestedFacilities = req.requested_items?.facilities || [];

        console.log(`Request #${req.request_id} has facilities:`,
          requestedFacilities.map(f => ({
            facility_id: f.facility_id,
            name: f.name
          })));

        const hasSelectedFacility = requestedFacilities.some(facility => {
          // Get the facility_id from the response
          const facilityId = facility.facility_id;
          if (!facilityId) {
            console.warn('Facility missing ID:', facility);
            return false;
          }

          // Check if this facility ID is in our selected list
          const isSelected = selectedFacilityIds.includes(facilityId.toString());
          if (isSelected) {
            console.log(`✓ Request #${req.request_id} includes selected facility: ${facilityId} (${facility.name})`);
          }
          return isSelected;
        });

        if (!hasSelectedFacility && selectedFacilityIds.length > 0) {
          console.log(`✗ Request #${req.request_id} does NOT include any selected facilities`);
        }

        return hasSelectedFacility;
      });

      console.log('Total filtered requests:', filteredRequests.length);
      console.log('Selected facility IDs:', selectedFacilityIds);

      // Add filtered events to calendar
      filteredRequests.forEach(req => {
        const calendarTitle = req.form_details.calendar_info?.title ||
          `Request #${String(req.request_id).padStart(4, '0')}`;

        calendar.addEvent({
          title: calendarTitle,
          start: `${req.schedule.start_date}T${req.schedule.start_time}`,
          end: `${req.schedule.end_date}T${req.schedule.end_time}`,
          extendedProps: {
            status: req.form_details.status.name,
            requestId: req.request_id
          },
          description: req.form_details.calendar_info?.description,
          color: req.form_details.status.color
        });
      });

      // Update mini calendar to reflect event changes
      updateMiniCalendar();
    }

    // One-time event listener setup with protection
    let eventListenersSetup = false;

    function setupEventListeners() {

      // Remove any existing event listeners by cloning elements
      const elements = [
        'editCalendarTitleBtn', 'editCalendarDescriptionBtn',
        'saveCalendarTitleBtn', 'saveCalendarDescriptionBtn',
        'cancelCalendarTitleBtn', 'cancelCalendarDescriptionBtn'
      ];

      elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
          const newElement = element.cloneNode(true);
          element.parentNode.replaceChild(newElement, element);
        }
      });

      // Now attach fresh event listeners
      document.getElementById('editCalendarTitleBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        enableEdit('title');
      });

      document.getElementById('editCalendarDescriptionBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        enableEdit('description');
      });

      document.getElementById('saveCalendarTitleBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        saveEdit('title');
      });

      document.getElementById('saveCalendarDescriptionBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        saveEdit('description');
      });

      document.getElementById('cancelCalendarTitleBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        cancelEdit('title');
      });

      document.getElementById('cancelCalendarDescriptionBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        cancelEdit('description');
      });
    }

    // Reset all edit states
    function resetEditStates() {
      // Title field
      document.getElementById('modalCalendarTitle').readOnly = true;
      document.getElementById('editCalendarTitleBtn').classList.remove('d-none');
      document.getElementById('calendarTitleActions').classList.add('d-none');

      // Description field
      document.getElementById('modalCalendarDescription').readOnly = true;
      document.getElementById('editCalendarDescriptionBtn').classList.remove('d-none');
      document.getElementById('calendarDescriptionActions').classList.add('d-none');
    }

    // Enable editing for a field
    function enableEdit(fieldType) {
      if (fieldType === 'title') {
        document.getElementById('modalCalendarTitle').readOnly = false;
        document.getElementById('modalCalendarTitle').focus();
        document.getElementById('editCalendarTitleBtn').classList.add('d-none');
        document.getElementById('calendarTitleActions').classList.remove('d-none');
      } else if (fieldType === 'description') {
        document.getElementById('modalCalendarDescription').readOnly = false;
        document.getElementById('modalCalendarDescription').focus();
        document.getElementById('editCalendarDescriptionBtn').classList.add('d-none');
        document.getElementById('calendarDescriptionActions').classList.remove('d-none');
      }
    }

    // Cancel editing and revert changes
    function cancelEdit(fieldType) {
      if (fieldType === 'title') {
        document.getElementById('modalCalendarTitle').value = originalCalendarTitle;
      } else if (fieldType === 'description') {
        document.getElementById('modalCalendarDescription').value = originalCalendarDescription;
      }
      resetEditStates();
    }


    // Save changes to database
    async function saveEdit(fieldType) {
      const newTitle = document.getElementById('modalCalendarTitle').value.trim();
      const newDescription = document.getElementById('modalCalendarDescription').value.trim();

      // Validate title if we're saving title
      if (fieldType === 'title' && !newTitle) {
        showToast('Calendar title cannot be empty', 'error');
        return;
      }

      try {
        const response = await fetch(`/api/admin/requisition-forms/${currentRequestId}/calendar-info`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${adminToken}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            calendar_title: newTitle,
            calendar_description: newDescription
          })
        });

        if (!response.ok) {
          throw new Error(`Failed to update calendar info: ${response.status}`);
        }

        const result = await response.json();

        // Update original values
        originalCalendarTitle = newTitle;
        originalCalendarDescription = newDescription;

        // UPDATE THE LOCAL allRequests ARRAY
        const requestIndex = allRequests.findIndex(req => req.request_id === currentRequestId);
        if (requestIndex !== -1) {
          // Update the calendar info in the local array
          if (!allRequests[requestIndex].form_details.calendar_info) {
            allRequests[requestIndex].form_details.calendar_info = {};
          }
          allRequests[requestIndex].form_details.calendar_info.title = newTitle;
          allRequests[requestIndex].form_details.calendar_info.description = newDescription;
        }

        // Update modal title if calendar title was changed
        if (fieldType === 'title') {
          document.getElementById('eventModalTitle').textContent =
            `Request ID #${String(currentRequestId).padStart(4, '0')} (${newTitle})`;
        }

        resetEditStates();
        showToast(`Calendar ${fieldType} updated successfully`, 'success');

        // Refresh calendar events to reflect changes
        updateCalendarEvents();

      } catch (error) {
        console.error(`Error updating calendar ${fieldType}:`, error);
        showToast(`Failed to update calendar ${fieldType}`, 'error');

        // Revert to original values on error
        if (fieldType === 'title') {
          document.getElementById('modalCalendarTitle').value = originalCalendarTitle;
        } else {
          document.getElementById('modalCalendarDescription').value = originalCalendarDescription;
        }
        resetEditStates();
      }
    }

    // Show event details in modal
    function showEventModal(request) {
      const formattedRequestId = String(request.request_id).padStart(4, '0');
      const calendarTitle = request.form_details.calendar_info?.title || 'No Calendar Title';
      const calendarDescription = request.form_details.calendar_info?.description || 'No description';

      document.getElementById('eventModalTitle').textContent =
        `Request ID #${formattedRequestId} (${calendarTitle})`;

      // Set calendar title and description
      document.getElementById('modalCalendarTitle').value = calendarTitle;
      document.getElementById('modalCalendarDescription').value = calendarDescription;

      // Store current request ID and original values
      currentRequestId = request.request_id;
      originalCalendarTitle = calendarTitle;
      originalCalendarDescription = calendarDescription;

      // Reset edit states to ensure fields are read-only initially
      resetEditStates();

      // Set other modal content
      document.getElementById('modalRequester').textContent =
        `${request.user_details.first_name} ${request.user_details.last_name}`;
      document.getElementById('modalPurpose').textContent = request.form_details.purpose;
      document.getElementById('modalParticipants').textContent = request.form_details.num_participants;
      document.getElementById('modalStatus').innerHTML = `
            <span class="badge" style="background-color: ${request.form_details.status.color}">
                ${request.form_details.status.name}
            </span>
        `;
      document.getElementById('modalFee').textContent = `₱${request.fees.approved_fee}`;
      document.getElementById('modalApprovals').textContent = `${request.approval_info.approval_count}`;
      document.getElementById('modalRejections').textContent = `${request.approval_info.rejection_count}`;

      // Format requested items
      let itemsHtml = '';

      if (request.requested_items.facilities.length > 0) {
        itemsHtml += '<div class="fw-bold small mb-1">Facilities:</div>';
        itemsHtml += request.requested_items.facilities.map(f =>
          `<div class="mb-1 small">• ${f.name} | ₱${f.fee}${f.rate_type === 'Per Hour' ? '/hour' : '/event'}${f.is_waived ? ' <span class="text-muted">(Waived)</span>' : ''}</div>`
        ).join('');
      }

      if (request.requested_items.equipment.length > 0) {
        itemsHtml += '<div class="fw-bold small mt-2 mb-1">Equipment:</div>';
        itemsHtml += request.requested_items.equipment.map(e =>
          `<div class="mb-1 small">• ${e.name} × ${e.quantity || 1} | ₱${e.fee}${e.rate_type === 'Per Hour' ? '/hour' : '/event'}${e.is_waived ? ' <span class="text-muted">(Waived)</span>' : ''}</div>`
        ).join('');
      }

      document.getElementById('modalItems').innerHTML = itemsHtml || '<p class="text-muted small">No items requested</p>';

      // Set up view details button
      document.getElementById('modalViewDetails').onclick = function() {
        window.location.href = `/admin/requisition/${request.request_id}`;
      };

      eventModal.show();
    }

    // Add this function to fetch facilities
    async function fetchFacilities() {
      try {
        const response = await fetch('/api/facilities', {
          headers: {
            'Authorization': `Bearer ${adminToken}`,
            'Accept': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error('Failed to fetch facilities');
        }

        const data = await response.json();
        allFacilities = data.data || [];
        renderFacilityFilters();
      } catch (error) {
        console.error('Error fetching facilities:', error);
        showToast('Failed to load facility filters', 'error');
      }
    }

    function renderFacilityFilters() {
      const facilityFilterList = document.getElementById('facilityFilterList');
      if (!facilityFilterList) return;

      facilityFilterList.innerHTML = '';

      // "All Facilities" option
      const allFacilitiesItem = document.createElement('div');
      allFacilitiesItem.className = 'facility-item';
      allFacilitiesItem.innerHTML = `
        <div class="form-check">
            <input class="form-check-input facility-filter" type="checkbox" id="allFacilities" value="All" checked>
            <label class="form-check-label" for="allFacilities">All Facilities</label>
        </div>
    `;
      facilityFilterList.appendChild(allFacilitiesItem);

      // Render facility checkboxes
      allFacilities.forEach(facility => {
        // Get the ID - use facility_id from the /api/facilities response
        const facilityId = facility.facility_id || facility.id;
        const facilityName = facility.facility_name || facility.name;

        if (!facilityId) {
          console.warn('Facility missing ID:', facility);
          return;
        }

        const facilityItem = document.createElement('div');
        facilityItem.className = 'facility-item mb-1';
        facilityItem.innerHTML = `
            <div class="form-check">
                <input class="form-check-input facility-filter" type="checkbox" 
                       id="facility${facilityId}" 
                       value="${facilityId}"
                       data-name="${facilityName}">
                <label class="form-check-label small" for="facility${facilityId}" 
                       title="${facilityName}">
                    ${facilityName.length > 25 ? 
                      facilityName.substring(0, 25) + '...' : 
                      facilityName}
                </label>
            </div>
        `;
        facilityFilterList.appendChild(facilityItem);
      });

      // Add event listeners for facility filtering
      setupFacilityFilterListeners();
    }

    // Add this function to setup facility filter event listeners
    function setupFacilityFilterListeners() {
      const allFacilitiesCheckbox = document.getElementById('allFacilities');
      const facilityCheckboxes = Array.from(document.querySelectorAll('.facility-filter')).filter(
        cb => cb.id !== 'allFacilities'
      );

      // Initialize selectedFacilityIds
      selectedFacilityIds = [];

      // When "All Facilities" is checked/unchecked
      if (allFacilitiesCheckbox) {
        allFacilitiesCheckbox.addEventListener('change', function() {
          if (this.checked) {
            // Uncheck all individual facility checkboxes
            facilityCheckboxes.forEach(cb => {
              cb.checked = false;
            });
            selectedFacilityIds = [];
          }
          updateCalendarEvents();
        });
      }

      // When individual facility checkboxes change
      facilityCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
          const facilityId = this.value;
          const facilityName = this.dataset.name;

          if (this.checked) {
            // Uncheck "All Facilities"
            if (allFacilitiesCheckbox) {
              allFacilitiesCheckbox.checked = false;
            }
            // Add to selected facilities if not already there
            if (!selectedFacilityIds.includes(facilityId)) {
              selectedFacilityIds.push(facilityId);
              console.log(`Added facility: ${facilityId} (${facilityName})`);
            }
          } else {
            // Remove from selected facilities
            selectedFacilityIds = selectedFacilityIds.filter(id => id !== facilityId);
            console.log(`Removed facility: ${facilityId} (${facilityName})`);

            // If no facilities selected, check "All Facilities"
            if (selectedFacilityIds.length === 0 && allFacilitiesCheckbox) {
              allFacilitiesCheckbox.checked = true;
            }
          }

          console.log('Currently selected facility IDs:', selectedFacilityIds);
          updateCalendarEvents();
        });
      });
    }

    // Fetch all requisition forms
    async function fetchRequisitionForms() {
      try {
        // Generate skeleton days immediately
        generateSkeletonDays();

        // Fetch both requisition forms and facilities in parallel
        const [formsResponse, facilitiesResponse] = await Promise.all([
          fetch('/api/admin/requisition-forms', {
            headers: {
              'Authorization': `Bearer ${adminToken}`,
              'Accept': 'application/json'
            }
          }),
          fetch('/api/facilities', {
            headers: {
              'Authorization': `Bearer ${adminToken}`,
              'Accept': 'application/json'
            }
          })
        ]);

        if (!formsResponse.ok) {
          throw new Error(`Failed to fetch requisition forms: ${formsResponse.status}`);
        }

        allRequests = await formsResponse.json();
        console.log('Loaded requests:', allRequests.length);

        if (facilitiesResponse.ok) {
          const facilitiesData = await facilitiesResponse.json();
          allFacilities = facilitiesData.data || [];
          console.log('Loaded facilities:', allFacilities.length);
        } else {
          console.warn('Failed to fetch facilities, continuing without facility filter');
          allFacilities = [];
        }



        // Initialize everything
        initializeMiniCalendar();
        initializeCalendar();
        renderFacilityFilters();

        // Hide skeletons and show content
        hideSkeletons();

      } catch (error) {
        console.error('Error fetching data:', error);
        showToast('Failed to load calendar data', 'error');

        // Still hide skeletons even on error
        hideSkeletons();
      }
    }

    // Event filter change handler
    document.querySelectorAll('.event-filter-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', updateCalendarEvents);
    });

    // Simple toast notification function
    window.showToast = function(message, type = 'success', duration = 3000) {
      const toast = document.createElement('div');
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

      const bsToast = new bootstrap.Toast(toast, {
        autohide: false
      });
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

    // Initialize everything
    fetchRequisitionForms();
    setTimeout(() => {
      setupEventListeners();
    }, 100);
  });

  // Add Reservation Modal functionality
async function initializeAddReservationModal() {
    const modal = new bootstrap.Modal(document.getElementById('addReservationModal'));
    
    // Set today as default start date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('startDate').value = today;
    document.getElementById('endDate').value = today;
    
    // Generate initial access code
    generateAccessCode();
    
    // Load purposes
    await loadPurposes();
    
    // Load facilities and equipment
    await loadFacilitiesForReservation();
    await loadEquipmentForReservation();
    
    // Set up event listeners
    setupReservationEventListeners();
    
    return modal;
}

function generateAccessCode() {
    // Generate a random 6-character alphanumeric code
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 6; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('accessCodeInput').value = code;
}

async function loadPurposes() {
    try {
        const response = await fetch('/api/requisition-purposes', {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const purposes = await response.json();
            const select = document.getElementById('purposeSelect');
            select.innerHTML = '<option value="">Select Purpose</option>';
            
            purposes.forEach(purpose => {
                const option = document.createElement('option');
                option.value = purpose.purpose_id;
                option.textContent = purpose.purpose_name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading purposes:', error);
    }
}

async function loadFacilitiesForReservation() {
    try {
        const response = await fetch('/api/facilities', {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const facilities = await response.json();
            const facilitiesList = document.getElementById('facilitiesList');
            facilitiesList.innerHTML = '';
            
            facilities.data.forEach(facility => {
                const div = document.createElement('div');
                div.className = 'form-check mb-2';
                div.innerHTML = `
                    <input class="form-check-input facility-checkbox" type="checkbox" 
                           id="facility_${facility.facility_id}" value="${facility.facility_id}"
                           data-name="${facility.facility_name}">
                    <label class="form-check-label" for="facility_${facility.facility_id}">
                        ${facility.facility_name} (₱${facility.external_fee}${facility.rate_type === 'Per Hour' ? '/hour' : '/event'})
                    </label>
                `;
                facilitiesList.appendChild(div);
            });
        }
    } catch (error) {
        console.error('Error loading facilities:', error);
    }
}

async function loadEquipmentForReservation() {
    try {
        const response = await fetch('/api/equipment', {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const equipmentList = await response.json();
            const container = document.getElementById('equipmentList');
            container.innerHTML = '';
            
            equipmentList.forEach(equipment => {
                const div = document.createElement('div');
                div.className = 'card mb-2';
                div.innerHTML = `
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-0">${equipment.equipment_name}</h6>
                                <small class="text-muted">₱${equipment.external_fee}${equipment.rate_type === 'Per Hour' ? '/hour' : '/event'} each</small>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary decrement" data-id="${equipment.equipment_id}">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control form-control-sm mx-2 equipment-quantity" 
                                           data-id="${equipment.equipment_id}" data-name="${equipment.equipment_name}"
                                           data-fee="${equipment.external_fee}" data-rate-type="${equipment.rate_type}"
                                           value="0" min="0" max="${equipment.quantity_available || 10}" style="width: 60px;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary increment" data-id="${equipment.equipment_id}">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });
        }
    } catch (error) {
        console.error('Error loading equipment:', error);
    }
}

function setupReservationEventListeners() {
    // Generate access code button
    document.getElementById('generateAccessCode').addEventListener('click', generateAccessCode);
    
    // Select all facilities
    document.getElementById('selectAllFacilities').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.facility-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
    
    // Equipment quantity controls
    document.addEventListener('click', function(e) {
        if (e.target.closest('.increment')) {
            const button = e.target.closest('.increment');
            const input = button.parentElement.querySelector('.equipment-quantity');
            const max = parseInt(input.getAttribute('max'));
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        }
        
        if (e.target.closest('.decrement')) {
            const button = e.target.closest('.decrement');
            const input = button.parentElement.querySelector('.equipment-quantity');
            if (parseInt(input.value) > 0) {
                input.value = parseInt(input.value) - 1;
            }
        }
    });
    
    // Save reservation
    document.getElementById('saveReservation').addEventListener('click', saveReservation);
}

async function saveReservation() {
    const form = document.getElementById('addReservationForm');
    const formData = new FormData(form);
    
    // Get selected facilities
    const selectedFacilities = Array.from(document.querySelectorAll('.facility-checkbox:checked'))
        .map(cb => ({
            facility_id: parseInt(cb.value),
            name: cb.dataset.name
        }));
    
    // Get equipment with quantities
    const selectedEquipment = Array.from(document.querySelectorAll('.equipment-quantity'))
        .filter(input => parseInt(input.value) > 0)
        .map(input => ({
            equipment_id: parseInt(input.dataset.id),
            name: input.dataset.name,
            quantity: parseInt(input.value),
            fee: parseFloat(input.dataset.fee),
            rate_type: input.dataset.rateType
        }));
    
    // Validate form
    if (!validateReservationForm(formData, selectedFacilities, selectedEquipment)) {
        return;
    }
    
    // Prepare data for API
    const reservationData = {
        user_type: formData.get('user_type'),
        first_name: formData.get('first_name'),
        last_name: formData.get('last_name'),
        email: formData.get('email'),
        organization_name: formData.get('organization_name'),
        contact_number: formData.get('contact_number'),
        purpose_id: parseInt(formData.get('purpose_id')),
        num_participants: parseInt(formData.get('num_participants')),
        access_code: formData.get('access_code'),
        start_date: formData.get('start_date'),
        end_date: formData.get('end_date'),
        start_time: formData.get('start_time') + ':00', // Add seconds
        end_time: formData.get('end_time') + ':00', // Add seconds
        calendar_title: formData.get('calendar_title') || `Admin Reservation - ${formData.get('first_name')} ${formData.get('last_name')}`,
        calendar_description: formData.get('calendar_description'),
        additional_requests: formData.get('additional_requests'),
        facilities: selectedFacilities,
        equipment: selectedEquipment,
        status_id: 3, // Assuming 3 is for "Scheduled" or appropriate status
        is_admin_created: true
    };
    
    try {
        const response = await fetch('/api/requisition-forms', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify(reservationData)
        });
        
        if (response.ok) {
            const result = await response.json();
            showToast('Reservation created successfully!', 'success');
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addReservationModal')).hide();
            
            // Clear form
            form.reset();
            
            // Refresh calendar and admin reservations list
            await fetchRequisitionForms();
            await loadAdminReservations();
            
        } else {
            const error = await response.json();
            showToast(`Error: ${error.message || 'Failed to create reservation'}`, 'error');
        }
    } catch (error) {
        console.error('Error saving reservation:', error);
        showToast('Failed to create reservation. Please try again.', 'error');
    }
}

function validateReservationForm(formData, facilities, equipment) {
    // Basic validation
    const requiredFields = ['user_type', 'first_name', 'last_name', 'email', 'purpose_id', 'num_participants', 'start_date', 'end_date', 'start_time', 'end_time'];
    
    for (const field of requiredFields) {
        if (!formData.get(field)) {
            showToast(`Please fill in the ${field.replace('_', ' ')} field`, 'error');
            return false;
        }
    }
    
    // Validate dates
    const startDate = new Date(formData.get('start_date') + ' ' + formData.get('start_time'));
    const endDate = new Date(formData.get('end_date') + ' ' + formData.get('end_time'));
    
    if (endDate <= startDate) {
        showToast('End date/time must be after start date/time', 'error');
        return false;
    }
    
    // Validate at least one facility or equipment
    if (facilities.length === 0 && equipment.length === 0) {
        showToast('Please select at least one facility or equipment item', 'error');
        return false;
    }
    
    return true;
}

// Load admin reservations list
async function loadAdminReservations() {
    try {
        const response = await fetch('/api/admin/requisition-forms', {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const allRequests = await response.json();
            const adminReservationsList = document.getElementById('adminReservationsList');
            
            // Filter for admin-created or recent reservations (you might want to add a flag to identify admin-created)
            const adminReservations = allRequests.slice(0, 5); // Show latest 5
            
            if (adminReservations.length > 0) {
                let html = '';
                adminReservations.forEach(reservation => {
                    const startDate = new Date(reservation.schedule.start_date + 'T' + reservation.schedule.start_time);
                    const endDate = new Date(reservation.schedule.end_date + 'T' + reservation.schedule.end_time);
                    
                    html += `
                        <div class="card border mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">${reservation.user_details.first_name} ${reservation.user_details.last_name}</h6>
                                        <p class="text-muted small mb-1">
                                            ${reservation.form_details.purpose} • 
                                            ${startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} 
                                            ${startDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}
                                        </p>
                                        <p class="mb-0 small">
                                            Facilities: ${reservation.requested_items.facilities.map(f => f.name).join(', ')}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge" style="background-color: ${reservation.form_details.status.color}">
                                            ${reservation.form_details.status.name}
                                        </span>
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary view-details" data-id="${reservation.request_id}">
                                                View
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                adminReservationsList.innerHTML = html;
                
                // Add event listeners to view buttons
                document.querySelectorAll('.view-details').forEach(button => {
                    button.addEventListener('click', function() {
                        const requestId = this.dataset.id;
                        window.location.href = `/admin/requisition/${requestId}`;
                    });
                });
            } else {
                adminReservationsList.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                        <p class="text-muted mb-0">No reservations added yet.</p>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Error loading admin reservations:', error);
    }
}

// Add to your existing initialization
async function initializeEverything() {
    await fetchRequisitionForms();
    
    // Initialize the reservation modal
    await initializeAddReservationModal();
    
    // Load admin reservations
    await loadAdminReservations();
    
    // Set up event listeners for the "Add new" button
    document.querySelector('[data-bs-target="#addReservationModal"]').addEventListener('click', function() {
        // Reset and prepare modal
        generateAccessCode();
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('startDate').value = today;
        document.getElementById('endDate').value = today;
    });
}

// Call this instead of just fetchRequisitionForms()
initializeEverything();


</script>
@endsection