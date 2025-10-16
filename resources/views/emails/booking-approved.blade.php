<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Approved - Payment Required</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #003366; color: white; padding: 20px; text-align: center; }
        .header img { height: 40px; vertical-align: middle; margin-right: 10px; }
        .header h1 { display: inline-block; vertical-align: middle; margin: 0; font-size: 24px; }
        .content { background-color: #f9f9f9; padding: 20px; }
        .footer { background-color: #003366; color: white; padding: 10px; text-align: center; font-size: 12px; }
        .button { background-color: #eeaf01; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .important { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://res.cloudinary.com/dn98ntlkd/image/upload/v1756785959/lvus0zhyldou8td35e3z.png" alt="CPU Logo">
            <h1>Central Philippine University</h1>
        </div>
        
        <div class="content">
            <h2>Your Booking Request Has Been Approved – Payment Required</h2>
            
            <p><strong>Dear {{ $user_name }},</strong></p>
            
            <p>Warm greetings from <strong>Central Philippine University</strong>! 🌸</p>
            
            <p>We are pleased to inform you that your <strong>Booking Request #{{ str_pad($request_id, 4, '0', STR_PAD_LEFT) }}</strong> has been approved and is now awaiting your payment.</p>
            
            <p>To complete your booking, please:</p>
            
            <ol>
                <li>Settle your payment of <strong>₱{{ number_format($approved_fee, 2) }}</strong> at the <strong>CPU Business Office</strong> within campus.</li>
                <li>After payment, kindly <strong>upload a clear photo of your receipt as Proof of Payment</strong> through the Booking website.</li>
            </ol>
            
            <div class="important">
                <p>⚠️ <strong>Important Reminder:</strong><br>
                You have until <strong>{{ $payment_deadline }}</strong> to complete the payment process. If payment is not made within this period, your booking request will be <strong>automatically cancelled</strong>.</p>
            </div>
            
            <p>Thank you for using our booking system. We look forward to serving you soon.</p>
            
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
