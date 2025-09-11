@extends('layouts.admin')

@section('title', 'Events Calendar')

@section('content')
  <!-- Combined CSS resources -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Use alternative FullCalendar CSS source -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet">
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

    /* Calendar container adjustments */
    .calendar-container {
      height: 100%;
      min-height: 350px;
      overflow: hidden;
    }

    #calendar {
      height: 100% !important;
      font-size: 0.9rem;
    }

    /* Allow event text to wrap inside timeGrid events */
    .fc-timegrid-event .fc-event-title,
    .fc-timegrid-event .fc-event-time {
      white-space: normal;
      overflow-wrap: break-word;
      word-break: break-word;
    }

    .fc-timegrid-event {
      height: auto !important;
      min-height: 2.5em;
    }
    
    /* Ensure calendar is visible */
    .fc {
      height: 100%;
    }
    
    /* Fix for datepicker display */
    .datepicker {
      z-index: 1000 !important;
    }
    
    /* FullCalendar overrides */
    .fc .fc-toolbar {
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    
    .fc .fc-toolbar-title {
      font-size: 1.25rem;
      margin: 0;
    }
    
    .fc .fc-button {
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
    }

    /* Ensure proper calendar container sizing */
    .fc .fc-view-harness {
      min-height: 400px;
    }

    /* Fix for calendar rendering */
    .fc-view {
      height: 100% !important;
    }

    /* Loading state for calendar */
    .calendar-loading {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 400px;
      background-color: #f8f9fa;
      border-radius: 0.375rem;
    }
  </style>
  <!-- Main Layout -->
  <div id="layout">
    <!-- Calendar Section -->
    <main id="main">
      <section class="d-flex" style="height: calc(100vh - 60px);">
        <!-- Left Section: Mini Calendar and Event List -->
        <div class="me-4" style="width: 300px; flex-shrink: 0;">
          <!-- Mini Datepicker Calendar -->
          <div id="miniCalendar" class="td-mini-calendar"></div>

          <div style="height: 50%; overflow-y: auto; margin-top: 20px;">
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
        <div style="flex-grow: 1; height: 100%;">
          <div id="calendar" class="border p-2 calendar-container" style="height: 100%;">
            <div class="calendar-loading" id="calendarLoading">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading calendar...</span>
              </div>
            </div>
          </div>
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
            <li class="list-group-item" id="eventDetails">
              <!-- Additional event details will be populated here -->
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Mini Calendar with Bootstrap Datepicker
      $('#miniCalendar').datepicker({
        format: "mm/dd/yyyy",
        todayHighlight: true,
        weekStart: 0, // Sunday
        autoclose: true
      }).on('changeDate', function(e) {
        // When a date is selected, switch the main calendar to day view
        if (typeof calendar !== 'undefined') {
          const selectedDate = e.date;
          calendar.gotoDate(selectedDate);
          calendar.changeView('timeGridDay');
        }
      });

      // Show the calendar immediately
      $('#miniCalendar').datepicker('show');

      // Initialize FullCalendar
      const calendarElement = document.getElementById('calendar');
      const calendarLoading = document.getElementById('calendarLoading');
      
      // Check if calendar element exists
      if (!calendarElement) {
        console.error('Calendar element not found');
        return;
      }
      
      const calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: 'dayGridMonth', // Default to month view
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay' // Add all three view options
        },
        views: {
          timeGridDay: {
            titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
            dayHeaderFormat: { weekday: 'long' }
          },
          timeGridWeek: {
            titleFormat: { year: 'numeric', month: 'long', day: 'numeric' }
          }
        },
        height: '100%', // Use full container height
        slotMinTime: '07:00:00', // Start calendar at 7am
        slotMaxTime: '20:00:00', // End calendar at 8pm
        nowIndicator: true, // Show current time indicator
        allDaySlot: false, // Hide all-day slot
        buttonText: {
          today: 'Today',
          month: 'Month',
          week: 'Week',
          day: 'Day'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
          // Get the authentication token
          const token = localStorage.getItem('adminToken');
          
          if (!token) {
            console.error('No authentication token found');
            failureCallback('Authentication required');
            return;
          }
          
          // Fetch requisition forms from API
          fetch('http://127.0.0.1:8000/api/admin/requisition-forms', {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            credentials: 'include'
          })
          .then(response => {
            if (!response.ok) {
              console.error('API request failed with status:', response.status);
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('API response data:', data); // Debug logging
            
            // Transform API data to FullCalendar events
            const events = data.map(form => {
              // Only include scheduled/ongoing events in the calendar
              if (!form.form_details || !form.form_details.status || 
                  !['Scheduled', 'Ongoing'].includes(form.form_details.status.name)) {
                return null;
              }
              
              // Check if required schedule properties exist
              if (!form.schedule || !form.schedule.start_date || !form.schedule.start_time) {
                console.warn('Missing schedule data for form:', form.request_id);
                return null;
              }
              
              try {
                const startDateTime = new Date(`${form.schedule.start_date}T${form.schedule.start_time}`);
                const endDateTime = new Date(`${form.schedule.end_date}T${form.schedule.end_time}`);
                
                return {
                  id: form.request_id,
                  title: (form.form_details.calendar_info && form.form_details.calendar_info.title) || 
                         form.form_details.purpose || 'Untitled Event',
                  start: startDateTime,
                  end: endDateTime,
                  extendedProps: {
                    description: (form.form_details.calendar_info && form.form_details.calendar_info.description) || 
                                'No description available.',
                    requester: form.user_details ? 
                              `${form.user_details.first_name} ${form.user_details.last_name}` : 
                              'Unknown',
                    purpose: form.form_details.purpose || 'No purpose specified',
                    status: form.form_details.status.name || 'Unknown',
                    facilities: form.requested_items && form.requested_items.facilities ? 
                               form.requested_items.facilities.map(f => f.name).join(', ') : 
                               'None',
                    equipment: form.requested_items && form.requested_items.equipment ? 
                              form.requested_items.equipment.map(e => e.name).join(', ') : 
                              'None'
                  }
                };
              } catch (error) {
                console.error('Error processing event data for form:', form.request_id, error);
                return null;
              }
            }).filter(event => event !== null);
            
            console.log('Processed events:', events); // Debug logging
            successCallback(events);
          })
          .catch(error => {
            console.error('Error fetching calendar events:', error);
            failureCallback('Failed to load events');
          });
        },
        eventClick: function(info) {
          const modalElement = document.getElementById('eventModal');
          if (!modalElement) {
            console.error('Event modal not found');
            return;
          }
          
          const modal = new bootstrap.Modal(modalElement);
          document.getElementById('eventTitle').textContent = info.event.title;
          
          const startDate = info.event.start;
          const endDate = info.event.end || startDate;
          
          document.getElementById('eventDate').textContent = startDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
          
          const timeOptions = { hour: '2-digit', minute: '2-digit' };
          const startTime = startDate.toLocaleTimeString('en-US', timeOptions);
          const endTime = endDate.toLocaleTimeString('en-US', timeOptions);
          
          document.getElementById('eventTime').textContent = `${startTime} - ${endTime}`;
          document.getElementById('eventDescription').textContent = info.event.extendedProps.description;
          
          // Add additional event details to modal
          const eventDetails = document.getElementById('eventDetails');
          if (eventDetails) {
            eventDetails.innerHTML = `
              <li class="list-group-item">
                <strong>Requester:</strong> ${info.event.extendedProps.requester}
              </li>
              <li class="list-group-item">
                <strong>Purpose:</strong> ${info.event.extendedProps.purpose}
              </li>
              <li class="list-group-item">
                <strong>Status:</strong> ${info.event.extendedProps.status}
              </li>
              <li class="list-group-item">
                <strong>Facilities:</strong> ${info.event.extendedProps.facilities || 'None'}
              </li>
              <li class="list-group-item">
                <strong>Equipment:</strong> ${info.event.extendedProps.equipment || 'None'}
              </li>
            `;
          }
          
          modal.show();
        },
        loading: function(isLoading) {
          if (isLoading) {
            calendarLoading.style.display = 'flex';
          } else {
            calendarLoading.style.display = 'none';
          }
        }
      });
      
      calendar.render();

      // Force calendar to update its size after rendering
      setTimeout(() => {
        calendar.updateSize();
      }, 100);

      // Also update size when window resizes
      window.addEventListener('resize', () => {
        calendar.updateSize();
      });

      // Event Filtering with Checkboxes
      const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
      if (filterCheckboxes.length > 0) {
        filterCheckboxes.forEach(checkbox => {
          checkbox.addEventListener('change', (event) => {
            const activeFilters = Array.from(document.querySelectorAll('.filter-checkbox:checked'))
              .map(cb => cb.getAttribute('data-filter'));

            calendar.getEvents().forEach(event => {
              if (activeFilters.length === 0 || activeFilters.includes(event.classNames[0].replace('event-', ''))) {
                event.setProp('display', 'auto');
              } else {
                event.setProp('display', 'none');
              }
            });
          });
        });
      }

      // Show All Button Functionality
      const showAllButton = document.getElementById('showAllButton');
      if (showAllButton) {
        showAllButton.addEventListener('click', () => {
          document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.checked = false;
          });

          calendar.getEvents().forEach(event => {
            event.setProp('display', 'auto');
          });
        });
      }
    });
  </script>
@endsection