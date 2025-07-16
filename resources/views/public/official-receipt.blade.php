<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Philippine University - Official Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
    <style>
        body {
            background-color: #f8f9fa;
        }

        .top-header-bar {
            background-color: #003366;
            color: white;
            padding: 10px 0;
        }

        .cpu-brand {
            display: flex;
            align-items: center;
        }

        .cpu-brand img {
            height: 50px;
            margin-right: 15px;
        }

        .cpu-brand .title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .cpu-brand .subtitle {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .admin-login a {
            color: #ffc107;
            text-decoration: none;
        }

        .admin-login a:hover {
            text-decoration: underline;
        }

        .main-navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .main-navbar .nav-link {
            color: #003366;
            font-weight: 500;
        }

        .main-navbar .nav-link.active {
            color: #007bff;
            font-weight: bold;
        }

        .main-navbar .dropdown-menu .dropdown-item {
            color: #003366;
        }

        .main-navbar .dropdown-item.active,
        .main-navbar .dropdown-item:active {
            background-color: #007bff;
            color: white;
        }

        .btn-book-now {
            background-color: #007bff;
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: bold;
        }

        .btn-book-now:hover {
            background-color: #0056b3;
            color: white;
        }

        .main-content {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .section-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .section-card h5,
        .section-card h6 {
            color: #003366;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-item:last-of-type {
            border-bottom: none;
        }

        .detail-item .label {
            font-weight: 500;
            color: #555;
        }

        .detail-item .value {
            text-align: right;
            color: #333;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item .item-details {
            flex-grow: 1;
            margin-right: 15px;
        }

        .summary-item .item-price {
            font-weight: bold;
            color: #003366;
        }

        .total-price {
            font-size: 1.5em;
            font-weight: bold;
            color: #003366;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #003366;
        }

        .slideshow-placeholder {
            background-color: #e9ecef;
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #6c757d;
            border-radius: 5px;
            margin-bottom: 15px;
            font-style: italic;
        }

        .included-equipment-list {
            list-style: none;
            padding: 0;
        }

        .included-equipment-list li {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dashed #f0f0f0;
        }

        .included-equipment-list li:last-child {
            border-bottom: none;
        }

        .payment-status-badge {
            font-size: 1.1rem;
            padding: 8px 12px;
            border-radius: 5px;
            font-weight: bold;
        }

        /* Status Colors */
        .status-pending { background-color: #ffc107; color: #333; }
        .status-approved { background-color: #28a745; color: white; }
        .status-rejected { background-color: #dc3545; color: white; }
        .status-paid { background-color: #007bff; color: white; }
        .status-completed { background-color: #6c757d; color: white; }

        /* Styles for the new "Permit Issued To" section */
        .permit-issued-to {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background-color: #e9f7ff; /* Light blue background */
            border: 1px solid #cceeff; /* Light blue border */
            border-radius: 8px;
        }

        .permit-issued-to h4 {
            color: #003366;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }

        .permit-issued-to p {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #333;
        }

        .permit-issued-to .name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0056b3; /* Darker blue for emphasis */
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .permit-issued-to .detail-row {
            display: flex;
            justify-content: center; /* Center items for a cleaner look */
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
            gap: 15px; /* Space between items */
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #cceeff;
        }

        .permit-issued-to .detail-row span {
            flex: 1 1 auto; /* Allow items to grow/shrink but maintain base width */
            min-width: 250px; /* Minimum width before wrapping */
            text-align: left;
        }

        .permit-issued-to .detail-row .label {
            font-weight: 600;
            color: #003366;
            margin-right: 5px;
        }

        .signature-block {
            margin-top: 50px;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }

        .signature-block .name {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #003366;
        }

        .signature-block .title {
            font-size: 0.9em;
            color: #555;
        }

        /* Print Button Styles */
        @media print {
            .navbar, .top-header-bar, footer, .btn-group, .d-flex.justify-content-end.mt-4, .print-button-container {
                display: none !important;
            }
            .main-content {
                margin-top: 0;
                padding-top: 0;
            }
            .section-card {
                box-shadow: none;
                border: 1px solid #dee2e6; /* Add light border for print */
                margin-bottom: 10px; /* Reduce margin for print */
            }
            .permit-issued-to {
                box-shadow: none;
                border: 1px solid #cceeff;
            }
            body {
                background-color: white;
            }
            .container {
                width: 100% !important; /* Make container full width for printing */
                max-width: none !important;
            }
            .row > div {
                flex: 0 0 100% !important; /* Make columns full width for printing */
                max-width: 100% !important;
            }
        }

    </style>
</head>

<body>
  <header class="top-header-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="cpu-brand">
            <img src="{{ asset('assets/public/cpu-logo.png') }}" alt="CPU Logo">
            <div>
                <div class="title">Central Philippine University</div>
                <div class="subtitle">Equipment and Facility Booking Services</div>
            </div>
        </div>
        <div class="admin-login">
            <span>Are you an Admin? <a href="admin pages/adminlogin.html">Login here.</a></span>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Booking Catalog
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="facilities">About Facilities</a></li>
                        <li><a class="dropdown-item" href="equipmentpage">About Equipment</a></li>
                        <li><a class="dropdown-item" href="extraservicespage">About Services</a></li>
                        <li><a class="dropdown-item" href="bookingcatalog">Booking Catalog</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mybookingpage">My Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="policies">Reservation Policies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="feedbackpage">Rate Our Services</a>
                </li>
            </ul>
            <a href="bookingpage" class="btn btn-book-now ms-lg-3">Book Now</a>
        </div>
    </div>
</nav>

    <div class="container main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Official Reservation Receipt <span class="badge bg-success">#RES-20250617-001</span></h4>
            <span class="payment-status-badge status-approved">Approved & Paid</span>
        </div>

        <div class="permit-issued-to">
            <h4>THIS PERMIT IS ISSUED TO:</h4>
            <p class="name">CPU Computer Science Society</p>
            <div class="detail-row">
                <span><span class="label">Contact Person:</span> Juan Dela Cruz</span>
                <span><span class="label">Cell Number:</span> +63 9XX XXX XXXX</span>
            </div>
            <div class="detail-row">
                <span><span class="label">Number of Participants:</span> 30</span>
            </div>
            <div class="detail-row">
                <span><span class="label">Activity/Purpose:</span> Workshop: Advanced Web Development</span>
            </div>
            <div class="detail-row">
                <span><span class="label">Venue:</span> MTCL 7, Computer Laboratory, Computer Science Department</span>
            </div>
            <div class="detail-row">
                <span><span class="label">Date & Time:</span> May 19, 2025 - May 20, 2025 (10:00 AM - 11:00 AM Daily)</span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="section-card">
                    <h5>Reservation Overview</h5>
                    <div class="detail-item">
                        <span class="label">Date Issued:</span>
                        <span class="value">June 17, 2025</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Booking Type:</span>
                        <span class="value">Facility & Equipment</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Applicant Type:</span>
                        <span class="value">Internal</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Purpose Description:</span>
                        <span class="value">Weekly workshop for students to learn advanced topics in web development, including Laravel and React.</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Attached Letter:</span>
                        <span class="value"><a href="#" class="text-decoration-none">view_letter_res001.pdf</a></span>
                    </div>
                </div>

                <div class="section-card">
                    <h5>Booking Summary</h5>

                    <div class="mb-3">
                        <h6>Facility</h6>
                        <div class="summary-item">
                            <div class="item-details">
                                <div>MTCL 7</div>
                                <small class="text-muted">Computer Laboratory, Computer Science Department</small>
                            </div>
                            <div class="item-price">₱750.00</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>Equipment</h6>
                        <div class="summary-item">
                            <div class="item-details">
                                <div>Projector</div>
                                <small class="text-muted">High Lumen Classroom Projector</small>
                            </div>
                            <div class="item-price">₱250.00</div>
                        </div>
                        <div class="summary-item">
                            <div class="item-details">
                                <div>Wireless Microphone (x2)</div>
                                <small class="text-muted">PA System Compatible</small>
                            </div>
                            <div class="item-price">₱300.00</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>Extra Services</h6>
                        <div class="summary-item">
                            <div class="item-details">
                                <div>Technical Support (4 hours)</div>
                                <small class="text-muted">On-site technical assistance</small>
                            </div>
                            <div class="item-price">₱500.00</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center total-price">
                        <span>Total Amount Paid:</span>
                        <span>₱1,800.00</span>
                    </div>
                    <small class="text-success text-end d-block mt-2">Payment Confirmed: June 18, 2025</small>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="section-card">
                    <h5>Scheduled Details</h5>
                    <div class="detail-item">
                        <span class="label">Date(s):</span>
                        <span class="value">May 19, 2025 - May 20, 2025</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Time:</span>
                        <span class="value">10:00 AM - 11:00 AM (Daily)</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Duration:</span>
                        <span class="value">2 Days</span>
                    </div>
                </div>

                <div class="section-card">
                    <h5>Facility Layout & Setup</h5>
                    <p class="mb-2">Booked Facility: <strong>MTCL 7</strong></p>
                    <div class="slideshow-placeholder">
                        Facility Images / Layout Plan
                    </div>

                    <h6 class="mt-4">Included Equipment (Allocated)</h6>
                    <ul class="included-equipment-list">
                        <li><span>IT Equipment:</span> <span>15 pcs</span></li>
                        <li><span>Chairs:</span> <span>30 pcs</span></li>
                        <li><span>Tables:</span> <span>15 pcs</span></li>
                        <li><span>Air Conditioning:</span> <span>3 units</span></li>
                    </ul>

                    <h6 class="mt-4">Setup Requests (Confirmed)</h6>
                    <ul class="included-equipment-list">
                        <li><span>Tables needed:</span> <span>15</span></li>
                        <li><span>Chairs needed:</span> <span>30</span></li>
                        <li><span>Preferred Layout:</span> <span><a href="#" class="text-decoration-none">circular_setup.jpg</a></span></li>
                        <li><span>Additional Notes:</span> <span>Please ensure good lighting and sound.</span></li>
                    </ul>
                </div>

                <div class="section-card">
                    <h5>Official Approval</h5>
                    <div class="signature-block">
                        <p class="mb-1">(<span class="name">Engr. Dany C. Molina</span>)</p>
                        <p class="title">VP for Administration</p>
                        <p class="mt-3"><small class="text-muted">Date Approved: June 18, 2025</small></p>
                    </div>
                    <p class="text-center mt-4 text-muted">This reservation has been officially approved and payment confirmed. Please present this receipt on your scheduled date.</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4 print-button-container">
            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Back to My Bookings</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">Save/Print as PDF</button>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">Copyright. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>