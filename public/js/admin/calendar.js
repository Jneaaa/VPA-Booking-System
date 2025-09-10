// calendar.js (jQuery-free version)
document.addEventListener('DOMContentLoaded', function() {
    const calendarElement = document.getElementById('calendar');
    
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
        height: '600px', // Explicitly set height to ensure visibility
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
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Transform API data to FullCalendar events
                const events = data.map(form => {
                    // Only include scheduled/ongoing events in the calendar
                    if (!['Scheduled', 'Ongoing'].includes(form.form_details.status.name)) {
                        return null;
                    }
                    
                    const startDateTime = new Date(`${form.schedule.start_date}T${form.schedule.start_time}`);
                    const endDateTime = new Date(`${form.schedule.end_date}T${form.schedule.end_time}`);
                    
                    return {
                        id: form.request_id,
                        title: form.form_details.calendar_info.title || form.form_details.purpose,
                        start: startDateTime,
                        end: endDateTime,
                        extendedProps: {
                            description: form.form_details.calendar_info.description || 'No description available.',
                            requester: `${form.user_details.first_name} ${form.user_details.last_name}`,
                            purpose: form.form_details.purpose,
                            status: form.form_details.status.name,
                            facilities: form.requested_items.facilities.map(f => f.name).join(', '),
                            equipment: form.requested_items.equipment.map(e => e.name).join(', ')
                        }
                    };
                }).filter(event => event !== null);
                
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
        }
    });
    calendar.render();

    // Event Filtering with Checkboxes - only if elements exist
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

    // Show All Button Functionality - only if button exists
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