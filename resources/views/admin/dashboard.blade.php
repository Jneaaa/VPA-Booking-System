@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Main Layout -->
    <div id="layout">
        <!-- Main Content -->
        <main id="main">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Your Dashboard</h2>
                <a href="#" class="btn btn-primary">
                    <i class="bi bi-gear me-1"></i> Manage Requests
                </a>
            </div>
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-muted">Total Requisitions</h5>
                                    <p class="card-text fs-3 fw-bold">245</p>
                                </div>
                                <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-muted">Approved Bookings</h5>
                                    <p class="card-text fs-3 fw-bold">112</p>
                                </div>
                                <i class="bi bi-check-circle fs-1 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-muted">Pending Requests</h5>
                                    <p class="card-text fs-3 fw-bold">16</p>
                                </div>
                                <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Calendar Section -->
            <section>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold">Events Calendar</h3>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="eventTypeDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-funnel me-1"></i> Filter Events
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="eventTypeDropdown">
                                <li><button class="dropdown-item" data-filter="facility"><i class="bi bi-door-open me-2"></i>Facility
                                    Rentals</button></li>
                                <li><button class="dropdown-item" data-filter="equipment"><i class="bi bi-tools me-2"></i>Equipment
                                    Rentals</button></li>
                                <li><button class="dropdown-item" data-filter="university"><i class="bi bi-building me-2"></i>University
                                    Events</button></li>
                                <li><button class="dropdown-item" data-filter="external"><i class="bi bi-globe me-2"></i>External
                                    Events</button></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><button class="dropdown-item" data-filter="all"><i class="bi bi-eye me-2"></i>Show All</button></li>
                            </ul>
                        </div>
                        <a href="calendar.html" class="btn btn-primary">
                            <i class="bi bi-calendar-week me-1"></i> Open Calendar
                        </a>
                    </div>
                </div>
                <div id="calendar" class="border rounded p-3 calendar-container" style="height: 600px;">
                    <!-- Set height to 600px -->
                </div>
            </section>
            <!-- System Log Section -->
            <section class="mt-5">
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
                <div id="systemLog" class="border rounded p-3 log-container">
                    <ul class="list-group">
                        <!-- Example log entries -->
                        <li class="list-group-item">
                            <strong>John Doe</strong> (Head Admin) approved a requisition for the Main Auditorium on <em>March 15,
                            2024</em>.
                        </li>
                        <li class="list-group-item">
                            <strong>Jane Smith</strong> (Assistant Admin) added new equipment: Sound System on <em>March 10,
                            2024</em>.
                        </li>
                        <li class="list-group-item">
                            <strong>John Doe</strong> (Head Admin) deleted a facility: Old Gymnasium on <em>March 5, 2024</em>.
                        </li>
                    </ul>
                </div>
            </section>
        </main>
    </div>
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
@endsection