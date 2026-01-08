@extends('layouts.app')

@section('title', 'Additional Services & Personnel Requirements')

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
            background-color: rgba(255, 255, 255, 0.97);
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

        .info-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            border-top: 4px solid #dc3545;
            margin-bottom: 2rem;
        }

        .rates-card {
            border-top: 4px solid #0d6efd;
        }

        .personnel-card {
            border-top: 4px solid #198754;
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .rates-card .card-icon {
            color: #0d6efd;
        }

        .personnel-card .card-icon {
            color: #198754;
        }

        .info-card .card-icon {
            color: #dc3545;
        }

        .table-custom {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 0;
        }

        .table-custom thead {
            background-color: #0d6efd;
            color: white;
        }

        .table-custom tbody tr:hover {
            background-color: #f8f9fa;
        }

        .event-size-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
        }

        .event-size-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .event-size-badge {
            background-color: #0d6efd;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .personnel-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed #e9ecef;
        }

        .personnel-item:last-child {
            border-bottom: none;
        }

        .personnel-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: #0d6efd;
            flex-shrink: 0;
        }

        .personnel-count {
            background-color: #0d6efd;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            font-weight: 600;
            flex-shrink: 0;
        }

        .highlight {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            margin-top: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: #0a58ca;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
        }

        .card-title {
            color: #0a58ca;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>

    <!-- Start Main Content -->
    <main class="main-content-wrapper container">
        <h1 class="mb-4 text-primary">Additional Services & Personnel Requirements</h1>

        <!-- Important Information Card -->
        <div class="card info-card">
            <div class="card-body">
                <div class="text-center card-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="card-title text-center">Important Information</h3>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Please note:</strong> Additional services may affect the total approved fee indicated in the request or requisition form.
                </div>
                <p>All required services must be clearly specified in the <strong>Additional Details</strong> section of the requisition form to ensure proper assessment and approval.</p>
            </div>
        </div>

        <!-- Two Column Layout for Rates and Personnel -->
        <div class="row mb-5">
            <!-- Left Column: Overtime Rates -->
            <div class="col-lg-6 mb-4">
                <div class="card rates-card h-100">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="card-title text-center">Personnel Overtime Rates</h3>
                        <p class="text-center text-muted mb-4">Per Hour (PHP)</p>
                        
                        <div class="table-responsive">
                            <table class="table table-custom table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Personnel Type</th>
                                        <th scope="col" class="text-end">Rate (PHP/hour)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <i class="fas fa-broom me-2"></i>
                                            Janitor
                                        </td>
                                        <td class="text-end fw-bold">140</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-shield-alt me-2"></i>
                                            Security Guard
                                        </td>
                                        <td class="text-end fw-bold">80</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-bolt me-2"></i>
                                            Electrical & Mechanical System (EMS)
                                        </td>
                                        <td class="text-end fw-bold">200</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-tools me-2"></i>
                                            Technical Staff (EMC)
                                        </td>
                                        <td class="text-end fw-bold">120</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-user-graduate me-2"></i>
                                            Working Student
                                        </td>
                                        <td class="text-end fw-bold">42</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Minimum Personnel Requirements -->
            <div class="col-lg-6 mb-4">
                <div class="card personnel-card h-100">
                    <div class="card-body">
                        <div class="text-center card-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <h3 class="card-title text-center">Minimum Personnel Requirements</h3>
                        <p class="text-center text-muted mb-4">Based on Event Size</p>
                        
                        <!-- Event Size 50-100 -->
                        <div class="event-size-card mb-4">
                            <div class="event-size-badge">50–100 Participants</div>
                            <div class="personnel-list">
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-broom"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Janitors</strong>
                                        <div class="small text-muted">Cleaning & maintenance</div>
                                    </div>
                                    <div class="personnel-count">2</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Security Guards</strong>
                                        <div class="small text-muted">Safety & security</div>
                                    </div>
                                    <div class="personnel-count">1</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>EMS Personnel</strong>
                                        <div class="small text-muted">Electrical & mechanical systems</div>
                                    </div>
                                    <div class="personnel-count">2</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Technical Staff</strong>
                                        <div class="small text-muted">Technical support & equipment</div>
                                    </div>
                                    <div class="personnel-count">2</div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Size 200-400 -->
                        <div class="event-size-card mb-4">
                            <div class="event-size-badge">200–400 Participants</div>
                            <div class="personnel-list">
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-broom"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Janitors</strong>
                                    </div>
                                    <div class="personnel-count">4</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Security Guards</strong>
                                    </div>
                                    <div class="personnel-count">4</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>EMS Personnel</strong>
                                    </div>
                                    <div class="personnel-count">4</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Technical Staff</strong>
                                    </div>
                                    <div class="personnel-count">4</div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Size 500-2000 -->
                        <div class="event-size-card">
                            <div class="event-size-badge">500–2,000 Participants</div>
                            <div class="personnel-list">
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-broom"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Janitors</strong>
                                    </div>
                                    <div class="personnel-count">10</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Security Guards</strong>
                                    </div>
                                    <div class="personnel-count">10</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>EMS Personnel</strong>
                                    </div>
                                    <div class="personnel-count">4</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Supervisors</strong>
                                        <div class="small text-muted">FMS, CTSSO, BUM</div>
                                    </div>
                                    <div class="personnel-count">4</div>
                                </div>
                                <div class="personnel-item">
                                    <div class="personnel-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="personnel-info">
                                        <strong>Technical Staff</strong>
                                    </div>
                                    <div class="personnel-count">4</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note Section -->
        <div class="highlight">
            <div class="d-flex align-items-start">
                <i class="fas fa-clipboard-check text-warning me-3 mt-1" style="font-size: 1.5rem;"></i>
                <div>
                    <h5 class="text-warning mb-2">Note to Requesting Parties</h5>
                    <p class="mb-0">Personnel deployment is based on approved attendance estimates. Adjustments to manpower requirements may be made as deemed necessary by the university to ensure safety, operational efficiency, and compliance with institutional standards. Please ensure accurate participant estimates when submitting your request.</p>
                </div>
            </div>
        </div>

        <!-- Back to Policies Button -->
        <div class="text-center mt-4">
            <a href="{{ route('policies') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i> Back to Booking Policies
            </a>
        </div>

    </main>
@endsection