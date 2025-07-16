<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Philippine University - Equipment and Facility Booking Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
    <style>
        body {
            background-image: url('{{ asset('assets/cpu-pic1.jpg') }}');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content-wrapper {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin: 2rem auto;
            max-width: 900px;
        }

        @media (max-width: 768px) {
            .main-content-wrapper {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    @extends('layouts.app')

    @section('title', 'Policies')


    @section('content')

            <!-- Start Main Content -->
            <main class="main-content-wrapper container">
                <h1 class="mb-4 text-primary">Reservation Policies</h1>

                <section class="mb-4">
                    <h4 class="text-primary">1. Eligibility</h4>
                    <p>Our services are available to students, alumni, faculty, and employed staff of Central Philippine
                        University, as well as external users from outside the university. For Centralians, a valid school ID
                        number will be required upon form submission.</p>
                </section>

                <section class="mb-4">
                    <h4 class="text-primary">2. Booking Period</h4>
                    <p>All reservations must be submitted through the system at least ten days (10) before the date of intended
                        use. The system will automatically validate requests based on equipment or facility availability. This
                        advance notice period allows for proper scheduling and resource allocation.</p>
                </section>

                <section class="mb-4">
                    <h4 class="text-primary">3. Approval Process</h4>
                    <p>Reservations are automatically routed to the appropriate offices. Users can track the status of their
                        requests in real time using the system-generated reference code.</p>
                </section>

                <section class="mb-4">
                    <h4 class="text-primary">4. Cancellation Policy</h4>
                    <p>Cancellations must be made through the system at least 24 hours before the scheduled use.</p>
                </section>

                <section class="mb-4">
                    <h4 class="text-primary">5. Equipment Returns Policy</h4>
                    <p>Borrowed equipment must be returned on the scheduled return date. Email reminders will be sent three days
                        (3) days before and on the day of return. Late returns will incur penalties, which must be paid at the
                        CPU Business Office.</p>
                </section>

                <section class="mb-4">
                    <h4 class="text-primary">6. User Accountability</h4>
                    <p>Users are responsible for the proper use and safekeeping of any facility or equipment reserved. Any
                        damage, loss, or misuse must be reported immediately and may result in penalties or disciplinary action.
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="text-primary">7. Notification Reminders</h4>
                    <p>The system will send automatic emails for booking confirmation, status updates, and return reminders.
                        Users are responsible for checking their registered email addresses regularly.</p>
                </section>

                <section class="mb-4">
                    <h4 class="text-primary">8. Feedback and Support</h4>
                    <p>A feedback form is available within the system for users to report or suggest improvements. Submitted
                        feedback will help improve future system functionality service.</p>
                </section>

            </main>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>

    @endsection

</html>