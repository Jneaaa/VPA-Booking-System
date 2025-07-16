<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'CPU Booking')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Add other assets (Bootstrap, etc.) here -->
</head>
<body>

    {{-- Header + Navbar --}}
    @include('partials.navbar')

    {{-- Page-specific content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

</body>
</html>
