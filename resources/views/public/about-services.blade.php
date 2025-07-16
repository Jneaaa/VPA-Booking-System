<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Philippine University - Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}">
    <style>
        /* General Body Styles */
        body {
            background-color: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            /* Ensure no default margin from browser */
        }

        /* --- Hero Section Styles (simplified from equipment page format) --- */
        .hero-section {
            background: url('{{ asset('assets/services-pic.png') }}') center center / cover no-repeat;
            /* Using image from original file */
            height: 200px;
            /* Consistent hero height */
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
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }

        /* --- Section Content / Catalog Styles (from equipment page format) --- */
        .section-content {
            padding-bottom: 2rem;
            flex-grow: 1;
            /* Allows content section to grow and push footer down */
        }

        .card-img {
            height: 200px;
            /* Consistent card image height */
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

        /* --- Pagination Styles (from equipment page format) --- */
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

        /* --- Footer Styles --- */
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto;
            /* Pushes footer to the bottom */
        }
    </style>
</head>

<body>
@extends('layouts.app')

@section('title', 'About Services')


@section('content')

    <section class="hero-section">
        <h1>Extra Services</h1>
    </section>

    <section class="section-content container">
        <h2 class="mb-3">Event Support & Services</h2>
        <p class="mb-4">Our staff are ready to assist your event needs. We offer services like technical support,
            security personnel,
            and logistics assistance to make sure your event runs smoothly.</p>
        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/image_cb5cef.jpg') }}" class="card-img-top card-img" alt="Security Personnel">
                    <div class="card-body">
                        <h5 class="card-title">Security Personnel</h5>
                        <p class="card-text">Professional staff to ensure safety and manage crowd control during your
                            event.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/image_bf1def.jpg') }}" class="card-img-top card-img" alt="Technical Support">
                    <div class="card-body">
                        <h5 class="card-title">Technical Support</h5>
                        <p class="card-text">Get help with setting up projectors, sound systems, and other technical
                            requirements.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/image_cb5cef.jpg') }}" class="card-img-top card-img" alt="Logistics Assistance">
                    <div class="card-body">
                        <h5 class="card-title">Logistics Assistance</h5>
                        <p class="card-text">Help with event setup, decor, and general coordination for smooth
                            operations.</p>
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
    <script src="{{ asset('js/public/extraservices.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggle = document.querySelectorAll('.dropdown-toggle');
            dropdownToggle.forEach(function (toggle) {
                toggle.addEventListener('click', function (event) {
                    event.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    dropdownMenu.classList.toggle('show');
                });
            });
        });
    </script>
</body>

</html>