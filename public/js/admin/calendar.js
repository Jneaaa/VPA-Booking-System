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
        events: [], // Empty events array - mock events removed
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
            document.getElementById('eventDescription').textContent = info.event.extendedProps.description || 'No description available.';
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