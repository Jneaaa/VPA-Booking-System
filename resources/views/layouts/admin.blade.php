<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CPU Booking')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-styles.css') }}" />
    <style>

        .p {
            color: #333333;
        }

        .btn-primary {
            background-color: var(--btn-primary);
            border: 1px solid transparent;
        }

        .btn-primary:hover {
            background-color: var(--btn-primary-hover);
            border: 1px solid transparent;
        }

        .btn-secondary {
            background-color: transparent;
            color: #666666ff;
            border: 1px transparent;
        }

        .btn-secondary:hover {
            background-color: lightgray;
            color: var(--cpu-text-dark);
            border: 1px transparent;
        }

        /* FullCalendar Styles */

        #calendar {
            background: #eee;
        } 

        .fc-daygrid-body {
            background: #f8f9fa;
        }

        .fc-col-header-cell-cushion {
            text-decoration: none;
            color: var(--cpu-text-dark);
        }

        .fc-col-header-cell {
            background: #f1f1f1ff;
        }

        .fc-daygrid-day-number {
            text-decoration: none;
            color: var(--cpu-text-dark);
        }

        .fc-event {
            color: var(--cpu-text-dark);
        }

        .fc-day-today {
            background: #d5d8dfff !important;
        }

        /* Essential Skeleton Loading */
        .skeleton {
            background: #eee;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }
        
        @keyframes shine {
            to { background-position-x: -200%; }
        }
        
        .skeleton-circle { border-radius: 50%; }
        .skeleton-text { 
            height: 1em;
            border-radius: 4px;
        }

        /* Root Variables */
        :root {
            --cpu-primary: #003366;
            --cpu-primary-hover: #004a94;
            --btn-primary: #135ba3;
            --btn-primary-hover: #2673c0;
            --cpu-secondary: #f2b123;
            --cpu-text-dark: #333333;
            --light-gray: #f8f9fa;
        }

        /* Base Layout */
        body {
            display: flex;
            background: #ffffffff;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: helvetica, sans-serif, Arial;
            color: var(--cpu-text-dark);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            background: #fafafaff;
            padding: 1rem;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1030;
            /* Box shadow for depth */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        #sidebar .nav-link {
            color: rgb(82, 82, 82);
            margin-bottom: -1px;
            transition: all 0.2s ease;
            
        }

        #sidebar .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }

        #sidebar .nav-link.active {
            color: #346391;
        }

        /* Topbar Styles */
        #topbar {
            background: var(--cpu-primary);
            border-bottom: 3px solid var(--cpu-secondary);
            color: var(--light-gray);
            font-style: italic;
            padding: 0.75rem 1rem;
            height: 45px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            margin-left: 250px;
            width: calc(100% - 250px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1020;
        }

        /* Profile Image */
        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .status-indicator {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: green;
            border: 2px solid white;
            border-radius: 50%;
            width: 15px;
            height: 15px;
        }

        /* Notification Styles */
        #notificationDropdown {
            width: 300px !important;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Add this new style for the sidebar toggle */
        .sidebar-toggle {
            display: none;
            position: fixed;
            left: 1rem;
            top: 0.75rem;
            z-index: 1031;
            padding: 0.25rem 0.75rem;
            font-size: 1.25rem;
            background: transparent;
            border: none;
            color: var(--cpu-text-dark);
        }

        .sidebar-toggle:hover {
            background: rgba(0,0,0,0.1);
        }

        /* Layout Responsiveness */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.active { transform: translateX(0); }
            #topbar {
                margin-left: 0;
                width: 100%;
            }
            .sidebar-toggle { 
                display: block; 
            }
        }

        @media (max-width: 575.98px) {
            #sidebar { width: 100%; max-width: 280px; }
            .profile-img {
                width: 80px !important;
                height: 80px !important;
            }
        }

        /* Main Content */
        #layout {
            margin-left: 250px;
            margin-top: 60px;
            display: flex;
            flex: 1;
        }

        main {
            padding: 20px;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <button class="btn sidebar-toggle" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
    {{-- Header --}}
    <header id="topbar" class="d-flex justify-content-between align-items-center">
        <!-- Display the current page title in the header -->
        <div class="d-flex align-items-center">
            <span class="fw brand-text"">
            @yield('title', 'CPU Facilities and Equipment Management')
            </span>
        </div>
    <div class="d-flex align-items-center">
      <!-- Dropdown Menu -->
      <div class="dropdown">
        <button class="btn btn-link p-0 text-white" id="dropdownMenuButton" data-bs-toggle="dropdown"
          aria-expanded="false">
          <i class="bi bi-three-dots fs-4"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="{{ url('/admin/admin-login') }}" id="logoutLink"><i
                class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </header>

    {{-- Sidebar + Main Content --}}

    

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
    <div class="mt-auto w-100 text-center" style="position: absolute; bottom: 10px; left: 0; padding: 0.5rem;">
        <small class="text-muted">&copy; {{ date('Y') }} CPU Facilities and Equipment Management</small>
    </div>
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

    <main style="margin-left: 250px; padding: 20px; width: calc(100% - 250px);">
        @yield('content')
    </main>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="{{ asset('js/admin/authentication.js') }}"></script>

    @yield('scripts')

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 991.98 && sidebar?.classList.contains('active')) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>

</body>

</html>