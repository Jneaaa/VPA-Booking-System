document.addEventListener('DOMContentLoaded', () => {
    
    console.log('Checking token...');
    const token = localStorage.getItem('adminToken');
    console.log('Token exists:', !!token);
    
    if (!token) {
        window.location.href = 'adminlogin.html';
        return;
    }

    // Verify token with backend
    fetch('http://127.0.0.1:8000/api/admin/profile', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Session expired or invalid');
        }
        return response.json();
    })
    .then(data => {
        // You can use the admin data here if needed
        console.log('Authenticated as', data.admin.email);
    })
    .catch(error => {
        console.error('Authentication error:', error);
        localStorage.removeItem('adminToken');
        window.location.href = 'adminlogin.html';
    });
});

// Logout functionality
document.getElementById('logoutLink')?.addEventListener('click', async (e) => {
    e.preventDefault();
    
    const token = localStorage.getItem('adminToken');
    if (!token) {
        window.location.href = 'adminlogin.html';
        return;
    }

    try {
        await fetch('http://127.0.0.1:8000/api/admin/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
    } catch (error) {
        console.error('Logout error:', error);
    } finally {
        localStorage.removeItem('adminToken');
        window.location.href = 'adminlogin.html';
    }
});