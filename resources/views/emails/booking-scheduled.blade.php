<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Scheduled - Central Philippine University</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header { background-color: #003366; color: white; padding: 20px; text-align: center; }
        .header img { height: 40px; vertical-align: middle; margin-right: 10px; }
        .header h1 { display: inline-block; vertical-align: middle; margin: 0; font-size: 24px; }
        .content {
            background: #f9f9f9;
            padding: 20px;
        }

        .footer {
            background: #003366;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }

        .receipt-info {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #003366;
        }

        .download-btn {
            display: inline-block;
            background: #eeaf01;
            color: black;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 15px 0;
        }

        .download-btn:hover {
            background: #c08d00ff;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://res.cloudinary.com/dn98ntlkd/image/upload/v1756785959/lvus0zhyldou8td35e3z.png"
                alt="CPU Logo">
            <h1>Central Philippine University</h1>
        </div>

        <div class="content">
            <p>Dear {{ $user_name }},</p>

            <p>Your booking request has been officially scheduled and your official receipt has been generated.</p>

            <div class="receipt-info">
                <h3>Booking Details:</h3>
                <p><strong>Request ID:</strong> #{{ str_pad($request_id, 4, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Official Receipt Number:</strong> {{ $official_receipt_num }}</p>
                <p><strong>Purpose:</strong> {{ $purpose }}</p>
                <p><strong>Schedule:</strong> {{ \Carbon\Carbon::parse($start_date)->format('F j, Y') }} at
                    {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} to
                    {{ \Carbon\Carbon::parse($end_date)->format('F j, Y') }} at
                    {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}
                </p>
                <p><strong>Total Approved Fee:</strong> ₱{{ number_format($approved_fee, 2) }}</p>
            </div>

            <p>Your official receipt has been generated and is available for download. Please present this receipt and
                the facility use permit on your scheduled date.</p>

            <!-- Download Button -->
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('official-receipt', ['requestId' => $request_id]) }}" class="download-btn"
                    target="_blank">
                    Download Official Receipt
                </a>
            </div>

            <p>If you have any questions, please contact the administration office.</p>

            <p>Best regards,<br>
                Central Philippine University Administration</p>
        </div>

<div class="footer">
    <p>For inquiries, please contact us at (033) 329-1971 local 1234</p>
    <p>Central Philippine University &copy; {{ date('Y') }}</p>
    <p>This is an automated message. Please do not reply to this email.</p>
</div>

    </div>
</body>

</html>