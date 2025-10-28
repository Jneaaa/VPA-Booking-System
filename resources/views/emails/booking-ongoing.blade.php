<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Now Ongoing - Central Philippine University</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #003366; color: white; padding: 20px; text-align: center; }
        .header img { height: 40px; vertical-align: middle; margin-right: 10px; }
        .header h1 { display: inline-block; vertical-align: middle; margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 20px; }
        .footer { background: #003366; color: white; padding: 10px; text-align: center; font-size: 12px; }
        .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://res.cloudinary.com/dn98ntlkd/image/upload/v1756785959/lvus0zhyldou8td35e3z.png" alt="CPU Logo">
            <h1>Central Philippine University</h1>
        </div>

        <div class="content">
            <p>Dear {{ $first_name }} {{ $last_name }},</p>

            <div class="info-box">
                <p><strong>Your booking is now officially ongoing!</strong></p>
                <p><strong>Request ID:</strong> {{ $request_id }}</p>
                <p><strong>Start Time:</strong> {{ $start_time }}</p>
                <p><strong>End Time:</strong> {{ $end_time }}</p>
                <p><strong>Facilities:</strong> {{ $facilities }}</p>
                <p><strong>Access Code:</strong> {{ $access_code }}</p>
            </div>

            <p>Your scheduled booking has now begun. Please proceed to your reserved facility and present your access code if required.</p>

            <p><strong>Important Reminders:</strong></p>
            <ul>
                <li>Ensure you adhere to the scheduled end time</li>
                <li>Follow all facility rules and guidelines</li>
                <li>Return any borrowed equipment in good condition</li>
                <li>Late returns may incur penalty fees</li>
            </ul>

            <p>Thank you for choosing Central Philippine University facilities!</p>

            <p>Sincerely,<br>
                Central Philippine University Administration</p>
        </div>

        <div class="footer">
            <p>For inquiries, call (033) 329-1971 loc. 1234</p>
            <p>&copy; {{ date('Y') }} Central Philippine University</p>
            <p>Automated message. Do not reply.</p>
        </div>
    </div>
</body>
</html>