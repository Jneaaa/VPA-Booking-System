<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Philippine University - Facilities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
            background: url('{{ asset('assets/facilities-pic2.jpg') }}') center center / cover no-repeat;
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
            justify-content: space-between;
        }


        .pagination .page-link {
            color: #003366;
            border-color: #003366;
        }

        .pagination .page-item.active .page-link {
            background-color: #003366;
            border-color: #003366;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #f2b123;
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
    @extends('layouts.app')

    @section('title', 'About Facilities')


    @section('content')


        <section class="hero-section">
            <h1>Facilities</h1>
        </section>

        <section class="section-content container">
            <h2 class="mb-3">Our Available Facilities</h2>
            <p class="mb-4">Explore a variety of spaces available for your events, meetings, and activities.</p>
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('assets/image_cb5cef.jpg') }}" class="card-img-top card-img"
                            alt="Meeting Room A">
                        <div class="card-body">
                            <h5 class="card-title">Meeting Room A</h5>
                            <p class="card-text">A versatile space ideal for small group meetings and discussions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('assets/image_bf1def.jpg') }}" class="card-img-top card-img"
                            alt="Lecture Hall B">
                        <div class="card-body">
                            <h5 class="card-title">Lecture Hall B</h5>
                            <p class="card-text">Equipped for large presentations and lectures with ample seating.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('assets/image_cb5cef.jpg') }}" class="card-img-top card-img" alt="Auditorium C">
                        <div class="card-body">
                            <h5 class="card-title">Auditorium C</h5>
                            <p class="card-text">A grand hall perfect for ceremonies, performances, and major events.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('assets/image_bf1def.jpg') }}" class="card-img-top card-img" alt="Gymnasium">
                        <div class="card-body">
                            <h5 class="card-title">Gymnasium</h5>
                            <p class="card-text">Multi-purpose facility for sports, large gatherings, and exhibitions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('assets/image_cb5cef.jpg') }}" class="card-img-top card-img" alt="Library Annex">
                        <div class="card-body">
                            <h5 class="card-title">Library Annex</h5>
                            <p class="card-text">A quiet study area or a spacious room for academic workshops.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('assets/image_bf1def.jpg') }}" class="card-img-top card-img"
                            alt="Computer Lab 1">
                        <div class="card-body">
                            <h5 class="card-title">Computer Lab 1</h5>
                            <p class="card-text">Fully equipped with computers for classes, training, and testing.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('assets/image_bf1def.jpg') }}" class="card-img-top card-img"
                        alt="Mary Thomas Computer Laboratory">
                    <div class="card-body">
                        <h5 class="card-title">Mary Thomas Computer Laboratory</h5>
                        <p class="card-text">Fully equipped with computers for classes, training, and testing.</p>
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
    <script src="{{ asset('js/public/facilities.js') }}"></script>
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