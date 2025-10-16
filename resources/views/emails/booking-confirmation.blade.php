<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Requisition Form Received - Central Philippine University</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
           .header { background-color: #003366; color: white; padding: 20px; text-align: center; }
        .header img { height: 40px; vertical-align: middle; margin-right: 10px; }
        .header h1 { display: inline-block; vertical-align: middle; margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 20px; }
        .footer { background: #003366; color: white; padding: 10px; text-align: center; font-size: 12px; }
        .access-code { 
            background: white; 
            padding: 15px; 
            margin: 15px 0; 
            border-left: 4px solid #003366;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
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
            
            <p>We have successfully received your requisition form for the use of CPU facilities and/or equipment. An administrator will now review your request. Once your form has been evaluated and approved, you will receive another email with the results.</p>
            
            <p>Please note that you may cancel your request within 5 days should an emergency arise.</p>
            
            <p>To monitor and track the status of your request, you can enter your code in the "Your Bookings" tab on the booking website.</p>
            
            <div class="access-code">
                Your Request Code:<br>
                <strong>{{ $access_code }}</strong>
            </div>
            
            <p>Thank you for using the CPU Facility and Equipment Booking System.</p>
            
            <p>Sincerely,<br>
            Central Philippine University Administration</p>
        </div>
        
        <div class="footer">
            <p>Central Philippine University &copy; {{ date('Y') }}</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>