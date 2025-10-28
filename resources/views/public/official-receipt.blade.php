<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Philippine University - Official Receipt & Permit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Screen Styles */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .document-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            page-break-after: auto;
        }

        .permit-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            page-break-after: auto;
        }

        .document-header {
            text-align: center;
        }

        .document-header h2 {
            font-weight: bold;
            text-transform: uppercase;
            color: #003366;
        }

        .issued-section {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 20px;
        }

        .issued-section p {
            margin: 2px;
            line-height: 1.4;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border-spacing: 0;
        }

        .details-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }

        .field-group {
            padding: 10px;
        }

        .field-group strong {
            font-size: 0.95em;
            display: block;
            font-weight: 600;
            color: #333;
        }

        .field-group div {
            font-size: 0.75em;
            color: #555;
            word-wrap: break-word;
        }

        .inner-table tr:nth-child(odd) {
            background-color: #fafafaff;
        }

        .document-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #666;
        }

.signature-block {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 15px;
    text-align: center;
}

.signature-block > div {
    flex: 0 0 auto;
    min-width: 200px; /* Increased from 160px to give more space for 3 per row */
    display: flex;
    flex-direction: column;
    align-items: center;
}

.signature-image-container {
    width: 160px; /* Slightly increased to match the new min-width */
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}
.signature-image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

        .signature-block img {
            width: 140px;
            height: auto;
            margin-bottom: 2px;
        }

        .signature-block .name {
            font-weight: bold;
            font-size: 1em;
            margin: 2px 0;
            color: #003366;
        }

        .signature-block .title {
            font-size: 0.85em;
            color: #555;
            margin: 2px 0;
        }

        .signature-block small {
            font-size: 0.75em;
            color: #777;
        }

        .print-button-container {
            text-align: center;
            margin: 30px 0;
        }

        .signature-block {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .signature-block>div {
            flex: 0 0 auto;
            min-width: 160px;
        }
    </style>
</head>

<body>
    <!-- Official Receipt Container -->
    <div class="document-container">
        <div class="document-header">
            <img src="{{ asset('assets/cpu-logo.png') }}" alt="CPU Logo"
                style="width: 100px; height: auto; margin-bottom: 10px;">
            <h2>Official Receipt</h2>
            <p>Booking Reference: <strong>#{{ $receiptData['official_receipt_num'] ?? 'N/A' }}</strong></p>
        </div>

        <hr style="border: none; border-top: 4px solid #585858; margin: 25px 0; ">

        <div class="document-section">
            <h5>Customer Details</h5>
            <p><strong>Name:</strong> {{ $receiptData['user_name'] ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $receiptData['user_email'] ?? 'N/A' }}</p>
            <p><strong>Organization:</strong> {{ $receiptData['organization_name'] ?? 'N/A' }}</p>
        </div>

        <div class="document-section">
            <h5>Booking Details</h5>
            <p><strong>Facility:</strong> {{ $receiptData['facility_name'] ?? 'N/A' }}</p>
            <p><strong>Date & Time:</strong> {{ $receiptData['schedule'] ?? 'N/A' }}</p>
            <p><strong>Purpose:</strong> {{ $receiptData['purpose'] ?? 'N/A' }}</p>
            <p><strong>Status:</strong> Confirmed & Scheduled</p>
        </div>

        <div class="document-section">
            <h5>Payment Summary</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($receiptData['fee_breakdown']))
                        @foreach($receiptData['fee_breakdown'] as $fee)
                            <tr>
                                <td>{{ $fee['description'] }}</td>
                                <td class="text-end">₱{{ number_format($fee['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <th>Total</th>
                        <th class="text-end">₱{{ number_format($receiptData['total_fee'] ?? 0, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="document-footer">
            <p>Issued on {{ $receiptData['issued_date'] ?? date('F j, Y') }}</p>
            <p><strong>Central Philippine University</strong></p>
        </div>
    </div>

    <!-- Permit and Approval Container -->
    <div class="permit-container">
        <div class="document-header">
            <h2>Facility Use Permit</h2>
        </div>
        <div class="issued-section">
            <p><strong>Central Philippine University</strong></p>
            <p>Issued on {{ $receiptData['issued_date'] ?? date('F j, Y') }}</p>
        </div>

        <hr style="border: none; border-top: 4px solid #585858; margin: 25px 0; ">

        <div class="document-section text-center">
            <h6>Permit Issued To:</h6>
            <p class="name"
                style="font-size: 1.3rem; font-weight: bold; text-transform: uppercase; margin-bottom: 20px;">
                {{ $receiptData['organization_name'] ?? $receiptData['user_name'] ?? 'N/A' }}
            </p>
        </div>

        <table class="details-table" style="margin-top: 20px;">
            <tr>
                <!-- Left Column -->
                <td>
                    <table class="inner-table">
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>Contact Person</strong>
                                    <div>{{ $receiptData['user_name'] ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>Contact Number</strong>
                                    <div>{{ $receiptData['contact_number'] ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>Number of Participants</strong>
                                    <div>{{ $receiptData['num_participants'] ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>Start Schedule</strong>
                                    <div>{{ $receiptData['start_schedule'] ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Right Column -->
                <td>
                    <table class="inner-table">
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>Activity/Purpose</strong>
                                    <div>{{ $receiptData['purpose'] ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>Venue</strong>
                                    <div>{{ $receiptData['facility_name'] ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>Request ID</strong>
                                    <div>#{{ str_pad($receiptData['request_id'] ?? '0000', 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="field-group">
                                    <strong>End Schedule</strong>
                                    <div>{{ $receiptData['end_schedule'] ?? 'N/A' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

<div class="document-section">
    <div class="signature-block">
        <!-- Dynamic Approving Admins - First Row (3 admins) -->
        @if(isset($receiptData['approving_admins']) && count($receiptData['approving_admins']) > 0)
            @foreach($receiptData['approving_admins'] as $index => $admin)
                @if($index < 3) <!-- Limit to 3 admins per row -->
                    <div>
                        <div class="signature-image-container">
                            @if(!empty($admin['signature_url']))
                                <img src="{{ $admin['signature_url'] }}" alt="Signature">
                            @else
                                <div style="width: 100%; height: 100%; border-bottom: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">
                                    <small class="text-muted">No Signature</small>
                                </div>
                            @endif
                        </div>
                        <p class="name">{{ $admin['name'] }}</p>
                        <p class="title">{{ $admin['title'] }}</p>
                        <p><small class="text-muted">Date approved: {{ $admin['date_approved'] ?? 'N/A' }}</small></p>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    <!-- Second row for additional admins if there are more than 3 -->
    @if(isset($receiptData['approving_admins']) && count($receiptData['approving_admins']) > 3)
        <div class="signature-block" style="margin-top: 20px;">
            @foreach($receiptData['approving_admins'] as $index => $admin)
                @if($index >= 3 && $index < 6) <!-- Second row, limit to 3 more -->
                    <div>
                        <div class="signature-image-container">
                            @if(!empty($admin['signature_url']))
                                <img src="{{ $admin['signature_url'] }}" alt="Signature">
                            @else
                                <div style="width: 100%; height: 100%; border-bottom: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">
                                    <small class="text-muted">No Signature</small>
                                </div>
                            @endif
                        </div>
                        <p class="name">{{ $admin['name'] }}</p>
                        <p class="title">{{ $admin['title'] }}</p>
                        <p><small class="text-muted">Date approved: {{ $admin['date_approved'] ?? 'N/A' }}</small></p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Third row for additional admins if there are more than 6 -->
    @if(isset($receiptData['approving_admins']) && count($receiptData['approving_admins']) > 6)
        <div class="signature-block" style="margin-top: 20px;">
            @foreach($receiptData['approving_admins'] as $index => $admin)
                @if($index >= 6 && $index < 9) <!-- Third row, limit to 3 more -->
                    <div>
                        <div class="signature-image-container">
                            @if(!empty($admin['signature_url']))
                                <img src="{{ $admin['signature_url'] }}" alt="Signature">
                            @else
                                <div style="width: 100%; height: 100%; border-bottom: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">
                                    <small class="text-muted">No Signature</small>
                                </div>
                            @endif
                        </div>
                        <p class="name">{{ $admin['name'] }}</p>
                        <p class="title">{{ $admin['title'] }}</p>
                        <p><small class="text-muted">Date approved: {{ $admin['date_approved'] ?? 'N/A' }}</small></p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
    <!-- Third row for additional admins if there are more than 6 -->
    @if(isset($receiptData['approving_admins']) && count($receiptData['approving_admins']) > 6)
        <div class="signature-block" style="margin-top: 20px;">
            @foreach($receiptData['approving_admins'] as $index => $admin)
                @if($index >= 6 && $index < 9) <!-- Third row, limit to 3 more -->
                    <div>
                        <div class="signature-image-container">
                            @if(!empty($admin['signature_url']))
                                <img src="{{ $admin['signature_url'] }}" alt="Signature">
                            @else
                                <div style="width: 100%; height: 100%; border-bottom: 1px solid #ccc; display: flex; align-items: center; justify-content: center;">
                                    <small class="text-muted">No Signature</small>
                                </div>
                            @endif
                        </div>
                        <p class="name">{{ $admin['name'] }}</p>
                        <p class="title">{{ $admin['title'] }}</p>
                        <p><small class="text-muted">Date approved: {{ $admin['date_approved'] ?? 'N/A' }}</small></p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
    </div>

    <small class="text-center mt-4 text-muted d-block">
        This reservation has been officially approved and payment confirmed. Please present your permit on your
        scheduled date.
    </small>

    <div class="print-button-container">
        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Back to My
            Bookings</button>
        <button type="button" class="btn btn-primary me-2" onclick="window.print()">Print Receipt & Permit</button>
        <button type="button" class="btn btn-success" id="downloadPdf">Download as PDF</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('downloadPdf').addEventListener('click', function () {
            // Implement PDF download functionality here
            window.print(); // Temporary solution - replace with proper PDF generation
        });
    </script>
</body>

</html>