// Notification functionality
document.addEventListener('DOMContentLoaded', function() {
    const notificationItems = document.querySelectorAll('#notificationDropdown .notification-item');
    const notificationBadge = document.querySelector('.notification-badge');
    
    notificationItems.forEach(item => {
      item.addEventListener('click', function(e) {
        if (this.classList.contains('unread')) {
          this.classList.remove('unread');
          updateNotificationBadge();
        }
        // Prevent default only if it's not the "View all" link
        if (!this.classList.contains('view-all-item')) {
          e.preventDefault();
          // In real implementation, you would handle the click:
          // window.location.href = '/request-details?id=' + this.dataset.notificationId;
        }
      });
    });
    
    function updateNotificationBadge() {
      const unreadCount = document.querySelectorAll('#notificationDropdown .notification-item.unread').length;
      
      if (unreadCount > 0) {
        notificationBadge.textContent = unreadCount;
        notificationBadge.style.display = 'flex';
      } else {
        notificationBadge.style.display = 'none';
      }
    }
});

// System log filtering functionality
document.addEventListener('DOMContentLoaded', function() {
  const logContainer = document.getElementById('systemLog');
  const roleDropdownItems = document.querySelectorAll('#adminRoleDropdown .dropdown-item');
  const dateFilterInput = document.getElementById('logDateFilter');

  roleDropdownItems.forEach(item => {
    item.addEventListener('click', function() {
      const filterRole = this.getAttribute('data-filter');
      filterLogs(filterRole, dateFilterInput.value);
    });
  });

  dateFilterInput.addEventListener('change', function() {
    const selectedDate = this.value;
    const activeRoleFilter = document.querySelector('#adminRoleDropdown .dropdown-item.active')?.getAttribute('data-filter') || 'all';
    filterLogs(activeRoleFilter, selectedDate);
  });

  function filterLogs(role, date) {
    const logItems = logContainer.querySelectorAll('.list-group-item');
    logItems.forEach(item => {
      const matchesRole = role === 'all' || item.textContent.toLowerCase().includes(role.toLowerCase());
      const matchesDate = !date || item.textContent.includes(new Date(date).toLocaleDateString('en-US'));
      item.style.display = matchesRole && matchesDate ? 'block' : 'none';
    });
  }
});
