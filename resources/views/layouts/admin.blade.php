<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CPU Booking')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-styles.css') }}" />
    <style>
        /* Add sharp edges to all elements */
        * {
            border-radius: 0 !important;
        }

        /* Exclude admin photo container and status circle */
        .profile-img {
            border-radius: 50% !important;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    @include('partials.admin-topbar')

    {{-- Sidebar + Main Content --}}

        @include('partials.admin-sidebar')
        <main>
            @yield('content')
        </main>


    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="{{ asset('js/admin/authentication.js') }}"></script>

    @yield('scripts')


</body>

</html>