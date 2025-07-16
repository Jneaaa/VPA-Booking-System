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
            <span>Are you an Admin? <a href="admin pages/adminlogin.html">Login here.</a></span>
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
                    <a class="nav-link active" href="index">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Booking Catalog
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="facility-catalog">Facilities Catalog</a></li>
                        <li><a class="dropdown-item" href="equipment-catalog">Equipment Catalog</a></li>
                        </ul>
                        </li>
                <li class="nav-item">
                    <a class="nav-link" href="your-bookings">Your Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="policies">Reservation Policies</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        About Services
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="about-facilities">Facilities</a></li>
                        <li><a class="dropdown-item" href="about-equipment">Equipment</a></li>
                        <li><a class="dropdown-item" href="about-services">Services</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user-feedback">Rate Our Services</a>
                </li>
            </ul>
            <a href="reservation-form" class="btn btn-book-now ms-lg-3">Book Now</a>
        </div>
    </div>
</nav>

