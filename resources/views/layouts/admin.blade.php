<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CPU Booking')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        #markAllAsRead {
            color: #2664b6ff !important;
            text-decoration: none;
        }

        #markAllAsRead:hover {
            color: #14407aff !important;
        }


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
            background: #e6e8ebff;
        }

        .fc-daygrid-day-number {
            text-decoration: none;
            color: var(--cpu-text-dark);
        }

        .fc-day-today {
            background: #f3f4f7ff !important;
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
            --btn-primary-hover: #0f4c8aff;
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
            font-family: 'Poppins', 'Century Gothic', Arial, sans-serif;
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

        /* Global thin scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
            /* for horizontal scrollbars */
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.35);
        }

        /* Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }


        #sidebar .nav-link {
            color: #5b6a7aff;
            margin-bottom: -1px;
            transition: all 0.2s ease;
            flex: 1;
            text-align: left;
        }

        #sidebar .badge {
            margin-left: auto;
        }


        #sidebar .nav-link:hover {
            background-color: rgba(80, 128, 206, 0.1);
        }

        #sidebar .nav-link.active {
            background-color: rgba(80, 128, 206, 0.1);
            font-weight: 500;
        }

        /* Hidden nav items */
        .nav-item.hidden {
            display: none !important;
            margin-bottom: 4px;
        }

        #sidebar .nav-link .nav-icon {
            width: 24px;
            text-align: center;
        }

        #sidebar .nav-link span {
            flex: 1;
            text-align: left;
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



        main {
            width: 100%;
        }

        /* Smooth transitions for topbar */
        .transition-all {
            transition: all 0.3s ease-in-out;
        }

        /* Hide the topbar when scrolled down */
        .topbar-hidden {
            transform: translateY(-100%);
        }

        /* Adjust main content padding when topbar is fixed */
        main {
            padding-top: 70px;
            /* Match topbar height + some spacing */
        }

        /* Preserve your original three dots style */
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
            line-height: 1;
            display: inline-flex;
        }

        .three-dots-icon:hover {
            background: rgba(190, 201, 211, 0.3);
            transform: scale(1.05);
        }

        .three-dots-icon:active {
            transform: scale(0.95);
            background: rgba(190, 201, 211, 0.5);
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
            color: white;
        }

        .back-button:hover {
            background: rgba(190, 201, 211, 0.3);
            transform: scale(1.05);
        }

        .back-button:active {
            transform: scale(0.95);
            background: rgba(190, 201, 211, 0.5);
        }
    </style>
</head>

<body>
    <button class="btn sidebar-toggle" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
    {{-- Header --}}
    <header id="topbar" class="d-flex justify-content-between align-items-center fixed-top transition-all">

        <!-- Left side: back button + current page title -->
        <div class="d-flex align-items-center gap-2">

            {{-- Back button --}}
            <a href="{{ url()->previous() }}" class="back-button me-0">
                <i class="bi bi-caret-left-fill"></i>
            </a>


            {{-- Current page title --}}
            <span class="fw-bold brand-text">
                @yield('title', 'CPU Facilities and Equipment Management')
            </span>
        </div>

        <!-- Right side: notification bell + dropdown menu -->
        <div class="d-flex align-items-center gap-3">
            <!-- Notification Bell -->
            <div class="dropdown">
                <button class="btn btn-link p-0 text-white position-relative" id="notificationDropdownButton"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-inbox three-dots-icon"></i>
                    <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end" id="notificationDropdown" style="width: 350px;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        <button class="btn btn-sm btn-link p-0" id="markAllAsRead">Mark all as read</button>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <div id="notificationList" class="px-2" style="max-height: 300px; overflow-y: auto;">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span class="ms-2">Loading notifications...</span>
                        </div>
                    </div>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="text-center">
                        <a href="{{ url('/admin/manage-requests') }}" class="dropdown-item text-primary">View All
                            Requisitions</a>
                    </li>
                </ul>
            </div>

            <!-- User Menu Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link p-0 text-white" id="dropdownMenuButton" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-three-dots three-dots-icon"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="{{ url('/admin/admin-login') }}" id="logoutLink">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a></li>
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
                    <img id="admin-profile-img" class="rounded-circle border border-3 border-white shadow-sm"
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
            <li class="nav-item mb-1 nav-link-skeleton" id="dashboard-nav-skeleton">
                <div class="d-flex align-items-center py-1 px-2">
                    <div class="skeleton skeleton-circle me-2" style="width: 20px; height: 20px;"></div>
                    <div class="skeleton skeleton-text" style="width: 85px; height: 16px;"></div>
                </div>
            </li>
            <li class="nav-item mb-1" id="dashboard-nav-item" style="display: none;">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/dashboard*') ? 'active' : '' }}"
                    href="{{ url('/admin/dashboard') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="fa-solid fa-house me-2"></i>
                        </div>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <li class="nav-item mb-1 nav-link-skeleton" id="calendar-nav-skeleton">
                <div class="d-flex align-items-center py-1 px-2">
                    <div class="skeleton skeleton-circle me-2" style="width: 20px; height: 20px;"></div>
                    <div class="skeleton skeleton-text" style="width: 70px; height: 16px;"></div>
                </div>
            </li>
            <li class="nav-item mb-1" id="calendar-nav-item" style="display: none;">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/calendar*') ? 'active' : '' }}"
                    href="{{ url('/admin/calendar') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="fa-solid fa-calendar me-1"></i>
                        </div>
                        <span>Events Calendar</span>
                    </div>
                </a>
            </li>

            <li class="nav-item mb-1 nav-link-skeleton" id="requisitions-nav-skeleton">
                <div class="d-flex align-items-center py-1 px-2">
                    <div class="skeleton skeleton-circle me-2" style="width: 20px; height: 20px;"></div>
                    <div class="skeleton skeleton-text" style="width: 80px; height: 16px;"></div>
                </div>
            </li>
            <li class="nav-item mb-1" id="requisitions-nav-item" style="display: none;">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/manage-requests*') ? 'active' : '' }}"
                    href="{{ url('/admin/manage-requests') }}" id="requisitionsNavLink">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="nav-icon p-1 rounded me-2">
                                <i class="fa-solid fa-file-lines me-2"></i>
                            </div>
                            <span>Requisitions</span>
                        </div>
                        <span id="requisitionNotificationBadge" class="badge bg-danger ms-2"
                            style="display: none;">0</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-1 nav-link-skeleton" id="facilities-nav-skeleton">
                <div class="d-flex align-items-center py-1 px-2">
                    <div class="skeleton skeleton-circle me-2" style="width: 20px; height: 20px;"></div>
                    <div class="skeleton skeleton-text" style="width: 70px; height: 16px;"></div>
                </div>
            </li>
            <li class="nav-item mb-1" id="facilities-nav-item" style="display: none;">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/manage-facilities*') ? 'active' : '' }}"
                    href="{{ url('/admin/manage-facilities') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="fa-solid fa-landmark me-2"></i>
                        </div>
                        <span>Facilities</span>
                    </div>
                </a>
            </li>

            <li class="nav-item mb-1 nav-link-skeleton" id="equipment-nav-skeleton">
                <div class="d-flex align-items-center py-1 px-2">
                    <div class="skeleton skeleton-circle me-2" style="width: 20px; height: 20px;"></div>
                    <div class="skeleton skeleton-text" style="width: 75px; height: 16px;"></div>
                </div>
            </li>
            <li class="nav-item mb-1" id="equipment-nav-item" style="display: none;">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/manage-equipment*') ? 'active' : '' }}"
                    href="{{ url('/admin/manage-equipment') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="fa-solid fa-box-archive me-2"></i>
                        </div>
                        <span>Equipment</span>
                    </div>
                </a>
            </li>

            <li class="nav-item mb-1 nav-link-skeleton" id="administrators-nav-skeleton">
                <div class="d-flex align-items-center py-1 px-2">
                    <div class="skeleton skeleton-circle me-2" style="width: 20px; height: 20px;"></div>
                    <div class="skeleton skeleton-text" style="width: 100px; height: 16px;"></div>
                </div>
            </li>
            <!-- Archive Skeleton -->
            <li class="nav-item mb-1 nav-link-skeleton" id="archive-nav-skeleton">
                <div class="d-flex align-items-center py-1 px-2">
                    <div class="skeleton skeleton-circle me-2" style="width: 20px; height: 20px;"></div>
                    <div class="skeleton skeleton-text" style="width: 65px; height: 16px;"></div>
                </div>
            </li>

            <!-- Archive Nav Item -->
            <li class="nav-item mb-1" id="archive-nav-item" style="display: none;">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/archives*') ? 'active' : '' }}"
                    href="{{ url('/admin/archives') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="fa-solid fa-box-archive me-2"></i>
                        </div>
                        <span>Archives</span>
                    </div>
                </a>
            </li>
            <li class="nav-item mb-1" id="administrators-nav-item" style="display: none;">
                <a class="nav-link py-1 px-2 rounded-2 {{ Request::is('admin/admin-roles*') ? 'active' : '' }}"
                    href="{{ url('/admin/admin-roles') }}">
                    <div class="d-flex align-items-center">
                        <div class="nav-icon p-1 rounded me-2">
                            <i class="fa-solid fa-user-gear me-2"></i>
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

        class NotificationManager {
            constructor() {
                this.pollingInterval = null;
                this.isInitialized = false;
                this.init();
            }

            init() {
                if (this.isInitialized) return;

                this.loadNotifications();
                this.setupEventListeners();
                this.startPolling();
                this.isInitialized = true;
            }

            setupEventListeners() {
                // Mark all as read
                document.getElementById('markAllAsRead')?.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.markAllAsRead();
                });

                // Mark as read when clicking requisitions nav link
                document.getElementById('requisitionsNavLink')?.addEventListener('click', () => {
                    this.markAllAsRead();
                });

                // Mark as read when clicking notification bell
                document.getElementById('notificationDropdownButton')?.addEventListener('click', () => {
                    this.loadNotifications();
                });

                // Close notifications when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('#notificationDropdown') &&
                        !e.target.closest('#notificationDropdownButton')) {
                        const dropdown = document.getElementById('notificationDropdown');
                        if (dropdown?.classList.contains('show')) {
                            this.loadNotifications(); // Refresh count when closing
                        }
                    }
                });
            }

            async loadNotifications() {
                try {
                    const token = localStorage.getItem('adminToken');
                    if (!token) return;

                    const response = await fetch('/api/admin/notifications', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch notifications');

                    const data = await response.json();
                    this.updateNotificationUI(data);
                } catch (error) {
                    console.error('Error loading notifications:', error);
                }
            }

            updateNotificationUI(data) {
                const { notifications, unread_count } = data;

                // Update badge counts
                this.updateBadge('notificationBadge', unread_count);
                this.updateBadge('requisitionNotificationBadge', unread_count);

                // Update notification list
                this.renderNotificationList(notifications);
            }

            updateBadge(elementId, count) {
                const badge = document.getElementById(elementId);
                if (!badge) return;

                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }

            renderNotificationList(notifications) {
                const container = document.getElementById('notificationList');
                if (!container) return;

                if (notifications.length === 0) {
                    container.innerHTML = '<div class="text-center py-3 text-muted">No notifications</div>';
                    return;
                }

                container.innerHTML = notifications.map(notification => `
            <div class="notification-item mb-2 p-2 rounded ${notification.is_read ? '' : 'bg-light'}" 
                 style="cursor: pointer; border-left: 3px solid ${notification.is_read ? 'transparent' : '#007bff'};"
                 onclick="notificationManager.markAsRead(${notification.notification_id})">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="small text-muted">${this.formatTime(notification.created_at)}</div>
                        <div class="fw-medium">${notification.message}</div>
                        ${notification.request_id ?
                        `<small class="text-primary">Request #${notification.request_id}</small>` : ''}
                    </div>
                    ${!notification.is_read ?
                        '<span class="badge bg-primary ms-2">New</span>' : ''}
                </div>
            </div>
        `).join('');
            }

            async markAsRead(notificationId = null) {
                try {
                    const token = localStorage.getItem('adminToken');
                    if (!token) return;

                    const url = notificationId ?
                        `/api/admin/notifications/mark-read/${notificationId}` :
                        '/api/admin/notifications/mark-all-read';

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.loadNotifications(); // Refresh notifications

                        // If marking single notification and dropdown is open, refresh list
                        if (notificationId) {
                            const dropdown = document.getElementById('notificationDropdown');
                            if (dropdown?.classList.contains('show')) {
                                this.loadNotifications();
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            }

            async markAllAsRead() {
                await this.markAsRead();
            }

            formatTime(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMins / 60);
                const diffDays = Math.floor(diffHours / 24);

                if (diffMins < 1) return 'Just now';
                if (diffMins < 60) return `${diffMins}m ago`;
                if (diffHours < 24) return `${diffHours}h ago`;
                if (diffDays < 7) return `${diffDays}d ago`;

                return date.toLocaleDateString();
            }

            startPolling() {
                // Poll every 30 seconds for new notifications
                this.pollingInterval = setInterval(() => {
                    this.loadNotifications();
                }, 30000);
            }

            stopPolling() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                }
            }
        }

        // Initialize notification manager
        const notificationManager = new NotificationManager();

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

                    // Hide sidebar items based on role
                    hideSidebarItemsBasedOnRole(data.role ? data.role.role_id : null);
                    // Initialize notifications after profile is loaded
                    notificationManager.init();
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

                    // If there's an error, show all nav items by default
                    showAllNavItems();
                });
        });

        function hideSidebarItemsBasedOnRole(roleId) {
            // Hide all skeletons first
            hideAllSkeletons();

            const dashboardNavItem = document.getElementById('dashboard-nav-item');
            const calendarNavItem = document.getElementById('calendar-nav-item');
            const requisitionsNavItem = document.getElementById('requisitions-nav-item');
            const facilitiesNavItem = document.getElementById('facilities-nav-item');
            const equipmentNavItem = document.getElementById('equipment-nav-item');
            const archiveNavItem = document.getElementById('archive-nav-item'); // NEW
            const administratorsNavItem = document.getElementById('administrators-nav-item');

            // Show Dashboard and Calendar for all roles (they're always visible)
            if (dashboardNavItem) dashboardNavItem.style.display = 'block';
            if (calendarNavItem) calendarNavItem.style.display = 'block';

            // Show all other items first
            if (requisitionsNavItem) requisitionsNavItem.style.display = 'block';
            if (facilitiesNavItem) facilitiesNavItem.style.display = 'block';
            if (equipmentNavItem) equipmentNavItem.style.display = 'block';
            if (archiveNavItem) archiveNavItem.style.display = 'block'; // NEW
            if (administratorsNavItem) administratorsNavItem.style.display = 'block';

            // Hide items based on role
            switch (roleId) {
                case 2: // Vice President of Administration
                case 3: // Approving Officer
                    if (facilitiesNavItem) facilitiesNavItem.style.display = 'none';
                    if (equipmentNavItem) equipmentNavItem.style.display = 'none';
                    if (archiveNavItem) archiveNavItem.style.display = 'none'; // NEW
                    if (administratorsNavItem) administratorsNavItem.style.display = 'none';
                    break;
                case 4: // Inventory Manager
                    if (requisitionsNavItem) requisitionsNavItem.style.display = 'none';
                    if (administratorsNavItem) administratorsNavItem.style.display = 'none';
                    break;
                // Head Admin (roleId 1) and any other roles get full access
                default:
                    // All items remain visible
                    break;
            }
        }


        function hideAllSkeletons() {
            const skeletons = [
                'dashboard-nav-skeleton',
                'calendar-nav-skeleton',
                'requisitions-nav-skeleton',
                'facilities-nav-skeleton',
                'equipment-nav-skeleton',
                'archive-nav-skeleton', // NEW
                'administrators-nav-skeleton'
            ];

            skeletons.forEach(id => {
                const skeleton = document.getElementById(id);
                if (skeleton) skeleton.style.display = 'none';
            });
        }


        function showAllNavItems() {
            // Hide skeletons
            hideAllSkeletons();

            // Show all nav items
            const navItems = [
                'dashboard-nav-item',
                'calendar-nav-item',
                'requisitions-nav-item',
                'facilities-nav-item',
                'equipment-nav-item',
                'archive-nav-item', // NEW
                'administrators-nav-item'
            ];

            navItems.forEach(id => {
                const item = document.getElementById(id);
                if (item) item.style.display = 'block';
            });
        }
    </script>

    <main style="margin-left: 250px; padding: 20px; width: calc(100% - 250px);">
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