@extends('layouts.app')
<head>
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}">
    <style>
        body {
            background-color: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }


        .hero-section {
            background: url('{{ asset('assets/equipment-pic1.jpg') }}') center center / cover no-repeat;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            margin-bottom: 2rem;
            padding: 2rem;

        }

        .hero-section h1 {
            font-size: 4rem;
            /* Larger heading for impact */
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }

        .section-content {
            padding-bottom: 2rem;
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

        /* Styling for pagination */
        .pagination .page-link {
            color: #003366;
            /* Dark blue color for links */
            border-color: #003366;
        }

        .pagination .page-item.active .page-link {
            background-color: #003366;
            border-color: #003366;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #f2b123;
            /* Gold hover effect */
            border-color: #f2b123;
            color: #003366;
        }

        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto;
        }
    </style>
</head>

<body>
        @section('title', 'About Equipment')

        @section('content')

            <section class="hero-section">
                <h1>Equipment</h1>
            </section>

            <section class="section-content container">
                <h2 class="mb-3">What You Can Borrow</h2>
                <p class="mb-4">We offer various types of equipment such as projectors, microphones, and laptops to support
                    academic and
                    extracurricular activities. Browse through our available equipment below.</p>
                <div class="row mt-4">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('assets/mic.jpg') }}" class="card-img-top card-img" alt="Microphone">
                            <div class="card-body">
                                <h5 class="card-title">Microphone</h5>
                                <p class="card-text">Perfect for speaking engagements and presentations, ensuring clear
                                    audio.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('assets/laptop.jpg') }}" class="card-img-top card-img" alt="Laptop">
                            <div class="card-body">
                                <h5 class="card-title">Laptop</h5>
                                <p class="card-text">Available for academic use, research, and event hosting, fully
                                    equipped.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('assets/projector.jpg') }}" class="card-img-top card-img" alt="Projector">
                            <div class="card-body">
                                <h5 class="card-title">Projector</h5>
                                <p class="card-text">High-resolution projectors for impactful presentations and large-scale
                                    screenings.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('assets/speaker.jpg') }}" class="card-img-top card-img" alt="Speaker">
                            <div class="card-body">
                                <h5 class="card-title">Portable Speaker System</h5>
                                <p class="card-text">Ideal for events requiring audio amplification in various settings.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('assets/camera.jpg') }}" class="card-img-top card-img" alt="Camera">
                            <div class="card-body">
                                <h5 class="card-title">Digital Camera</h5>
                                <p class="card-text">Capture high-quality photos and videos for academic projects or events.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('assets/whiteboard.jpg') }}" class="card-img-top card-img" alt="Whiteboard">
                            <div class="card-body">
                                <h5 class="card-title">Interactive Whiteboard</h5>
                                <p class="card-text">Enhance your presentations and collaborative sessions with an
                                    interactive
                                    display.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <nav aria-label="Page navigation example" class="d-flex justify-content-center mt-4">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </section>
        @endsection

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dropdownElements = document.querySelectorAll('.dropdown-toggle');
                dropdownElements.forEach(dropdown => {
                    new bootstrap.Dropdown(dropdown);
                });
            });
        </script>
    </body>

</html>