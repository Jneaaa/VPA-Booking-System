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