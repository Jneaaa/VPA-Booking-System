document.addEventListener('DOMContentLoaded', function() {
    // Initialize Calendar
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
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
      height: 450, // Increase height to accommodate time slots
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
          start: new Date(new Date().setHours(9, 0, 0)),
          end: new Date(new Date().setHours(11, 0, 0)),
          description: 'A university-wide event for all students.',
          classNames: ['event-university']
        },
        {
          id: '2',
          title: 'Facility Rental',
          start: new Date(new Date().setDate(new Date().getDate() + 2)),
          end: new Date(new Date().setDate(new Date().getDate() + 2)),
          description: 'Rental of the university gymnasium.',
          classNames: ['event-facility']
        },
        {
          id: '3',
          title: 'Equipment Rental',
          start: new Date(new Date().setDate(new Date().getDate() + 5)),
          end: new Date(new Date().setDate(new Date().getDate() + 5)),
          description: 'Rental of sound system equipment.',
          classNames: ['event-equipment']
        },
        {
          id: '4',
          title: 'External Conference',
          start: new Date(new Date().setDate(new Date().getDate() + 7)),
          end: new Date(new Date().setDate(new Date().getDate() + 7)),
          description: 'An external conference hosted by a partner organization.',
          classNames: ['event-external']
        }
      ],
      eventClick: function(info) {
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        document.getElementById('eventTitle').textContent = info.event.title;
        
        // Format date based on whether it's an all-day event
        const startDate = info.event.start;
        const endDate = info.event.end || startDate;
        
        document.getElementById('eventDate').textContent = startDate.toLocaleDateString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
        
        // Format time if not all-day
        const timeOptions = { hour: '2-digit', minute: '2-digit' };
        const startTime = startDate.toLocaleTimeString('en-US', timeOptions);
        const endTime = endDate.toLocaleTimeString('en-US', timeOptions);
        document.getElementById('eventTime').textContent = `${startTime} - ${endTime}`;
        
        document.getElementById('eventDescription').textContent = info.event.extendedProps.description || 'No description available.';
        modal.show();
      }
    });
    calendar.render();

    // Event Filtering
    document.querySelectorAll('[data-filter]').forEach(filter => {
      filter.addEventListener('click', () => {
        const filterType = filter.getAttribute('data-filter');
        
        calendar.getEvents().forEach(event => {
          if (filterType === 'all') {
            event.setProp('display', 'auto');
          } else {
            event.setProp('display', event.classNames.includes(`event-${filterType}`) ? 'auto' : 'none');
          }
        });
      });
    });
  });