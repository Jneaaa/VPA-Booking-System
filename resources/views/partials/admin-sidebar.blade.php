<style>
    .skeleton {
        background: #eee;
        background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
        background-size: 200% 100%;
        animation: 1.5s shine linear infinite;
    }
    
    @keyframes shine {
        to {
            background-position-x: -200%;
        }
    }
    
    .skeleton-circle {
        border-radius: 50%;
    }
    
    .skeleton-text {
        height: 1em;
        border-radius: 4px;
    }
</style>

<nav id="sidebar">

    <div class="text-center mb-4">
        <div class="position-relative d-inline-block">
            <div id="profile-img-container">
                <div id="profile-skeleton" class="skeleton skeleton-circle" 
                     style="width: 100px; height: 100px; display: block;"></div>
                <img id="admin-profile-img" src="{{ asset('images/default-admin.png') }}" 
                     alt="Admin Profile" 
                     class="profile-img rounded-circle"
                     style="width: 100px; height: 100px; object-fit: cover; display: none;">
            </div>
        </div>
        <div id="name-skeleton" class="skeleton skeleton-text mx-auto mt-3 mb-1" 
             style="width: 150px; display: block;"></div>
        <h5 class="mt-3 mb-1" id="admin-name" style="display: none">Loading...</h5>
        
        <div id="role-skeleton" class="skeleton skeleton-text mx-auto" 
             style="width: 100px; display: block;"></div>
        <p class="text-muted mb-0" id="admin-role" style="display: none">Loading...</p>
    </div>


    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/calendar*') ? 'active' : '' }}" href="{{ url('/admin/calendar') }}">
                <i class="bi bi-calendar-event me-2"></i>
                Calendar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/manage-requests*') ? 'active' : '' }}" href="{{ url('/admin/manage-requests') }}">
                <i class="bi bi-file-earmark-text me-2"></i>
                Requisitions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/manage-facilities*') ? 'active' : '' }}" href="{{ url('/admin/manage-facilities') }}">
                <i class="bi bi-building me-2"></i>
                Facilities
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/manage-equipment*') ? 'active' : '' }}" href="{{ url('/admin/manage-equipment') }}">
                <i class="bi bi-tools me-2"></i>
                Equipment
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('admin/admin-roles*') ? 'active' : '' }}" href="{{ url('/admin/admin-roles') }}">
                <i class="bi bi-people me-2"></i>
                Admin Roles
            </a>
        </li>
    </ul>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('adminToken');
    if (!token) return;

    fetch('http://127.0.0.1:8000/api/admin/profile', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        // Preload the image
        if (data.photo_url) {
            const img = new Image();
            img.onload = () => {
                document.getElementById('profile-skeleton').style.display = 'none';
                document.getElementById('admin-profile-img').src = data.photo_url;
                document.getElementById('admin-profile-img').style.display = 'block';
            };
            img.src = data.photo_url;
        } else {
            document.getElementById('profile-skeleton').style.display = 'none';
            document.getElementById('admin-profile-img').style.display = 'block';
        }
        
        // Update name
        document.getElementById('name-skeleton').style.display = 'none';
        const nameElement = document.getElementById('admin-name');
        nameElement.textContent = `${data.first_name} ${data.last_name}`;
        nameElement.style.display = 'block';
        
        // Update role
        document.getElementById('role-skeleton').style.display = 'none';
        const roleElement = document.getElementById('admin-role');
        roleElement.textContent = data.role ? data.role.role_title : 'Admin';
        roleElement.style.display = 'block';
    })
    .catch(error => {
        console.error('Error fetching profile:', error);
        // Show error state or fallback content
        document.getElementById('profile-skeleton').style.display = 'none';
        document.getElementById('name-skeleton').style.display = 'none';
        document.getElementById('role-skeleton').style.display = 'none';
        document.getElementById('admin-profile-img').style.display = 'block';
        document.getElementById('admin-name').style.display = 'block';
        document.getElementById('admin-role').style.display = 'block';
    });
});
</script>
