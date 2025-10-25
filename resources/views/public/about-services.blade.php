@extends('layouts.app')

@section('title', 'About Services - Extra Resources')

@section('content')
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}">
    <style>
        body {
            background-color: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .hero-section {
            position: relative;
            flex-direction: column;
            background: url('{{ asset('assets/homepage.jpg') }}') center center / cover no-repeat;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            padding: 2rem;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            /* Adjust opacity for darker/lighter overlay */
            z-index: 0;
        }

        .hero-section h1,
        .hero-section h2,
        .hero-section p,
        .hero-section a {
            position: relative;
            z-index: 1;
            /* Keeps text above the overlay */
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }

        .section-content {
            flex-grow: 1;
        }

        .card-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .card.h-100 {
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
    </style>


    <section class="hero-section text-center">
        <h1>Extra Services</h1>
<p class="mb-4 mx-auto text-center" style="max-width: 700px;">
    Access a range of support services designed to enhance event coordination, technical setup, and on-site management.
</p>

    </section>

    <section class="section-content container">
        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/services/security.jpg') }}" class="card-img-top card-img"
                        alt="Security">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Security Personnel</h5>
                        <p class="card-text">Professional staff to ensure safety and manage crowd control during your
                            event.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/services/tech-support.jpg') }}" class="card-img-top card-img"
                        alt="Technical Support">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Technical Support</h5>
                        <p class="card-text">Get help with setting up projectors, sound systems, and other technical
                            requirements.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/services/logistics.jpg') }}" class="card-img-top card-img"
                        alt="Logistics">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Logistics Assistance</h5>
                        <p class="card-text">Help with event setup, decor, and general coordination for smooth
                            operations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection