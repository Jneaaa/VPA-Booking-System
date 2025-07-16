<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Central Philippine University Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <style>
    body {
      background-color: rgba(0, 0, 0, 0.4);
      background-image: url("{{ asset('assets/homepage.jpg') }}");
      background-size: cover;
      background-position: relative;
      background-repeat: no-repeat;
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: -1;

    }

    .hero-section {
      min-height: 50vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 70vh;
      margin-bottom: 50px;
    }

    .hero-section h2 {
      font-size: 2rem;
      margin-bottom: 1.5rem;
    }

    .hero-section .btn {
      padding: 0.75rem 2rem;
      font-size: 1.1rem;
    }

    .catalog-section {
      background-color: white;
      margin-top: -100px;
      border-radius: 0.5rem;
      padding: 3rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      position: relative;
      z-index: 1;
      margin-left: auto;
      margin-right: auto;
      width: 90%;
      margin-bottom: 5%;
      max-width: 1100px;

    }

    .catalog-section h4 {
      font-size: 1.8rem;
      color: #333;
    }

    .catalog-section p.text-muted {
      color: #666;
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .catalog-card {
      text-align: center;
      padding: 1.5rem;
      border-radius: 0.5rem;
      background-color: #003366;
      box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;

    }

    .catalog-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 0.25rem;
      margin-bottom: 1rem;
    }

    .catalog-card h6 {
      font-size: 1.25rem;
      color: white;
      margin-bottom: 0.5rem;
    }

    .catalog-card p {
      font-size: 0.9rem;
      color: white;
      flex-grow: 1;
    }

    .catalog-card .btn {
      background-color: #f2b123;
      color: black;
      border: none;
      padding: 0.5rem 1.5rem;
      font-size: 0.9rem;
      font-weight: bold;
      margin-top: 1rem;
    }

    .catalog-card .btn:hover {
      background-color: #be8200;
    }
  </style>
</head>

<body>

  @extends('layouts.app')

  @section('title', 'Home')


  @section('content')
    <section class="hero-section text-white text-center">
    <h2 class="fw-bold">Simplify the way you book university facilities,<br>equipment, and services — all in one
      platform,<br>anytime, anywhere.</h2>
    <a href="reservation-form" class="btn btn-warning mt-3 fw-bold">Start Booking</a>
    </section>

    <section class="catalog-section container text-center">
    <h4 class="fw-bold mb-2">Explore Available Resources</h4>
    <p class="text-muted mb-4">Browse available facilities, equipment, and services for your next event or activity.
      Everything’s up-to-date and ready to reserve.</p>
    <div class="row">
      <div class="col-md-4 mb-4">
      <div class="catalog-card">
        <img src="{{ asset('assets/facilities-pic2.JPG') }}" class="img-fluid rounded mb-2" alt="Facilities">
        <h6>Facilities</h6>
        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.</p>
        <a href="about-facilities" class="btn">Explore</a>
      </div>
      </div>
      <div class="col-md-4 mb-4">
      <div class="catalog-card">
        <img src="{{ asset('assets/equipment-pic.jpg') }}" class="img-fluid rounded mb-2" alt="Equipment">
        <h6>Equipment</h6>
        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.</p>
        <a href="about-equipment" class="btn">Explore</a>
      </div>
      </div>
      <div class="col-md-4 mb-4">
      <div class="catalog-card">
        <img src="{{ asset('assets/services-pic.png') }}" class="img-fluid rounded mb-2" alt="Services">
        <h6>Extra Services</h6>
        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.</p>
        <a href="about-services" class="btn">Explore</a>
      </div>
      </div>
    </div>
    </section>
  @endsection
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>