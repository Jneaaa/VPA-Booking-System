<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Late Penalty Notice - Central Philippine University</title>
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

        .header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header img {
            height: 40px;
            vertical-align: middle;
            margin-right: 10px;
        }

        .header h1 {
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            font-size: 24px;
        }

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

        .penalty-info {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #cc0000;
        }

        .penalty-info strong {
            color: #cc0000;
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
            <p>Dear {{ $first_name }} {{ $last_name }},</p>

            <p>Your requisition form has been marked as <strong>Late/Damaged</strong>. The booking is currently ongoing, and as a result, a penalty fee must be settled to formally close your request record.</p>

            <div class="penalty-info">
                <p><strong>Penalty Fee:</strong> ₱{{ number_format($penalty_fee, 2) }}</p>

            </div>
            
                            <p>Please settle this amount at the <strong>CPU Business Office</strong> at your earliest convenience.</p>

            <p>Once payment has been made, the administration will verify your record and close the transaction in the system.</p>

            <p>Thank you for your cooperation and understanding.</p>

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
