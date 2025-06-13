document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('adminToken');
    
    if (!token) {
        window.location.href = 'adminlogin.html';
        return;
    }

    // Verify token with backend
    fetch('http://127.0.0.1:8000/api/admin/profile', {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Invalid token');
        }
        return response.json();
    })
    .then(data => {
        // Update UI with admin data if needed
        console.log('Welcome', data.admin);
    })
    .catch(error => {
        localStorage.removeItem('adminToken');
        window.location.href = 'adminlogin.html';
    });
});

// Only keep logout functionality
document.getElementById('logoutLink')?.addEventListener('click', (e) => {
    e.preventDefault();
    
    const token = localStorage.getItem('adminToken');
    if (!token) return;

    fetch('http://127.0.0.1:8000/api/admin/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`
        }
    })
    .finally(() => {
        localStorage.removeItem('adminToken');
        window.location.href = 'adminlogin.html';
    });
});
