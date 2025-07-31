<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CPU Booking')</title>
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
