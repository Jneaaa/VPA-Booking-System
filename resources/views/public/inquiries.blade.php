@extends('layouts.app')

@section('title', 'CPU Booking Policies')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('{{ asset('assets/cpu-pic1.jpg') }}');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content-wrapper {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
            margin: 2rem auto;
            max-width: 1200px;
        }

        @media (max-width: 768px) {
            .main-content-wrapper {
                margin: 1rem;
                padding: 1.5rem;
            }
        }

        .policy-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            border-top: 4px solid #1f4988ff;
        }

        .policy-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 2.5rem;
            color: #1f4988ff;
            margin-bottom: 1rem;
        }

        .policy-type {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .policy-details {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .policy-details ul {
            padding-left: 1.2rem;
            margin-bottom: 0;
        }

        .policy-details li {
            margin-bottom: 0.4rem;
        }

        .highlight {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-left: 4px solid #1f4988ff;
            border-radius: 8px;
            margin-top: 2.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            color: #0a58ca;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1rem;
            color: #6c757d;
            font-style: italic;
            margin-bottom: 1.5rem;
        }
    </style>

    <!-- Start Main Content -->
    <main class="main-content-wrapper container">
        <h1 class="mb-4 text-primary">Guidelines on the Charges for the Use of CPU Facilities</h1>

        <!-- Row 1: Cards 1-3 -->
        <div class="row mb-4">
            <!-- Card 1 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="policy-type">Academic Requirements</div>
                        <h5 class="card-title">Classes, Seminars & Conferences</h5>
                        <div class="policy-details">
                            <ul>
                                <li>Venues, PA equipment, and staff overtime are <strong>free of charge</strong></li>
                                <li>LED screen usage requires payment of the charged amount</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="policy-type">University Events</div>
                        <h5 class="card-title">University Programs & Activities</h5>
                        <div class="policy-details">
                            <ul>
                                <li>All university programs and activities are <strong>free of charge</strong></li>
                                <li>Includes ceremonies honoring board passers</li>
                                <li>No venue or equipment fees apply</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="policy-type">Student Organizations</div>
                        <h5 class="card-title">Student-Organized Activities</h5>
                        <p class="small text-muted">(Excluding UDAY activities)</p>
                        <div class="policy-details">
                            <ul>
                                <li>Venues & PA equipment: <strong>75% discount</strong></li>
                                <li>LED screen requires payment</li>
                                <li>Employee overtime paid by organizer</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Cards 4-6 -->
        <div class="row mb-4">
            <!-- Card 4 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="policy-type">Student Activities</div>
                        <h5 class="card-title">Without A Fee</h5>
                        <div class="policy-details">
                            <ul>
                                <li>Venues & PA equipment: <strong>75% discount</strong></li>
                                <li>LED screen requires payment</li>
                                <li>Employee overtime paid by organizer</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="policy-type">Student Activities</div>
                        <h5 class="card-title">With Fee</h5>
                        <div class="policy-details">
                            <ul>
                                <li>Venues & PA equipment: <strong>50% discount</strong></li>
                                <li>LED screen requires payment</li>
                                <li>Employee overtime paid by organizer</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="policy-type">Alumni Activities</div>
                        <h5 class="card-title">Class Reunions</h5>
                        <div class="policy-details">
                            <ul>
                                <li>Venues, PA equipment & staff overtime are <strong>free of charge</strong></li>
                                <li>LED screen requires payment</li>
                                <li>Special rates for alumni gatherings</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Cards 7-9 -->
        <div class="row mb-4">
            <!-- Card 7 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="policy-type">Alumni Activities</div>
                        <h5 class="card-title">Alumni-Organized Events</h5>
                        <div class="policy-details">
                            <ul>
                                <li>Venues & PA equipment: <strong>30% discount</strong></li>
                                <li>LED screen requires payment</li>
                                <li>Employee overtime paid by organizer</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 8 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="policy-type">Alumni Activities</div>
                        <h5 class="card-title">Personal Events of Alumni</h5>
                        <div class="policy-details">
                            <ul>
                                <li>Venues & PA equipment: <strong>20% discount</strong></li>
                                <li>LED screen requires payment</li>
                                <li>Employee overtime paid by organizer</li>
                                <li>Other required fees apply</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 9 -->
            <div class="col-lg-4 col-md-6 mb-2">
                <div class="card policy-card">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                        <div class="policy-type">External Users</div>
                        <h5 class="card-title">Outsiders / External Users</h5>
                        <div class="policy-details">
                            <ul>
                                <li><strong>All necessary charges apply</strong></li>
                                <li>No university discounts available</li>
                                <li>Standard rates for venues & equipment</li>
                                <li>Employee overtime charged accordingly</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note Section -->
        <div class="highlight">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="text-primary mb-2">Important Note</h5>
                    <p class="mb-0"><strong>All bookings are subject to availability</strong> and must comply with the general reservation policies of the university. For detailed booking procedures, eligibility requirements, and to check real-time availability, please refer to the main booking policies page or contact the facilities management office.</p>
                </div>
            </div>
        </div>

    </main>
@endsection