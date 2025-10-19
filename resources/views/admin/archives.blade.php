@extends('layouts.admin')

@section('title', 'Archives')

@section('content')

    <style>
        #archiveTable {
            font-size: 0.875rem;
        }

        #archiveTable th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        #archiveTable td {
            vertical-align: middle;
        }

        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
            border-radius: 0.25rem;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        .bar-chart-item .progress {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .bar-chart-item .progress-bar {
            position: relative;
            border-radius: 8px;
            transition: width 0.8s ease-in-out;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-text {
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            font-size: 0.85rem;
            white-space: nowrap;
        }


        /* Responsive adjustments */
        @media (max-width: 768px) {
            .bar-chart-item .progress {
                height: 20px !important;
            }

            .progress-text {
                font-size: 0.75rem;
            }

            .col-md-2.border-end {
                border-right: none !important;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
                border-bottom: 1px solid #dee2e6;
            }
        }
    </style>
    <main>
        <!-- Page Header and Statistics in Two Columns -->
        <div class="row mb-4">
            <!-- Left Column: Page Header Card -->
            <div class="col-md-6">
                <div class="card bg-transparent border-0 h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <h1 class="h3 mb-2 text-gray-800 fw-bold text-primary">Archives</h1>
                        <p class="mb-3">View completed, cancelled, and rejected requisitions</p>
                        <button class="btn btn-primary" id="exportArchiveBtn">
                            <i class="fas fa-file-export me-2"></i>Export Archive
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Statistics Card -->
            <div class="col-md-6">
                <div class="card border-0 h-100">
                    <div class="card-body">
                        <div class="row align-items-center h-100">
                            <!-- Year Navigation and Header -->
                            <div class="col-12 text-center mb-3">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <button class="btn btn-sm btn-secondary" id="prevYearBtn">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <h5 class="m-0 fw-bold text-primary" id="currentYear">2024</h5>
                                    <button class="btn btn-sm btn-secondary" id="nextYearBtn">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <p class="m-0 text-muted small">Completed Transactions</p>
                            </div>

                            <!-- Bar Chart Container -->
                            <div class="col-12">
                                <div class="bar-chart-container" id="monthlyChartContainer">
                                    <!-- Monthly bar chart will be rendered here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archive Table -->
        <div class="card bg-transparent border-0 mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-0">
                <h5 class="m-0 font-weight-bold text-primary fw-bold">Completed Transactions</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-secondary" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="archiveTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>Request ID</th>
                                <th>Purpose</th>
                                <th>Date Completed</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="archiveTableBody">
                            <!-- Data will be loaded via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading archived requisitions...</p>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5" style="display: none;">
                    <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Archived Requisitions</h5>
                    <p class="text-muted">There are no completed, cancelled, or rejected requisitions to display.</p>
                </div>
            </div>
        </div>
        </div>

        <!-- View Details Modal -->
        <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewDetailsModalLabel">Requisition Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="requisitionDetails">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/toast.js') }}"></script>
    <script>
        class ArchiveManager {
            constructor() {
                this.archivedRequisitions = [];
                this.currentYear = new Date().getFullYear();
                this.init();
            }

            init() {
                this.loadArchivedRequisitions();
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Refresh button
                document.getElementById('refreshBtn').addEventListener('click', () => {
                    this.loadArchivedRequisitions();
                });

                // Export button
                document.getElementById('exportArchiveBtn').addEventListener('click', () => {
                    this.exportArchive();
                });

                // Year navigation
                document.getElementById('prevYearBtn').addEventListener('click', () => {
                    this.currentYear = parseInt(this.currentYear) - 1;
                    this.updateMonthlyChart(this.currentYear);
                });

                document.getElementById('nextYearBtn').addEventListener('click', () => {
                    this.currentYear = parseInt(this.currentYear) + 1;
                    this.updateMonthlyChart(this.currentYear);
                });

                // Handle modal show event
                const viewDetailsModal = document.getElementById('viewDetailsModal');
                viewDetailsModal.addEventListener('show.bs.modal', (event) => {
                    const button = event.relatedTarget;
                    if (button) {
                        const requestId = button.getAttribute('data-request-id');
                        this.showRequisitionDetails(requestId);
                    }
                });

                // Also handle direct row clicks
                document.addEventListener('click', (event) => {
                    const row = event.target.closest('tr');
                    if (row && row.hasAttribute('onclick')) {
                        // Extract requestId from the onclick attribute
                        const onclickAttr = row.getAttribute('onclick');
                        const match = onclickAttr.match(/archiveManager\.showRequisitionDetails\((\d+)\)/);
                        if (match) {
                            const requestId = match[1];
                            this.showRequisitionDetails(requestId);
                            // Manually trigger the modal
                            const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
                            modal.show();
                        }
                    }
                });
            }

            exportArchive() {
                if (this.archivedRequisitions.length === 0) {
                    this.showError('No data available to export.');
                    return;
                }

                try {
                    // Create CSV content with all details for export
                    const headers = [
                        'Request ID',
                        'Official Receipt #',
                        'Requester Name',
                        'Email',
                        'Organization',
                        'Purpose',
                        'Status',
                        'Start Schedule',
                        'End Schedule',
                        'Participants',
                        'Facilities',
                        'Equipment',
                        'Date Completed'
                    ];

                    const csvData = this.archivedRequisitions.map(requisition => [
                        requisition.request_id,
                        `"${requisition.official_receipt_num || ''}"`,
                        `"${requisition.requester_name}"`,
                        `"${requisition.email}"`,
                        `"${requisition.organization_name || ''}"`,
                        `"${requisition.purpose}"`,
                        `"${requisition.status}"`,
                        `"${requisition.start_schedule}"`,
                        `"${requisition.end_schedule}"`,
                        requisition.num_participants,
                        `"${requisition.facilities.join(', ')}"`,
                        `"${requisition.equipment.join(', ')}"`,
                        new Date(requisition.updated_at).toLocaleDateString()
                    ]);

                    const csvContent = [headers, ...csvData]
                        .map(row => row.join(','))
                        .join('\n');

                    // Create and download file
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);

                    link.setAttribute('href', url);
                    link.setAttribute('download', `archives_${new Date().toISOString().split('T')[0]}.csv`);
                    link.style.visibility = 'hidden';

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    this.showSuccess('Archive exported successfully!');

                } catch (error) {
                    console.error('Export error:', error);
                    this.showError('Failed to export archive: ' + error.message);
                }
            }

            showSuccess(message) {
                showToast(message, 'success');
            }

            async loadArchivedRequisitions() {
                const loadingState = document.getElementById('loadingState');
                const emptyState = document.getElementById('emptyState');
                const tableBody = document.getElementById('archiveTableBody');

                loadingState.style.display = 'block';
                emptyState.style.display = 'none';
                tableBody.innerHTML = '';

                try {
                    const token = localStorage.getItem('adminToken');
                    const response = await fetch('/api/admin/archives', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch archived requisitions');

                    const result = await response.json();

                    if (result.success) {
                        this.archivedRequisitions = result.data;
                        this.renderArchiveTable();
                        this.updateStatistics();
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error loading archived requisitions:', error);
                    this.showError('Failed to load archived requisitions: ' + error.message);
                } finally {
                    loadingState.style.display = 'none';
                }
            }
            renderArchiveTable() {
                const tableBody = document.getElementById('archiveTableBody');
                const emptyState = document.getElementById('emptyState');

                if (this.archivedRequisitions.length === 0) {
                    tableBody.innerHTML = '';
                    emptyState.style.display = 'block';
                    return;
                }

                emptyState.style.display = 'none';

                tableBody.innerHTML = this.archivedRequisitions.map(requisition => `
                    <tr onclick="archiveManager.showRequisitionDetails(${requisition.request_id})" style="cursor: pointer;">
                        <td class="fw-bold">#${requisition.request_id.toString().padStart(4, '0')}</td>
                        <td>${requisition.purpose}</td>
                        <td class="small">
                            ${new Date(requisition.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}<br>
                            <small class="text-muted">${new Date(requisition.updated_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</small>
                        </td>
                        <td>
                            <span class="status-badge" style="background-color: ${requisition.status_color}; color: white;">
                                ${requisition.status}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewDetailsModal"
                                    data-request-id="${requisition.request_id}"
                                    onclick="event.stopPropagation()">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
            updateStatistics() {
                const totalArchived = this.archivedRequisitions.length;

                // Update total count only (the only element that still exists)
                const totalArchivedElement = document.getElementById('totalArchived');
                if (totalArchivedElement) {
                    totalArchivedElement.textContent = totalArchived;
                }

                // Update the monthly chart with current year data
                this.updateMonthlyChart(this.currentYear);
            }

            updateMonthlyChart(year) {
                const container = document.getElementById('monthlyChartContainer');
                const currentYearElement = document.getElementById('currentYear');

                // Update current year display
                currentYearElement.textContent = year;
                this.currentYear = year;

                // Filter requisitions for the selected year and include multiple statuses
                const yearRequisitions = this.archivedRequisitions.filter(r => {
                    const requisitionYear = new Date(r.updated_at).getFullYear();
                    return requisitionYear === year &&
                        ['Completed', 'Late', 'Cancelled'].includes(r.status);
                });

                // Group by month and status
                const monthlyData = {};
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                // Initialize data structure
                for (let i = 0; i < 12; i++) {
                    monthlyData[i] = {
                        'Completed': 0,
                        'Late': 0,
                        'Cancelled': 0,
                        total: 0
                    };
                }

                // Populate data
                yearRequisitions.forEach(requisition => {
                    const month = new Date(requisition.updated_at).getMonth();
                    const status = requisition.status;
                    if (monthlyData[month][status] !== undefined) {
                        monthlyData[month][status]++;
                        monthlyData[month].total++;
                    }
                });

                // Get max count for scaling
                const maxCount = Math.max(...Object.values(monthlyData).map(m => m.total), 1);

                // Generate the vertical bar chart HTML
                let chartHTML = `
            <div class="vertical-bar-chart">
                <div class="d-flex align-items-end justify-content-between h-100 gap-1" style="height: 180px;">
        `;

                // Generate vertical bars for each month with stacked statuses
                for (let month = 0; month < 12; month++) {
                    const monthData = monthlyData[month];
                    const total = monthData.total;

                    // Calculate the total bar height based on maxCount
                    const barHeightPercentage = maxCount > 0 ? (total / maxCount) * 100 : 0;

                    chartHTML += `
                <div class="d-flex flex-column align-items-center" style="flex: 1; height: 100%; max-width: 40px;">
                    <!-- Count label at top -->
                    <div class="text-center mb-1" style="min-height: 20px;">
                        <small class="text-dark">${total}</small>
                    </div>

                    <!-- Bar Container -->
                    <div class="position-relative w-100" style="height: 100%; min-height: 120px;">
                        <!-- Background track -->
                        <div class="bg-transparent rounded" style="height: 100%; width: 100%; position: absolute;"></div>

                        <!-- Stacked status bars -->
        <div class="position-absolute bottom-0 d-flex flex-column-reverse justify-content-center" 
             style="height: ${barHeightPercentage}%; width: 100%;">
            ${monthData.Completed > 0 ? `
            <div class="bg-success rounded-top" style="height: ${(monthData.Completed / total) * 100}%; width: 60%; margin: 0 auto;" 
                 title="Completed: ${monthData.Completed}"></div>
            ` : ''}
            ${monthData.Late > 0 ? `
            <div class="bg-warning" style="height: ${(monthData.Late / total) * 100}%; width: 60%; margin: 0 auto;" 
                 title="Late: ${monthData.Late}"></div>
            ` : ''}
            ${monthData.Cancelled > 0 ? `
            <div class="bg-danger rounded-bottom" style="height: ${(monthData.Cancelled / total) * 100}%; width: 60%; margin: 0 auto;" 
                 title="Cancelled: ${monthData.Cancelled}"></div>
            ` : ''}
        </div>
                    </div>

                    <!-- Month label at bottom -->
                    <div class="text-center mt-1">
                        <small class="text-muted fw-semibold">${monthNames[month]}</small>
                    </div>
                </div>
            `;
                }

                chartHTML += `
                </div>

                <!-- Legend -->
                <div class="row justify-content-center mt-3">
                    <div class="col-auto">
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-success me-2" style="width: 15px; height: 15px; border-radius: 100px;"></div>
                                <small class="text-muted">Completed</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning me-2" style="width: 15px; height: 15px; border-radius: 100px;"></div>
                                <small class="text-muted">Late</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-danger me-2" style="width: 15px; height: 15px; border-radius: 100px;"></div>
                                <small class="text-muted">Cancelled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

                container.innerHTML = chartHTML;
            }

            animateProgressBar(barId, percent, percentId) {
                const bar = document.getElementById(barId);
                const percentElement = document.getElementById(percentId);

                // Reset to 0 first for smooth animation
                bar.style.width = '0%';
                percentElement.textContent = '0%';

                // Animate to target percentage
                setTimeout(() => {
                    bar.style.width = percent + '%';
                    bar.setAttribute('aria-valuenow', percent);
                    percentElement.textContent = percent + '%';
                }, 100);
            }

            showRequisitionDetails(requestId) {
                console.log('showRequisitionDetails called with requestId:', requestId);

                // Convert to number for comparison
                const id = parseInt(requestId);
                const requisition = this.archivedRequisitions.find(r => r.request_id === id);
                console.log('Found requisition:', requisition);

                if (!requisition) {
                    console.error('No requisition found for requestId:', requestId);
                    return;
                }

                const modalBody = document.getElementById('requisitionDetails');
                console.log('Modal body element:', modalBody);

                // Set the modal content
                modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Requester Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Request ID:</strong></td>
                                <td>#${requisition.request_id.toString().padStart(4, '0')}</td>
                            </tr>
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>${requisition.requester_name}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>${requisition.email}</td>
                            </tr>
                            ${requisition.organization_name ? `
                            <tr>
                                <td><strong>Organization:</strong></td>
                                <td>${requisition.organization_name}</td>
                            </tr>` : ''}
                            <tr>
                                <td><strong>Participants:</strong></td>
                                <td>${requisition.num_participants}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Booking Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Purpose:</strong></td>
                                <td>${requisition.purpose}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="status-badge" style="background-color: ${requisition.status_color}; color: white;">
                                        ${requisition.status}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Start Schedule:</strong></td>
                                <td>${requisition.start_schedule}</td>
                            </tr>
                            <tr>
                                <td><strong>End Schedule:</strong></td>
                                <td>${requisition.end_schedule}</td>
                            </tr>
                            <tr>
                                <td><strong>Official Receipt:</strong></td>
                                <td>
                                    ${requisition.official_receipt_num ?
                        `<div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-info">${requisition.official_receipt_num}</span>
                                            <a href="/official-receipt/${requisition.request_id}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               target="_blank">
                                                <i class="fas fa-receipt me-1"></i>Open Receipt
                                            </a>
                                        </div>` :
                        '<span class="text-muted">N/A</span>'
                    }
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Facilities</h6>
                        ${requisition.facilities.length > 0 ?
                        requisition.facilities.map(facility =>
                            `<span class="badge bg-secondary me-1 mb-1">${facility}</span>`
                        ).join('') :
                        '<p class="text-muted">No facilities requested</p>'
                    }
                    </div>
                    <div class="col-md-6">
                        <h6>Equipment</h6>
                        ${requisition.equipment.length > 0 ?
                        requisition.equipment.map(eq =>
                            `<span class="badge bg-info me-1 mb-1">${eq}</span>`
                        ).join('') :
                        '<p class="text-muted">No equipment requested</p>'
                    }
                    </div>
                </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Timeline</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Submitted:</strong></td>
                        <td>${new Date(requisition.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })} at ${new Date(requisition.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>${new Date(requisition.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })} at ${new Date(requisition.updated_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</td>
                    </tr>
                </table>
            </div>
        </div>
            `;

                console.log('Modal content set successfully');
            }

            showError(message) {
                // You can implement a toast notification system here
                console.error(message);
                showToast(message, 'error');
            }
        }

        // Initialize archive manager when the page loads
        const archiveManager = new ArchiveManager();
    </script>
@endsection