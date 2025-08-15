document.addEventListener('DOMContentLoaded', () => {
    console.log('Checking token...');
    const token = localStorage.getItem('adminToken');
    console.log('Token exists:', !!token);
    
    if (!token) {
        window.location.href = '/admin/admin-login';
        return;
    }

    fetch('http://127.0.0.1:8000/api/admin/profile', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        credentials: 'include'  // Critical for Sanctum
    })
    .then(response => {
        if (!response.ok) {
            console.log('HTTP Status:', response.status);
            return response.text().then(text => {
                console.log('Response:', text);
                throw new Error('Session expired or invalid');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Authenticated as', data.admin?.email);
    })
    .catch(error => {
        console.error('Authentication error:', error);
        localStorage.removeItem('adminToken');
        window.location.href = '/admin/admin-login';
    });
});

// Logout functionality
document.getElementById('logoutLink')?.addEventListener('click', async (e) => {
    e.preventDefault();
    
    const token = localStorage.getItem('adminToken');
    if (!token) {
        window.location.href = '/admin/admin-login';
        return;
    }

    try {
        await fetch('http://127.0.0.1:8000/api/admin/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            credentials: 'include'  // Critical for Sanctum
        });
    } catch (error) {
        console.error('Logout error:', error);
    } finally {
        localStorage.removeItem('adminToken');
        window.location.href = '/admin/admin-login';
    }
});