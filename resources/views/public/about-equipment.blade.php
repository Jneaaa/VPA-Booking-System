@extends('layouts.app')

@section('title', 'About Services - Equipment')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}">
    <style>
        body {
            background-color: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            /* Larger heading for impact */
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }

        .section-content {
            flex-grow: 1;
        }

        .card-img {
            height: 200px;
            /* Slightly taller images in cards to match the visual */
            object-fit: cover;
            width: 100%;
        }

        /* Ensure cards have consistent height */
        .card.h-100 {
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
    <section class="hero-section">
        <h1>Equipment</h1>
        <p class="mb-4">Choose from a range of equipment categories designed to support academic, technical, and
            event-related needs.</p>
    </section>

    <section class="section-content container">
        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/equipment/audio.jpg') }}" class="card-img-top card-img"
                        alt="Audio Equipment">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Audio Equipment</h5>
                        <p class="card-text">Sound systems, microphones, speakers, and other audio support devices for
                            events and presentations.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/equipment/visual.webp') }}" class="card-img-top card-img"
                        alt="Visual Equipment">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Visual Equipment</h5>
                        <p class="card-text">Projectors, screens, and display systems for lectures, meetings, and visual
                            presentations.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/equipment/lighting.jpg') }}" class="card-img-top card-img"
                        alt="Lighting Equipment">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Lighting Equipment</h5>
                        <p class="card-text">Stage lights, spotlights, and adjustable lighting systems for indoor or outdoor
                            events.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/equipment/conference.jpg') }}" class="card-img-top card-img"
                        alt="Conference Equipment">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Conference Equipment</h5>
                        <p class="card-text">Conference tools including microphones, display panels, and accessories for
                            meetings.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/equipment/event.png') }}" class="card-img-top card-img"
                        alt="Event Equipment">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Event Equipment</h5>
                        <p class="card-text">Essential event tools such as staging materials, podiums, and other event
                            support gear.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/equipment/it.jpg') }}" class="card-img-top card-img"
                        class="card-img-top card-img" alt="IT Equipment">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">IT Equipment</h5>
                        <p class="card-text">Laptops, computers, and communication devices for academic, research, and
                            technical use.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection