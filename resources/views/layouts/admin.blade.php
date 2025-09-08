<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CPU Booking')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        .text-primary {
            color: var(--cpu-primary) !important;
        }

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
            background: #ffffff;
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
            background: linear-gradient(180deg, #eceef3ff 0%, #f8f8f8 50%, #f1f1f1 100%);
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
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1030;
            /* Box shadow for depth */
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.02);
        }

        #sidebar .nav-link {
            color: #5b6a7aff;
            margin-bottom: -1px;
            transition: all 0.2s ease;
            font-weight: 500 !important;

        }

        #sidebar .nav-link:hover {
            background-color: rgba(80, 128, 206, 0.1);
        }

        #sidebar .nav-link.active {
            background-color: rgba(80, 128, 206, 0.1);
        }


        /* Topbar Styles */
        #topbar {
            background-color: rgba(30, 83, 153, 0.94) !important;
            backdrop-filter: blur(8px);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1rem;
            height: 64px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            margin-left: 250px;
            width: calc(100% - 250px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1030;
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
            background: rgba(0, 0, 0, 0.1);
        }

        /* Layout Responsiveness */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.active {
                transform: translateX(0);
            }

            #topbar {
                margin-left: 0;
                width: 100%;
            }

            .sidebar-toggle {
                display: block;
            }
        }

        @media (max-width: 575.98px) {
            #sidebar {
                width: 100%;
                max-width: 280px;
            }

            .profile-img {
                width: 80px !important;
                height: 80px !important;
            }
        }

        /* Main Content */
        #layout {
            margin-left: 250px;
            display: flex;
            flex: 1;
        }

main {
  padding: 20px;
  padding-top: 50px;
  width: 90%;       /* reduce width */
 margin-left: auto;
}

        /* Smooth transitions for topbar */
        .transition-all {
            transition: all 0.3s ease-in-out;
        }

        /* Hide the topbar when scrolled down */
        .topbar-hidden {
            transform: translateY(-100%);
        }


    </style>
</head>

<body>
    <button class="btn sidebar-toggle" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
    {{-- Header --}}
    <header id="topbar" class="d-flex justify-content-between align-items-center fixed-top transition-all">
        <!-- Display the current page title in the header -->
        <div class="d-flex align-items-center">
            <span class="fw brand-text"">
            @yield('title', 'CPU Facilities and Equipment Management')
            </span>
        </div>
    <div class=" d-flex align-items-center">
                <!-- Dropdown Menu -->
                <div class="dropdown">
                    <button class="btn btn-link p-0 text-white" id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-three-dots three-dots-icon"></i>

                        <style>
                            .three-dots-icon {
                                background: rgba(255, 255, 255, 0);
                                width: 2rem;
                                height: 2rem;
                                display: flex;
                                align-items: center;
                                justify-content: center;

                                border-radius: 50%;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                color: #ffffffff !important;

                                font-size: 1.5rem;
                                /* adjust size so dots are sharp */
                                line-height: 1;
                                /* prevent ghost line below */
                                display: inline-flex;
                                /* fixes rendering artifacts */
                            }

                            .three-dots-icon:hover {
                                background: rgba(190, 201, 211, 0.3);
                                transform: scale(1.05);
                            }

                            .three-dots-icon:active {
                                transform: scale(0.95);
                                /* press-down effect */
                                background: rgba(190, 201, 211, 0.5);
                            }
                        </style>

                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" style='color: darkred' href="{{ url('/admin/admin-login') }}"
                                id="logoutLink"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
        </div>
    </header>

    {{-- Sidebar + Main Content --}}



    <nav id="sidebar" class="d-flex flex-column">
        <!-- Profile Section -->
        <div class="text-center mb-4 px-3 pt-4">
            <div class="position-relative d-inline-block mb-3">
                <div id="profile-img-container" class="position-relative">
                    <div id="profile-skeleton" class="skeleton skeleton-circle" style="width: 80px; height: 80px;">
                    </div>
                    <img id="admin-profile-img"
                        src="https://res.cloudinary.com/dn98ntlkd/image/upload/v1751033911/ksdmh4mmpxdtjogdgjmm.png"
                        class="rounded-circle border border-3 border-white shadow-sm"
                        style="width: 80px; height: 80px; object-fit: cover; display: none;">

                    <div class="status-indicator bg-success"></div>
                </div>
            </div>
            <div id="name-skeleton" class="skeleton skeleton-text mx-auto mb-2" style="width: 120px;"></div>
            <h5 class="mb-1 fw-semibold" id="admin-name" style="display: none">
                <a href="#" class="text-decoration-none text-dark admin-profile-link">Loading...</a>
            </h5>
            <div id="role-skeleton" class="skeleton skeleton-text mx-auto mb-3" style="width: 80px;"></div>
            <p class="text-muted small mb-0" id="admin-role" style="display: none">Loading...</p>
        </div>

        <ul class="nav flex-column px-2 flex-grow-1">
            <li class="nav-item mb-1">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/dashboard*') ? 'active' : '' }}"
                    href="{{ url('/admin/dashboard') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="bi bi-speedometer2 me-2"></i>
                        </div>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/calendar*') ? 'active' : '' }}"
                    href="{{ url('/admin/calendar') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="bi bi-calendar-event me-1"></i>
                        </div>
                        <span>Calendar</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/manage-requests*') ? 'active' : '' }}"
                    href="{{ url('/admin/manage-requests') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="bi bi-file-earmark-text me-2"></i>
                        </div>
                        <span>Requisitions</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/manage-facilities*') ? 'active' : '' }}"
                    href="{{ url('/admin/manage-facilities') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="bi bi-building me-2"></i>
                        </div>
                        <span>Facilities</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/manage-equipment*') ? 'active' : '' }}"
                    href="{{ url('/admin/manage-equipment') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="bi bi-tools me-2"></i>
                        </div>
                        <span>Equipment</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/admin-roles*') ? 'active' : '' }}"
                    href="{{ url('/admin/admin-roles') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="bi bi-people me-2"></i>
                        </div>
                        <span>Administrators</span>
                    </div>
                </a>
            </li>
        </ul>

        <!-- Footer -->
        <div class="mt-auto p-3 text-center border-top">
            <small class="text-muted">&copy; {{ date('Y') }} Central Philippine University</small>
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
                    const nameElement = document.querySelector('#admin-name a');
                    nameElement.textContent = `${data.first_name} ${data.last_name}`;
                    nameElement.href = `/admin/profile/${data.admin_id}`;
                    document.getElementById('admin-name').style.display = 'block';

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


    <main>
        @yield('content')
    </main>


    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const topbar = document.getElementById('topbar');
            let lastScroll = 0;

            window.addEventListener('scroll', () => {
                const currentScroll = window.scrollY;

                // Hide on scroll down, show on scroll up
                if (currentScroll > 100 && currentScroll > lastScroll) {
                    topbar.classList.add('topbar-hidden');
                } else if (currentScroll < lastScroll) {
                    topbar.classList.remove('topbar-hidden');
                }

                lastScroll = currentScroll;
            });

            // Show on hover
            topbar.addEventListener('mouseenter', () => {
                topbar.classList.remove('topbar-hidden');
            });
        });
    </script>
</body>

</html>