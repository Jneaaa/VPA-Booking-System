<style>
/* Style the 'How to book?' trigger text */
.navbar .how-to-book {
    font-size: 0.85rem;
    cursor: pointer;
    text-decoration: underline;
    color: #4d4d4dff;
}

.custom-tooltip .tooltip-inner {
    background-color: #000000; /* black background */
    color: #ffffff;            /* white text */
    font-size: 0.85rem;
    padding: 0.75rem 0.75rem;  /* reduce top/bottom padding */
    line-height: 1.2;          /* tighten spacing */
    max-width: 300px;
    text-align: left;
    white-space: pre-line;     /* preserves line breaks */
}

.custom-tooltip .tooltip-arrow::before {
    border-bottom-color: #000000; /* match arrow to bg */
}


</style>

<header class="top-header-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="cpu-brand">
            <img src="{{ asset('assets/cpu-logo.png') }}" alt="CPU Logo">
            <div>
                <div class="title">Central Philippine University</div>
                <div class="subtitle">Equipment and Facility Booking Services</div>
            </div>
        </div>
        <div class="admin-login">
            <span>Are you an Admin? <a href="{{ url('admin/admin-login') }}">Login here.</a></span>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('index') ? 'active' : '' }}" href="{{ url('index') }}">Home</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('booking-catalog*') || Request::is('facility-catalog') || Request::is('equipment-catalog') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Booking Catalog
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ Request::is('facility-catalog') ? 'active' : '' }}"
                                href="{{ asset('facility-catalog') }}">
                                Facility Catalog
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ Request::is('equipment-catalog') ? 'active' : '' }}"
                                href="{{ asset('equipment-catalog') }}">
                                Equipment Catalog
                            </a>
                        </li>
                    </ul>

                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Request::is('your-bookings') ? 'active' : '' }}"
                        href="{{ url('your-bookings') }}">Your Bookings</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('policies') ? 'active' : '' }}"
                        href="{{ url('policies') }}">Reservation Policies</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('about-facilities', 'about-equipment', 'about-services') ? 'active' : '' }}"
                        href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        About Services
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item {{ Request::is('about-facilities') ? 'active' : '' }}"
                                href="{{ url('about-facilities') }}">Facilities</a></li>
                        <li><a class="dropdown-item {{ Request::is('about-equipment') ? 'active' : '' }}"
                                href="{{ url('about-equipment') }}">Equipment</a></li>
                        <li><a class="dropdown-item {{ Request::is('about-services') ? 'active' : '' }}"
                                href="{{ url('about-services') }}">Services</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('user-feedback') ? 'active' : '' }}"
                        href="{{ url('user-feedback') }}">Rate Our Services</a>
                </li>
            </ul>

<div class="d-flex align-items-center ms-lg-3">
<span class="me-2 how-to-book d-flex align-items-center" 
      data-bs-toggle="tooltip" 
      data-bs-placement="bottom" 
      data-bs-custom-class="custom-tooltip"
      title="1. Browse the catalog and add venues or equipment to your booking cart.
2. Go to the reservation form via 'Book Now' or your cart.
3. Fill in required booking data and check item availability for your timeslot.
4. Read reservation policies before submitting.">
    How to book? 
    <i class="bi bi-question-circle ms-1" style="font-size: 0.9rem;"></i>
</span>



    <a href="{{ url('reservation-form') }}" class="btn btn-book-now">Book Now</a>
</div>


        </div>
    </div>
</nav>

<script>
    // Initialize Bootstrap tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (el) {
    return new bootstrap.Tooltip(el);
});

    // Initialize Bootstrap dropdowns
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownElements = document.querySelectorAll('.dropdown-toggle');
        dropdownElements.forEach(dropdown => {
            new bootstrap.Dropdown(dropdown);
        });
    });
</script>