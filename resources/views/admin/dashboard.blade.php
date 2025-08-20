@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <style>
        /* New styles for the dashboard header */
        .dashboard-header {
            position: relative;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
            overflow: hidden;
            color: white;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background-image: url("{{ asset('assets/cpu-pic1.jpg') }}");
            background-size: cover;
            background-position: center;
            z-index: -1;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 51, 102, 0.8), rgba(0, 51, 102, 0.5));
            z-index: -1;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>

    <div id="main-container">
        <!-- Main Content -->
        <main>
            <!-- Dashboard Header with Wallpaper -->
            <div class="dashboard-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0">Your Dashboard</h2>
                    <a href="#" id="manageProfileBtn" class="btn btn-light">
                        <i class="bi bi-gear me-1"></i> Manage Requests
                    </a>
                </div>
            </div>
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 hover-effect">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small">Total Requisitions</span>
                                    <h2 class="mt-2 mb-0 fw-bold">245</h2>
                                    <span class="badge bg-primary bg-opacity-10 text-primary mt-2">+12% from last
                                        month</span>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 hover-effect">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small">Ongoing Events</span>
                                    <h2 class="mt-2 mb-0 fw-bold">245</h2>
                                    <span class="badge bg-primary bg-opacity-10 text-primary mt-2">+12% from last
                                        month</span>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-calendar-event fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 hover-effect">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small">Pending Requests</span>
                                    <h2 class="mt-2 mb-0 fw-bold">245</h2>
                                    <span class="badge bg-primary bg-opacity-10 text-primary mt-2">+12% from last
                                        month</span>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-clock-history fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

 <!-- Calendar Section -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Events Calendar</h3>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="eventTypeDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-funnel me-1"></i> Filter Events
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="eventTypeDropdown">
                        <li>
                            <button class="dropdown-item" data-filter="facility">
                                <i class="bi bi-door-open me-2"></i> Facility Rentals
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" data-filter="equipment">
                                <i class="bi bi-tools me-2"></i> Equipment Rentals
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" data-filter="university">
                                <i class="bi bi-building me-2"></i> University Events
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" data-filter="external">
                                <i class="bi bi-globe me-2"></i> External Events
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button class="dropdown-item" data-filter="all">
                                <i class="bi bi-eye me-2"></i> Show All
                            </button>
                        </li>
                    </ul>
                </div>
                <a href="calendar.html" class="btn btn-primary">
                    <i class="bi bi-calendar-week me-1"></i> Open Calendar
                </a>
            </div>
        </div>

        <!-- Calendar container -->
        <div id="calendar" class="border rounded p-3 calendar-container" style="height: 600px;">
            <!-- Calendar will render here -->
        </div>
    </div>
</div> <!-- ✅ Calendar card closes here -->

<!-- System Log Section -->
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">System Log</h3>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="adminRoleDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person me-1"></i> Filter by Role
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="adminRoleDropdown">
                        <li><button class="dropdown-item" data-filter="head-admin">Head Admin</button></li>
                        <li><button class="dropdown-item" data-filter="assistant-admin">Assistant Admin</button></li>
                        <li><button class="dropdown-item" data-filter="all">Show All</button></li>
                    </ul>
                </div>
                <input type="date" class="form-control" id="logDateFilter" placeholder="Filter by Date">
            </div>
        </div>

        <!-- System log container -->
        <div id="systemLog" class="border rounded p-3 log-container">
            <ul class="list-group">
                <!-- Example log entries -->
                <li class="list-group-item">
                    <strong>John Doe</strong> (Head Admin) approved a requisition for the Main Auditorium on
                    <em>March 15, 2024</em>.
                </li>
                <li class="list-group-item">
                    <strong>Jane Smith</strong> (Assistant Admin) added new equipment: Sound System on
                    <em>March 10, 2024</em>.
                </li>
                <li class="list-group-item">
                    <strong>John Doe</strong> (Head Admin) deleted a facility: Old Gymnasium on
                    <em>March 5, 2024</em>.
                </li>
            </ul>
        </div>
    </div>
</div>

        </main>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Title:</strong> <span id="eventTitle"></span>
                        </li>
                        <li class="list-group-item">
                            <strong>Date:</strong> <span id="eventDate"></span>
                        </li>
                        <li class="list-group-item">
                            <strong>Time:</strong> <span id="eventTime">10:00 AM - 12:00 PM</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Description:</strong> <span id="eventDescription"></span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/calendar.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const adminId = localStorage.getItem('adminId');
            const manageProfileBtn = document.getElementById('manageProfileBtn');
            if (adminId) {
                manageProfileBtn.href = `/admin/profile/${adminId}`;
            }
        });
    </script>
@endsection