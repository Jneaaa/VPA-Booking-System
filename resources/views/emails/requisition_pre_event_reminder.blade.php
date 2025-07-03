<!DOCTYPE html>
<html>
<head>
    <title>Upcoming Reservation Reminder</title>
    <style>
        /* Similar styling as confirmation email */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Upcoming Reservation Reminder</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->first_name }},</p>
            
            <p>This is a reminder about your upcoming reservation:</p>
            
            <p>
                <strong>Date:</strong> {{ $requisition->start_date }}<br>
                <strong>Time:</strong> {{ $requisition->start_time }}<br>
                <strong>Facilities:</strong> 
                {{ $requisition->requestedFacilities->pluck('facility.facility_name')->implode(', ') }}
            </p>
            
            <p>Please ensure all requirements are ready for your event.</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>