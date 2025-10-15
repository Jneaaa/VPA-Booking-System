{{-- calendar.blade.php --}}
@extends('layouts.admin')
@section('title', 'Ongoing Events')
@section('content')
  <style>
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

    /* Skeleton-specific height constraints */
    .col-lg-3 .skeleton-container {
      min-height: auto !important;
      height: auto !important;
    }

    /* Limit skeleton calendar height */
    .col-lg-3 .card:first-child .skeleton-container {
      max-height: 200px;
      /* Reduced from ~300px */
    }

    /* Limit skeleton filter height */
    .col-lg-3 .card:last-child .skeleton-container {
      max-height: 100px;
      /* Reduced from ~150px */
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
      height: 500px;
      /* Match calendar height */
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
                    <button class="btn btn-sm btn-outline-secondary prev-month" type="button">
                      <i class="bi bi-chevron-left"></i>
                    </button>
                    <h6 class="mb-0 month-year" id="currentMonthYear">October 2024</h6>
                    <button class="btn btn-sm btn-outline-secondary next-month" type="button">
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

          <!-- Events Filter Card (Separate Row) -->
          <div class="card">
            <div class="card-body">
              <!-- Skeleton for Events Filter -->
              <div class="skeleton-container">
                <div class="skeleton skeleton-title mb-3" style="height: 20px; width: 60%;"></div>
                <div class="d-flex align-items-center mb-2">
                  <div class="skeleton skeleton-checkbox"></div>
                  <div class="skeleton skeleton-text flex-grow-1" style="height: 14px;"></div>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <div class="skeleton skeleton-checkbox"></div>
                  <div class="skeleton skeleton-text flex-grow-1" style="height: 14px;"></div>
                </div>
              </div>

              <!-- Actual Events Filter Content -->
              <div class="calendar-content">
                <h6 class="fw-bold mb-3">Events Filter</h6>

                <div class="form-check mb-2">
                  <input class="form-check-input event-filter-checkbox scheduled-checkbox" type="checkbox" value="Scheduled"
                    id="filterScheduled" checked>
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
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: FullCalendar -->
        <div class="col-lg-9">
          <div class="card h-100">
            <div class="card-body p-3">
              <!-- Calendar Skeleton -->
              <div class="skeleton-container">
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
                <div class="skeleton skeleton-calendar" style="height: 400px; border-radius: 8px;"></div>
              </div>
              <!-- Actual Calendar Content -->
              <div class="calendar-content">
                <div id="calendar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 800px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="eventModalBody">
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

@endsection

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      let calendar;
      let currentDate = new Date();
      let allRequests = [];
      let filteredRequests = [];
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
        document.querySelector('.prev-month').addEventListener('click', function () {
          currentDate.setMonth(currentDate.getMonth() - 1);
          updateMiniCalendar();
        });

        document.querySelector('.next-month').addEventListener('click', function () {
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
          prevDayElement.addEventListener('click', function () {
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
          dayElement.addEventListener('click', function () {
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
          nextDayElement.addEventListener('click', function () {
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
          console.log('Navigating to date:', date);
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
          eventClick: function (info) {
            const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);
            if (request) {
              showEventModal(request);
            }
          },
          eventDidMount: function (info) {
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
          datesSet: function (info) {
            calendar.updateSize();
          },
          viewDidMount: function (info) {
            setTimeout(() => calendar.updateSize(), 0);
          },
          eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
          },
          slotMinTime: '00:00:00',
          slotMaxTime: '24:00:00',
          allDaySlot: false,
          nowIndicator: true,
          navLinks: true,
          dayHeaderFormat: { weekday: 'long', month: 'short', day: 'numeric' },
          views: {
            dayGridMonth: {
              dayHeaderFormat: {
                weekday: 'short'
              }
            }
          },
          eventDisplay: 'block',
          dayMaxEvents: true,
          moreLinkClick: 'popover'
        });

        calendar.render();
        updateCalendarEvents();
      }

      // Filter and update calendar events
      function updateCalendarEvents() {
        if (!calendar) return;

        calendar.removeAllEvents();

        // Filter requests by status
        filteredRequests = allRequests.filter(req => {
          const statusName = req.form_details.status.name;
          const showScheduled = document.getElementById('filterScheduled').checked;
          const showOngoing = document.getElementById('filterOngoing').checked;
          const showLate = document.getElementById('filterLate').checked;

          return (statusName === 'Scheduled' && showScheduled) ||
                (statusName === 'Ongoing' && showOngoing) ||
                (statusName === 'Late' && showLate);
        });

        console.log('Filtered calendar events:', {
          totalRequests: allRequests.length,
          filteredRequests: filteredRequests.length,
          filteredRequestIds: filteredRequests.map(r => r.request_id)
        });

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

      // Show event details in modal
      function showEventModal(request) {
        const formattedRequestId = String(request.request_id).padStart(4, '0');
        const calendarTitle = request.form_details.calendar_info?.title || 'No Calendar Title';

        document.getElementById('eventModalTitle').textContent =
          `Request ID #${formattedRequestId} (${calendarTitle})`;

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
        document.getElementById('modalViewDetails').onclick = function () {
          window.location.href = `/admin/requisition/${request.request_id}`;
        };

        eventModal.show();
      }

      // Fetch all requisition forms
      async function fetchRequisitionForms() {
        try {
          console.log('Fetching requisition forms...');

          // Generate skeleton days immediately
          generateSkeletonDays();

          const response = await fetch('http://127.0.0.1:8000/api/admin/requisition-forms', {
            headers: {
              'Authorization': `Bearer ${adminToken}`,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            throw new Error(`Failed to fetch requisition forms: ${response.status} ${response.statusText}`);
          }

          allRequests = await response.json();

          console.log('Fetched requisition forms:', {
            totalRequests: allRequests.length,
            requestIds: allRequests.map(r => r.request_id)
          });

          // Initialize calendars after data is loaded
          initializeMiniCalendar();
          initializeCalendar();

          // Hide skeletons and show content
          hideSkeletons();

        } catch (error) {
          console.error('Error fetching requisition forms:', error);
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

      // Initialize everything
      fetchRequisitionForms();
    });
  </script>
@endsection