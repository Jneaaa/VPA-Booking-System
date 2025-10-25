@extends('layouts.app')

@section('title', 'CPU Facility & Equipment Booking Services')

@section('content')
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
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .hero-section.visible {
      opacity: 1;
      transform: translateY(0);
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
      background: #144270ff;
      margin-top: -100px;
      border-top: solid 2px var(--cpu-secondary);
      border-bottom: solid 2px var(--cpu-secondary);
      border-radius: 0.5rem;
      padding: 2rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.28);
      position: relative;
      z-index: 1;
      margin-left: auto;
      margin-right: auto;
      width: 100%;
      margin-bottom: 5%;
      max-width: 1100px;
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .catalog-section.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .catalog-section h4 {
      font-size: 1.8rem;
      color: #fff;
    }

    .catalog-section p {
      color: #dfdfdfff;
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .catalog-card {
      background-color: #ffffffff;
      text-align: center;
      border-radius: 0.5rem;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      transition: box-shadow 0.2s ease;
      padding-bottom: 1rem; 
    }

    .catalog-card:hover {
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.86);
      /* darker shadow, no lift */
    }

    .catalog-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      margin-bottom: 1rem !important;
    }

    .catalog-card h6 {
      font-size: 1.25rem;
      color: #003366;
      margin-bottom: 0.5rem;
    }

    .catalog-card h6 a {
      color: inherit;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .catalog-card h6 a:hover {
      color: #004a94;
    }

    .catalog-card p {
      font-size: 0.9rem;
      color: black;
      flex-grow: 1;
      margin-bottom: 0;
      margin: 0 auto 0; 
      max-width: 85%;
    }
  </style>

  <section class="hero-section text-white text-center">
    <h2 class="fw-bold">Simplify the way you book university facilities,<br>equipment, and services — all in one
      platform,<br>anytime, anywhere.</h2>
    <a href="reservation-form" class="btn btn-warning mt-3 fw-bold">Start Booking</a>
  </section>

  <section class="catalog-section container text-center">
    <h4 class="fw-bold mb-2">Explore Available Resources</h4>
    <p class="mb-4">Browse available facilities, equipment, and services for your next event or activity.</p>
    <div class="row g-3">
      <div class="col-md-4 mb-3">
        <div class="catalog-card">
          <img src="{{ asset('assets/facilities-pic2.JPG') }}" class="img-fluid rounded mb-2" alt="Facilities">
          <h6 class="fw-bold"><a href="about-facilities">Facilities</a></h6>
          <p class="text-muted">Explore our venues to support every activity.</p>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="catalog-card">
          <img src="{{ asset('assets/equipment-pic.jpg') }}" class="img-fluid rounded mb-2" alt="Equipment">
          <h6 class="fw-bold"><a href="about-equipment">Equipment</a></h6>
          <p class="text-muted">Equip your activities with reliable resources.</p>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="catalog-card">
          <img
            src="https://d3njjcbhbojbot.cloudfront.net/api/utilities/v1/imageproxy/https://images.ctfassets.net/wp1lcwdav1p1/5PbtVEidv28K3XNOywzVj3/fedb6ac03469ce4de8720bc0995df898/GettyImages-1620440886.jpg?w=1500&h=680&q=60&fit=fill&f=faces&fm=jpg&fl=progressive&auto=format%2Ccompress&dpr=1&w=1000"
            class="img-fluid rounded mb-2" alt="Extra Services">
          <h6 class="fw-bold"><a href="about-services">Extra Services</a></h6>
          <p class="text-muted">Make your event seamless with extra services.</p>
        </div>
      </div>
    </div>
  </section>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const heroSection = document.querySelector(".hero-section");
    const catalogSection = document.querySelector(".catalog-section");

    if (catalogSection) {
      catalogSection.classList.add("visible");
    }

    if (heroSection) {
      const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });

      observer.observe(heroSection);
    }
  });
</script>


@endsection