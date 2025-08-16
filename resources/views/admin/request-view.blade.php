@extends('layouts.admin')

@section('title', 'Admin Dashboard • Manage Requisitions • View Request')

@section('content')
<style>
    body {
        background-color: #f8f9fa !important;
    }
</style>
<div class="container-fluid" style="max-width: calc(100vw - 280px); margin-right: 10px; margin-top: 50px;">
    <div class="card" style="border-radius: 8px; border: none; box-shadow: none; background: #f8f9fa;">
        <div class="card-body">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/manage-requests') }}">Requisitions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Request</li>
                </ol>
            </nav>

            <!-- Skeleton Loading -->
            <div id="loadingState">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="skeleton skeleton-text" style="width: 200px; height: 30px;"></div>
                    <div class="skeleton skeleton-text" style="width: 100px; height: 30px;"></div>
                </div>

                <div class="row g-3">
                    <!-- User Information Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <hr style="border: 1px solid #eee;">
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 95%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <hr style="border: 1px solid #eee;">
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 95%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Skeleton -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <div class="skeleton" style="height: 450px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Fees Skeleton -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="skeleton skeleton-text mb-3" style="width: 150px;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 100%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 90%;"></div>
                                <div class="skeleton skeleton-text mb-2" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actual Content (Initially Hidden) -->
            <div id="contentState" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Request ID #<span id="requestIdTitle"></span></h4>
                    <span class="badge" id="statusBadge"></span>
                </div>

                <div class="row g-3">
                    <!-- User Information -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Requester Details</h5>
                                <hr style="border: 1px solid black;">
                                <div id="userDetails"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Reservation Details</h5>
                                <hr style="border: 1px solid black;">
                                <div id="formDetails"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Visualization -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Schedule Visualization</h5>
                                <div id="calendar" style="overflow: hidden;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Requested Items -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Requested Items</h5>
                                <div id="requestedItems"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Fees -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Additional Fees</h5>
                                <div id="additionalFees"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Status -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Form Status</h5>
                                <div id="formStatus"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button class="btn btn-success" id="approveBtn">Approve Request</button>
                            <button class="btn btn-danger" id="rejectBtn">Reject Request</button>
                            <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Requester:</strong> <span id="modalRequester"></span></p>
                        <p><strong>Purpose:</strong> <span id="modalPurpose"></span></p>
                        <p><strong>Participants:</strong> <span id="modalParticipants"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                        <p><strong>Tentative Fee:</strong> <span id="modalFee"></span></p>
                        <p><strong>Approvals:</strong> <span id="modalApprovals"></span></p>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Requested Items:</h6>
                    <div id="modalItems"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="modalViewDetails">View Full Details</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const requestId = window.location.pathname.split('/').pop();
            const adminToken = localStorage.getItem('adminToken');
            let allRequests = [];

            // Initialize Bootstrap modal
            const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

            // Initialize compact calendar
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 500,
                handleWindowResize: true,
                windowResizeDelay: 200,
                aspectRatio: 1.5,
                expandRows: true,
                events: [],
                eventClick: function (info) {
                    const request = allRequests.find(req => req.request_id == info.event.extendedProps.requestId);
                    if (request) {
                        showEventModal(request);
                    }
                },
                eventDidMount: function (info) {
                    // Add custom styling based on status
                    const status = info.event.extendedProps.status;
                    if (status === 'Pending Approval') {
                        info.el.style.backgroundColor = '#ffc107';
                        info.el.style.borderColor = '#ffc107';
                    } else if (status === 'Approved' || status === 'Scheduled') {
                        info.el.style.backgroundColor = '#28a745';
                        info.el.style.borderColor = '#28a745';
                    } else if (status === 'Rejected') {
                        info.el.style.backgroundColor = '#dc3545';
                        info.el.style.borderColor = '#dc3545';
                    } else if (status === 'Awaiting Payment') {
                        info.el.style.backgroundColor = '#0d6efd';
                        info.el.style.borderColor = '#0d6efd';
                    }
                },
                datesSet: function(info) {
                    // Force refresh of calendar rendering
                    calendar.updateSize();
                },
                viewDidMount: function(info) {
                    // Ensure proper initial rendering
                    setTimeout(() => calendar.updateSize(), 0);
                },
                eventTimeFormat: { // like '14:30:00'
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                },
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                nowIndicator: true,
                navLinks: true,
                dayHeaderFormat: { weekday: 'short', month: 'short', day: 'numeric' },
                eventDisplay: 'block'
            });

            // Ensure calendar is properly rendered after DOM content is loaded
            setTimeout(() => {
                calendar.render();
                calendar.updateSize();
            }, 0);

            // Function to show event details in modal
            function showEventModal(request) {
                document.getElementById('eventModalTitle').textContent = request.form_details.calendar_info.title;
                document.getElementById('modalRequester').textContent = `${request.user_details.first_name} ${request.user_details.last_name}`;
                document.getElementById('modalPurpose').textContent = request.form_details.purpose;
                document.getElementById('modalParticipants').textContent = request.form_details.num_participants;
                document.getElementById('modalStatus').textContent = request.form_details.status.name;
                document.getElementById('modalFee').textContent = `₱${request.fees.tentative_fee}`;
                document.getElementById('modalApprovals').textContent = `${request.approvals.count}/3`;

                // Format requested items
                let itemsHtml = '';
                if (request.requested_items.facilities.length > 0) {
                    itemsHtml += '<h6>Facilities:</h6>';
                    itemsHtml += request.requested_items.facilities.map(f =>
                        `<p>• ${f.name} - ₱${f.fee} ${f.is_waived ? '(Waived)' : ''}</p>`
                    ).join('');
                }

                if (request.requested_items.equipment.length > 0) {
                    itemsHtml += '<h6 class="mt-2">Equipment:</h6>';
                    itemsHtml += request.requested_items.equipment.map(e =>
                        `<p>• ${e.name} - ₱${e.fee} ${e.is_waived ? '(Waived)' : ''}</p>`
                    ).join('');
                }

                document.getElementById('modalItems').innerHTML = itemsHtml || '<p>No items requested</p>';

                // Set up view details button
                document.getElementById('modalViewDetails').onclick = function () {
                    window.location.href = `/admin/requisition/${request.request_id}`;
                };

                eventModal.show();
            }

            async function fetchRequestDetails() {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/admin/requisition-forms`, {
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch request details');

                    allRequests = await response.json();
                    const request = allRequests.find(req => req.request_id == requestId);
                    if (!request) throw new Error('Request not found');

                    // Update request ID in title
                    document.getElementById('requestIdTitle').textContent = requestId;

                    // Update calendar with all events
                    calendar.removeAllEvents();
                    allRequests.forEach(req => {
                        calendar.addEvent({
                            title: req.form_details.calendar_info.title,
                            start: `${req.schedule.start_date}T${req.schedule.start_time}`,
                            end: `${req.schedule.end_date}T${req.schedule.end_time}`,
                            extendedProps: {
                                status: req.form_details.status.name,
                                requestId: req.request_id
                            },
                            description: req.form_details.calendar_info.description
                        });
                    });

                    // Highlight current request
                    calendar.getEvents().forEach(event => {
                        if (event.extendedProps.requestId == requestId) {
                            event.setProp('backgroundColor', '#003366');
                            event.setProp('borderColor', '#003366');
                        }
                    });

                    // Update status badge
                    const statusBadge = document.getElementById('statusBadge');
                    statusBadge.textContent = request.form_details.status.name;
                    statusBadge.className = `badge bg-${getStatusColor(request.form_details.status.name)}`;

                    // Update user details
                    document.getElementById('userDetails').innerHTML = `
                            <p><strong>Name:</strong> ${request.user_details.first_name} ${request.user_details.last_name}</p>
                            <p><strong>Email:</strong> ${request.user_details.email}</p>
                            <p><strong>User Type:</strong> ${request.user_details.user_type}</p>
                            <p><strong>School ID:</strong> ${request.user_details.school_id || 'N/A'}</p>
                            <p><strong>Organization:</strong> ${request.user_details.organization_name || 'N/A'}</p>
                            <p><strong>Contact:</strong> ${request.user_details.contact_number || 'N/A'}</p>
                        `;

                    // Update form details
                    document.getElementById('formDetails').innerHTML = `
                            <p><strong>Purpose:</strong> ${request.form_details.purpose}</p>
                            <p><strong>Participants:</strong> ${request.form_details.num_participants}</p>
                            <p><strong>Schedule:</strong> ${formatDateTime(request.schedule)}</p>
                            <p><strong>Additional Requests:</strong> ${request.form_details.additional_requests || 'None'}</p>
                              <p><strong>Formal Letter:</strong> ${request.documents.formal_letter.url ?
                            `<a href="${request.documents.formal_letter.url}" target="_blank" class="btn btn-sm btn-primary">View Document</a>` :
                            'Not uploaded'}</p>
                            <p><strong>Facility Setup:</strong> ${request.documents.facility_layout.url ?
                            `<a href="${request.documents.formal_letter.url}" target="_blank" class="btn btn-sm btn-primary">View Document</a>` :
                            'Not uploaded'}</p>
                        `;

                    // Update requested items with fee breakdown
                    document.getElementById('requestedItems').innerHTML = `
                        <div class="mb-3">
                            <h6>Facilities:</h6>
                            ${request.requested_items.facilities.length > 0 ?
                            request.requested_items.facilities.map(f =>
                                `<div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>• ${f.name} ${f.is_waived ? '(Waived)' : ''}</span>
                                    <span class="ms-3">₱${f.fee}</span>
                                </div>`
                            ).join('') : '<p>No facilities requested</p>'}

                            <h6 class="mt-3">Equipment:</h6>
                            ${request.requested_items.equipment.length > 0 ?
                            request.requested_items.equipment.map(e =>
                                `<div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>• ${e.name} ${e.is_waived ? '(Waived)' : ''}</span>
                                    <span class="ms-3">₱${e.fee}</span>
                                </div>`
                            ).join('') : '<p>No equipment requested</p>'}
                        </div>
                        <hr style="border: 1px solid #dee2e6;">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <strong>Tentative Fee:</strong>
                            <span>₱${request.fees.tentative_fee}</span>
                        </div>
                    `;

                    // Update form status
                    document.getElementById('formStatus').innerHTML = `
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Approved Fee:</strong> ${request.fees.approved_fee ? `₱${request.fees.approved_fee}` : 'Pending'}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Late Penalty:</strong> ${request.fees.late_penalty_fee ? `₱${request.fees.late_penalty_fee}` : 'N/A'}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Approvals:</strong> ${request.approvals.count}/3</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Status:</strong> ${request.form_details.status.name}</p>
                            </div>
                        </div>
                    `;

                    // Clear additional fees section (leaving empty for now)
                    document.getElementById('additionalFees').innerHTML = '';

                    // Remove the old documents section update since it's been replaced
                    // Remove the old fees and status update since it's been replaced

                    // Set up approve/reject buttons
                    document.getElementById('approveBtn').addEventListener('click', () => approveRequest(requestId));
                    document.getElementById('rejectBtn').addEventListener('click', () => rejectRequest(requestId));

                    // After successful data fetch and updates, show content and hide loading state
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('contentState').style.display = 'block';

                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to load request details');
                    // Show error state
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('contentState').innerHTML = `
                        <div class="alert alert-danger">
                            Failed to load request details. Please try refreshing the page.
                        </div>
                    `;
                    document.getElementById('contentState').style.display = 'block';
                }
            }

            function formatDateTime(schedule) {
                const startDate = new Date(schedule.start_date + 'T' + schedule.start_time);
                const endDate = new Date(schedule.end_date + 'T' + schedule.end_time);
                return `${startDate.toLocaleString()} to ${endDate.toLocaleString()}`;
            }

            function getStatusColor(status) {
                const colors = {
                    'Pending Approval': 'warning',
                    'In Review': 'info',
                    'Awaiting Payment': 'primary',
                    'Scheduled': 'success',
                    'Rejected': 'danger',
                    'Cancelled': 'secondary'
                };
                return colors[status] || 'secondary';
            }

            async function approveRequest(requestId) {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/admin/approve-request`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ request_id: requestId })
                    });

                    if (!response.ok) throw new Error('Failed to approve request');

                    const result = await response.json();
                    alert(result.message || 'Request approved successfully');
                    fetchRequestDetails(); // Refresh the data
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to approve request');
                }
            }

            async function rejectRequest(requestId) {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/admin/reject-request`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${adminToken}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ request_id: requestId })
                    });

                    if (!response.ok) throw new Error('Failed to reject request');

                    const result = await response.json();
                    alert(result.message || 'Request rejected successfully');
                    fetchRequestDetails(); // Refresh the data
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to reject request');
                }
            }

            fetchRequestDetails();
        });
    </script>
@endsection