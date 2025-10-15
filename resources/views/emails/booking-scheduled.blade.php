<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Scheduled - Central Philippine University</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #003366; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .footer { background: #003366; color: white; padding: 10px; text-align: center; font-size: 12px; }
        .receipt-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #003366; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Central Philippine University</h1>
            <h2>Booking Scheduled Confirmation</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $user_name }},</p>
            
            <p>Your booking request has been officially scheduled and your official receipt has been generated.</p>
            
            <div class="receipt-info">
                <h3>Booking Details:</h3>
                <p><strong>Request ID:</strong> #{{ str_pad($request_id, 4, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Official Receipt Number:</strong> {{ $official_receipt_num }}</p>
                <p><strong>Purpose:</strong> {{ $purpose }}</p>
                <p><strong>Schedule:</strong> {{ \Carbon\Carbon::parse($start_date)->format('F j, Y') }} at {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} to {{ \Carbon\Carbon::parse($end_date)->format('F j, Y') }} at {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}</p>
                <p><strong>Total Approved Fee:</strong> ₱{{ number_format($approved_fee, 2) }}</p>
            </div>
            
            <p>Your official receipt has been generated and is available for download. Please present this receipt and the facility use permit on your scheduled date.</p>
            
            <p>If you have any questions, please contact the administration office.</p>
            
            <p>Best regards,<br>
            Central Philippine University Administration</p>
        </div>
        
        <div class="footer">
            <p>Central Philippine University &copy; {{ date('Y') }}</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>