@extends('layouts.app')

@section('title', 'About Services - Facilities')

@section('content')
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

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
    </style>

    <section class="hero-section">
        <h1>Facilities</h1>
        <p class="mb-4">Explore a range of facilities designed to host events, meetings, trainings, and recreational
            activities with ease.</p>
    </section>

    <section class="section-content container">
        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/facilities/conference-room.jpeg') }}"
                        class="card-img-top card-img" alt="Conference Room">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            <a href="{{ url('/facility-catalog') }}" class="text-decoration-none stext-dark">
                                Conference & Meeting Rooms
                            </a>
                        </h5>
                        <p class="card-text">Well-equipped spaces for group discussions, seminars, and formal meetings.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/facilities/lecture-hall.jpg') }}" class="card-img-top card-img"
                        alt="Lecture Hall">
                    <div class="card-body">
                        <a href="{{ url('/facility-catalog') }}" class="text-decoration-none text-dark">
                            <h5 class="card-title fw-bold">Lecture & Training Halls
                        </a>
                        </h5>
                        <p class="card-text">Spacious venues ideal for lectures, presentations, and workshops.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/facilities/auditorium.jpg') }}" class="card-img-top card-img"
                        alt="Auditorium">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            <a href="{{ url('/facility-catalog') }}" class="text-decoration-none text-dark">
                                Auditoriums
                            </a>
                        </h5>
                        <p class="card-text">Large venues designed for conferences, ceremonies, and cultural events.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/facilities/court.jpg') }}" class="card-img-top card-img"
                        alt="Gym Court">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            <a href="{{ url('/facility-catalog') }}" class="text-decoration-none text-dark">
                                Sports & Gym Facilities
                            </a>
                        </h5>
                        <p class="card-text">Multipurpose gyms and courts for athletic events, exhibitions, and student
                            activities.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/facilities/study-area.jpg') }}" class="card-img-top card-img"
                        alt="Libraries">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            <a href="{{ url('/facility-catalog') }}" class="text-decoration-none text-dark">
                                Libraries & Study Areas
                            </a>
                        </h5>
                        <p class="card-text">Quiet spaces designed for research, study sessions, and academic gatherings.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/frontend-pics/facilities/comp-lab.jpg') }}" class="card-img-top card-img"
                        alt="Computer Laboratories">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            <a href="{{ url('/facility-catalog') }}" class="text-decoration-none text-dark">
                                Computer Laboratories
                            </a>
                        </h5>
                        <p class="card-text">Fully equipped labs for IT classes, training sessions, and technical workshops.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection