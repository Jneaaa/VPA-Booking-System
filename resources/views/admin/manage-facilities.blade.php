@extends('layouts.admin')

@section('title', 'Dashboard - Manage Facilities')

@section('content')
@php
    if (!isset($statuses)) {
        $statuses = \App\Models\Status::all();
    }
    if (!isset($departments)) {
        $departments = \App\Models\Department::all();
    }
    if (!isset($categories)) {
        $categories = \App\Models\Category::all();
    }
    if (!isset($facilities)) {
        $facilities = collect(); // Empty collection
    }
<style>
    /* Status indicator styles */
    .status-available {
        color: #198754;
        font-weight: bold;
    }
    
    .status-unavailable {
        color: #dc3545;
        font-weight: bold;
    }
    
    .status-reserved {
        color: #fd7e14;
        font-weight: bold;
    }
    
    .status-maintenance {
        color: #6c757d;
        font-weight: bold;
    }
    
    /* List view styles */
    .list-view .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .list-view .card {
        flex-direction: row;
    }
    
    .list-view .card-img-top {
        width: 300px;
        height: 200px;
        object-fit: cover;
    }
    
    /* Search container styles */
    .search-container {
        position: relative;
    }
    
    .search-container i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    
    .search-container input {
        padding-left: 40px;
    }
    
    /* Button styles */
    .btn-manage {
        background-color: #0d6efd;
        color: white;
        border: none;
    }
    
    .btn-manage:hover {
        background-color: #0b5ed7;
        color: white;
    }
    
    .btn-flex {
        flex: 1;
    }
    
    .facility-actions {
        display: flex;
        gap: 10px;
    }
    
    /* Card image styling */
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
    
    /* Loading spinner */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }
</style>

    <!-- Main Layout -->
    <div id="layout">
        <!-- Main Content -->
        <main id="main">
            <div class="container-fluid bg-light rounded p-4">
                <div class="container-fluid">
                    <!-- Header & Controls -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Manage Facilities</h2>
                        <div>
                            <a href="{{ url('/admin/add-facility') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle-fill me-2"></i>Add New Facility
                            </a>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filters & Search Bar -->
                    <form id="filterForm" method="GET" action="{{ url('/admin/manage-facilities') }}">
                        <div class="row mb-3 g-2">
                            <div class="col-sm-6 col-md-2 col-lg-2">
                                <select name="layout" id="layoutSelect" class="form-select">
                                    <option value="grid" {{ request('layout', 'grid') == 'grid' ? 'selected' : '' }}>Grid Layout</option>
                                    <option value="list" {{ request('layout') == 'list' ? 'selected' : '' }}>List Layout</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-2 col-lg-2">
                                <select name="status" id="statusFilter" class="form-select">
                                    <option value="all">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->status_id }}" {{ request('status') == $status->status_id ? 'selected' : '' }}>
                                            {{ $status->status_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-2 col-lg-2">
                                <select name="department" id="departmentFilter" class="form-select">
                                    <option value="all">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->department_id }}" {{ request('department') == $department->department_id ? 'selected' : '' }}>
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-2 col-lg-2">
                                <select name="category" id="categoryFilter" class="form-select">
                                    <option value="all">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                <div class="search-container">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="search" id="searchInput" class="form-control" 
                                           placeholder="Search Facilities..." value="{{ request('search') }}">
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading facilities...</p>
                    </div>

                    <!-- Facilities List -->
                    <div id="facilityContainer" class="row g-3">
                        @forelse($facilities as $facility)
                        <div class="col-md-4 facility-card" 
                             data-status="{{ $facility->status_id }}" 
                             data-department="{{ $facility->department_id }}" 
                             data-category="{{ $facility->category_id }}" 
                             data-title="{{ strtolower($facility->facility_name) }}"
                             data-description="{{ strtolower($facility->description) }}">
                            <div class="card h-100">
                                @if($facility->images->count() > 0)
                                    <img src="{{ $facility->images->first()->image_url }}" class="card-img-top" alt="{{ $facility->facility_name }}">
                                @else
                                    <img src="https://via.placeholder.com/300x200/4A90E2/FFFFFF?text=No+Image" class="card-img-top" alt="No Image">
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <div>
                                        <h5 class="card-title">{{ $facility->facility_name }}</h5>
                                        <p class="card-text text-muted mb-2">
                                            <i class="bi bi-tag-fill text-primary"></i> {{ $facility->category->category_name }} |
                                            @if($facility->subcategory)
                                                <i class="bi bi-diagram-2-fill text-primary"></i> {{ $facility->subcategory->subcategory_name }} |
                                            @endif
                                            <i class="bi bi-building-fill text-primary"></i> {{ $facility->department->department_name }}
                                        </p>
                                        <p class="status-{{ strtolower(str_replace(' ', '-', $facility->status->status_name)) }}">
                                            <i class="bi bi-circle-fill me-1"></i>{{ $facility->status->status_name }}
                                        </p>
                                        <p class="card-text mb-3">{{ Str::limit($facility->description, 150) }}</p>
                                    </div>
                                    <div class="facility-actions mt-auto pt-3">
                                    <a href="{{ route('admin.edit-facility', $facility->id) }}" class="btn btn-primary">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>No facilities found. 
                                <a href="{{ route('admin.add-facility') }}" class="alert-link">Add a new facility</a> to get started.
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination Controls -->
                    @if($facilities->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $facilities->appends(request()->except('page'))->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const facilityContainer = document.getElementById('facilityContainer');
            const searchInput = document.getElementById('searchInput');
            const layoutSelect = document.getElementById('layoutSelect');
            const statusFilter = document.getElementById('statusFilter');
            const departmentFilter = document.getElementById('departmentFilter');
            const categoryFilter = document.getElementById('categoryFilter');
            const filterForm = document.getElementById('filterForm');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Apply layout view based on current selection
            function applyLayoutView() {
                const layout = layoutSelect.value;
                if (layout === 'list') {
                    facilityContainer.classList.add('list-view');
                } else {
                    facilityContainer.classList.remove('list-view');
                }
            }

            // Apply initial layout
            applyLayoutView();

            // Submit form when filters change (server-side filtering)
            [layoutSelect, statusFilter, departmentFilter, categoryFilter].forEach(control => {
                control.addEventListener('change', function() {
                    loadingSpinner.style.display = 'block';
                    facilityContainer.style.opacity = '0.5';
                    filterForm.submit();
                });
            });

            // Client-side search filtering
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const facilityCards = document.querySelectorAll('.facility-card');
                let visibleCount = 0;

                facilityCards.forEach(card => {
                    const cardTitle = card.dataset.title;
                    const cardDescription = card.dataset.description;
                    const matchesSearch = cardTitle.includes(searchTerm) || 
                                         cardDescription.includes(searchTerm);

                    if (matchesSearch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide no results message
                toggleNoResultsMessage(visibleCount);
            });

            // Toggle no results message
            function toggleNoResultsMessage(visibleCount) {
                let noResultsMessage = document.getElementById('noResultsMessage');
                
                if (visibleCount === 0) {
                    if (!noResultsMessage) {
                        noResultsMessage = document.createElement('div');
                        noResultsMessage.id = 'noResultsMessage';
                        noResultsMessage.className = 'col-12';
                        noResultsMessage.innerHTML = `
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-triangle me-2"></i>No facilities match your search criteria.
                            </div>
                        `;
                        facilityContainer.appendChild(noResultsMessage);
                    }
                } else if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            }

            // Handle delete confirmation with better UX
            document.querySelectorAll('form[method="POST"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this facility? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });

            // Handle pagination links to preserve filters
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadingSpinner.style.display = 'block';
                    facilityContainer.style.opacity = '0.5';
                    window.location.href = this.href;
                });
            });
        });
    </script>
@endsection