document.addEventListener('DOMContentLoaded', function() {
    const calendarElement = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: 'timeGridWeek', // Default to week view
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
        height: '100%', // Ensure it fills the container height
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
        events: [
            {
                id: '1',
                title: 'CPU Event',
                start: new Date(new Date().setHours(9, 0, 0)), // Explicitly set hours and minutes
                end: new Date(new Date().setHours(11, 0, 0)), // Ensure accurate end time
                description: 'A university-wide event for all students.',
                classNames: ['event-university']
            },
            {
                id: '2',
                title: 'Facility Rental',
                start: new Date(new Date().setDate(new Date().getDate() + 2)).setHours(10, 0, 0), // Accurate day and time
                end: new Date(new Date().setDate(new Date().getDate() + 2)).setHours(12, 0, 0),
                description: 'Rental of the university gymnasium.',
                classNames: ['event-facility']
            },
            {
                id: '3',
                title: 'Equipment Rental',
                start: new Date(new Date().setDate(new Date().getDate() + 5)).setHours(14, 0, 0), // Accurate day and time
                end: new Date(new Date().setDate(new Date().getDate() + 5)).setHours(16, 0, 0),
                description: 'Rental of sound system equipment.',
                classNames: ['event-equipment']
            },
            {
                id: '4',
                title: 'External Conference',
                start: new Date(new Date().setDate(new Date().getDate() + 7)).setHours(9, 0, 0), // Accurate day and time
                end: new Date(new Date().setDate(new Date().getDate() + 7)).setHours(17, 0, 0),
                description: 'An external conference hosted by a partner organization.',
                classNames: ['event-external']
            }
        ],
        eventClick: function(info) {
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
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

    // Initialize Mini Calendar with Bootstrap Datepicker
    const miniCalendarElement = $('#miniCalendar');
    const savedDate = localStorage.getItem('miniCalendarDate'); // Retrieve saved date from localStorage

    miniCalendarElement.datepicker({
        format: "mm/dd/yyyy",
        todayHighlight: true,
        weekStart: 0, // Sunday
        autoclose: true
    }).on('changeDate', function(e) {
        // Sync selected date to the big calendar
        calendar.changeView('timeGridDay', e.date);

        // Save the selected date to localStorage
        localStorage.setItem('miniCalendarDate', e.date.toISOString());
    });

    // Set the mini calendar to the saved date if available
    if (savedDate) {
        miniCalendarElement.datepicker('setDate', new Date(savedDate));
    }

    // Event Filtering with Checkboxes
    document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
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

    // Show All Button Functionality
    document.getElementById('showAllButton').addEventListener('click', () => {
        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });

        calendar.getEvents().forEach(event => {
            event.setProp('display', 'auto');
        });
    });
});