<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Automatic Late Penalty Notice - Central Philippine University</title>
    <style>
        /* Same styles as your original template */
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #003366; color: white; padding: 20px; text-align: center; }
        .header img { height: 40px; vertical-align: middle; margin-right: 10px; }
        .header h1 { display: inline-block; vertical-align: middle; margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 20px; }
        .footer { background: #003366; color: white; padding: 10px; text-align: center; font-size: 12px; }
        .penalty-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #cc0000; }
        .penalty-info strong { color: #cc0000; }
        .auto-notice { background: #fff3cd; padding: 10px; margin: 10px 0; border-left: 4px solid #ffc107; }
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

            <div class="auto-notice">
                <p><strong>Automated System Notice:</strong> Our system has detected that your booking has exceeded the allowed time and has been automatically marked as late.</p>
            </div>

            <p>Your requisition form has been automatically marked as <strong>Late</strong> by our system. The booking duration has been exceeded, and as a result, a late penalty fee has been applied.</p>

            <div class="penalty-info">
                <p><strong>Automatic Penalty Fee:</strong> ₱{{ number_format($penalty_fee, 2) }}</p>
                <p><strong>Original End Time:</strong> {{ $original_end_time }}</p>
                <p><strong>Detected As Late:</strong> {{ $detected_late_time }}</p>
            </div>
            
            <p>Please settle this amount at the <strong>CPU Business Office</strong> at your earliest convenience.</p>

            <p>Once payment has been made, the administration will verify your record and close the transaction in the system.</p>

            <p><strong>Note:</strong> This is an automated notification. If you believe this is an error, please contact the administration office immediately.</p>

            <p>Thank you for your cooperation and understanding.</p>

            <p>Sincerely,<br>
                Central Philippine University Administration<br>
                <em>Automated System</em></p>
        </div>

        <div class="footer">
            <p>For inquiries, call (033) 329-1971 loc. 1234</p>
            <p>&copy; {{ date('Y') }} Central Philippine University</p>
            <p>Automated message. Do not reply.</p>
        </div>
    </div>
</body>
</html>