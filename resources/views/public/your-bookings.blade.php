@extends('layouts.app')

@section('title', 'Booking Catalog - Facilities & Equipment')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem 0;
            background-image: url('{{ asset('assets/homepage.jpg') }}');
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
            position: relative;
        }

        .main-content::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .content-wrapper {
            position: relative;
            z-index: 2;
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .1);
            margin: 2rem auto;
            max-width: 700px;
            width: 90%;
        }

        .content-wrapper h2 {
            color: #333;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
        }


        .lookup-form .input-group {
            margin-bottom: 1.5rem;
        }

        .lookup-form .form-control {
            border-radius: 0.25rem 0 0 0.25rem;
            height: calc(2.25rem + 2px);
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .lookup-form .btn-primary {
            background-color: #041A4B;
            border-color: #041A4B;
            color: white;
            font-weight: bold;
            border-radius: 0 0.25rem 0.25rem
        }

        .lookup-form .btn-primary:hover {
            background-color: #002c6b;
            border-color: #002c6b;
        }

        .no-requisition-message {
            color: #777;
            text-align: center;
            font-style: italic;
            margin-top: 1.5rem;
        }


        .requisition-list {
            margin-top: 2rem;
        }

        .requisition-card {
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            background-color: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
        }

        .requisition-card .status-badge {
            padding: 0.4em 0.8em;
            border-radius: 0.25rem;
            font-weight: bold;
            font-size: 0.85rem;
            min-width: 80px;
            text-align: center;
        }

        /* Custom Status Badges */
        .status-badge.completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        /* Light green, dark green text */
        .status-badge.pending {
            background-color: #fff3cd;
            color: #664d03;
        }

        /* Light yellow, dark yellow text */
        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #842029;
        }

        /* Light red, dark red text */
        .status-badge.on-going {
            background-color: #cfe2ff;
            color: #052c65;
        }

        /* Light blue, dark blue text */
        .status-badge.rejected {
            background-color: #f8d7da;
            color: #842029;
        }

        /* Light red, dark red text */


        .requisition-details {
            flex-grow: 1;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;

            font-size: 0.95rem;
            color: #555;
            padding-right: 1rem;

        }

        .requisition-details div {
            margin-bottom: 0.3rem;
        }

        .requisition-details strong {
            color: #333;
            min-width: 80px;
            /* Align labels */
            display: inline-block;
        }

        .requisition-total {
            font-weight: bold;
            font-size: 1.1rem;
            color: #041A4B;
            text-align: right;
            /* Align total to the right */
            width: 100%;
            /* Take full width on smaller screens */
            margin-top: 0.5rem;
        }

        .requisition-actions {
            display: flex;
            flex-direction: column;
            /* Stack buttons on smaller screens */
            align-items: flex-end;
            /* Align buttons to the right */
            margin-top: 1rem;
            width: 100%;
            /* Take full width on smaller screens */
        }

        .requisition-actions .btn {
            margin-left: 0.5rem;
            /* Space between buttons if they are side-by-side */
            margin-bottom: 0.5rem;
            /* Space between stacked buttons */
            white-space: nowrap;
            /* Prevent button text from wrapping */
        }

        .requisition-actions .btn:last-child {
            margin-bottom: 0;
            /* No margin for the last button */
        }

        /* Filter Dropdown */
        .filter-dropdown {
            margin-bottom: 1.5rem;
            text-align: right;
        }

        /* Footer */
        footer {
            background-color: #041A4B;
            /* Dark blue from the image */
            color: white;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: auto;
            /* Pushes footer to the bottom */
        }

        footer p {
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        /* Responsive adjustments */
        @media (min-width: 768px) {
            .requisition-card {
                justify-content: space-between;
                align-items: center;
                /* Center align items for larger screens */
            }

            .requisition-details {
                flex: 1;
                padding-right: 1rem;
            }

            .requisition-total {
                width: auto;
                /* Allow total to shrink on larger screens */
                text-align: right;
                margin-top: 0;
            }

            .requisition-actions {
                flex-direction: row;
                /* Buttons side-by-side on larger screens */
                width: auto;
                /* Auto width */
                margin-top: 0;
            }
        }

        @media (min-width: 992px) {
            .content-wrapper {
                padding: 2rem;
            }
        }
    </style>

    <main class="main-content d-flex align-items-center justify-content-center">
        <div class="content-wrapper">
            <h2>Requisition Form Lookup</h2>

            <div id="lookupSection" class="lookup-form">
                <div class="input-group">
                    <input type="text" class="form-control" id="referenceInput"
                        placeholder="Enter your reference code or email address" aria-label="Reference code or email">
                    <button class="btn btn-primary" type="button" onclick="showResults()">Search Requisitions</button>
                </div>
                <p id="noResultsMessage" class="no-requisition-message">No requisition forms found. Please check your
                    reference code or email and try again.</p>
            </div>

            <div id="resultsSection" style="display: none;">
                <div class="lookup-form">
                    <div class="input-group">
                        <input type="text" class="form-control" value="juan_costareal@email.com"
                            aria-label="Reference code or email" readonly>
                        <button class="btn btn-primary" type="button" onclick="showLookup()">New Search</button>
                    </div>
                </div>

                <h4 class="mt-4 mb-3 fw-bold">Your Requisition Forms</h4>

                <div class="d-flex justify-content-end filter-dropdown">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">All</a></li>
                            <li><a class="dropdown-item" href="#">Completed</a></li>
                            <li><a class="dropdown-item" href="#">Pending Approval</a></li>
                            <li><a class="dropdown-item" href="#">On-going</a></li>
                            <li><a class="dropdown-item" href="#">Cancelled</a></li>
                            <li><a class="dropdown-item" href="#">Rejected</a></li>
                        </ul>
                    </div>
                </div>

                <div class="requisition-list">
                    <div class="requisition-card">
                        <div class="requisition-details">
                            <div><strong>Form 7B-109283192038</strong> <span class="text-muted small">Form Submitted:
                                    5/13/2025</span></div>
                            <div><strong>Facility:</strong> Promenade Park</div>
                            <div><strong>Equipment:</strong> • Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;•
                                Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Equipment Name</div>
                            <div><strong>Extra Services:</strong> • Security Personnel
                                <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Technical Support
                            </div>
                        </div>
                        <div class="requisition-total">Total Fee: 1200.00</div>
                        <div class="requisition-actions">
                            <span class="status-badge completed">Completed</span>
                            <button class="btn btn-sm btn-outline-dark">View</button>
                        </div>
                    </div>

                    <div class="requisition-card">
                        <div class="requisition-details">
                            <div><strong>Form 7B-109283192038</strong> <span class="text-muted small">Form Submitted:
                                    5/13/2025</span></div>
                            <div><strong>Facility:</strong> Promenade Park</div>
                            <div><strong>Equipment:</strong> • Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;•
                                Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Equipment Name</div>
                            <div><strong>Extra Services:</strong> • Security Personnel
                                <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Technical Support
                            </div>
                        </div>
                        <div class="requisition-total">Total Fee: 1200.00</div>
                        <div class="requisition-actions">
                            <span class="status-badge pending">Pending Approval</span>
                            <button class="btn btn-sm btn-primary">Edit</button>
                            <button class="btn btn-sm btn-danger">Cancel</button>
                        </div>
                    </div>

                    <div class="requisition-card">
                        <div class="requisition-details">
                            <div><strong>Form 7B-109283192038</strong> <span class="text-muted small">Form Submitted:
                                    5/13/2025</span></div>
                            <div><strong>Facility:</strong> Promenade Park</div>
                            <div><strong>Equipment:</strong> • Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;•
                                Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Equipment Name</div>
                            <div><strong>Extra Services:</strong> • Security Personnel
                                <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Technical Support
                            </div>
                        </div>
                        <div class="requisition-total">Total Fee: 1200.00</div>
                        <div class="requisition-actions">
                            <span class="status-badge cancelled">Cancelled</span>
                            <button class="btn btn-sm btn-outline-dark">View</button>
                            <button class="btn btn-sm btn-outline-dark">Rebook</button>
                        </div>
                    </div>

                    <div class="requisition-card">
                        <div class="requisition-details">
                            <div><strong>Form 7B-109283192038</strong> <span class="text-muted small">Form Submitted:
                                    5/13/2025</span></div>
                            <div><strong>Facility:</strong> Promenade Park</div>
                            <div><strong>Equipment:</strong> • Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;•
                                Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Equipment Name</div>
                            <div><strong>Extra Services:</strong> • Security Personnel
                                <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Technical Support
                            </div>
                        </div>
                        <div class="requisition-total">Total Fee: 1200.00</div>
                        <div class="requisition-actions">
                            <span class="status-badge on-going">On-going</span>
                            <button class="btn btn-sm btn-outline-dark">View</button>
                        </div>
                    </div>

                    <div class="requisition-card">
                        <div class="requisition-details">
                            <div><strong>Form 7B-109283192038</strong> <span class="text-muted small">Form Submitted:
                                    5/13/2025</span></div>
                            <div><strong>Facility:</strong> Promenade Park</div>
                            <div><strong>Equipment:</strong> • Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;•
                                Equipment Name <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Equipment Name</div>
                            <div><strong>Extra Services:</strong> • Security Personnel
                                <br>&emsp;&emsp;&emsp;&emsp;&emsp;• Technical Support
                            </div>
                        </div>
                        <div class="requisition-total">Total Fee: 1200.00</div>
                        <div class="requisition-actions">
                            <span class="status-badge rejected">Rejected</span>
                            <button class="btn btn-sm btn-outline-dark">View</button>
                            <button class="btn btn-sm btn-outline-dark">Resubmit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showResults() {
            document.getElementById('lookupSection').style.display = 'none';
            document.getElementById('resultsSection').style.display = 'block';
            // In a real application, you'd fetch data here based on input
            // and hide/show the "no results" message accordingly.
            document.getElementById('noResultsMessage').style.display = 'none';
        }

        function showLookup() {
            document.getElementById('lookupSection').style.display = 'block';
            document.getElementById('resultsSection').style.display = 'none';
            // Clear input and show no results message when going back to lookup
            document.getElementById('referenceInput').value = '';
            document.getElementById('noResultsMessage').style.display = 'block';
        }

        // Optional: Check URL parameter to load results directly
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('show') === 'results') {
                showResults();
            } else {
                showLookup();
            }
        };
    </script>
@endsection