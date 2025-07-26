<!DOCTYPE html>
<html>
<head>
    <title>Requisition Submission Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 0.9em; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Requisition Submitted Successfully</h1>
            <p>Reference #{{ $requisition->request_id }}</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->first_name }} {{ $user->last_name }},</p>
            
            <p>Your requisition has been received and is currently pending approval. Here are the details:</p>
            
            <h3>Reservation Details</h3>
            <p>
                <strong>Date:</strong> {{ $requisition->start_date }} to {{ $requisition->end_date }}<br>
                <strong>Time:</strong> {{ $requisition->start_time }} to {{ $requisition->end_time }}<br>
                <strong>Participants:</strong> {{ $requisition->num_participants }}<br>
                <strong>Purpose:</strong> {{ $requisition->purpose->purpose_name }}<br>
                <strong>Tentative Fee:</strong> ₱{{ number_format($requisition->tentative_fee, 2) }}
            </p>
            
            @if(count($facilities) > 0)
            <h3>Requested Facilities</h3>
            <table>
                <thead>
                    <tr>
                        <th>Facility</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facilities as $facility)
                    <tr>
                        <td>{{ $facility->facility->facility_name }}</td>
                        <td>{{ $facility->facility->category->category_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            
            @if(count($equipment) > 0)
            <h3>Requested Equipment</h3>
            <table>
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipment as $item)
                    <tr>
                        <td>{{ $item->equipment->equipment_name }}</td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            
            <p>You can check the status of your requisition using your access code: <strong>{{ $requisition->access_code }}</strong></p>
            
            <p>We will notify you once your requisition has been processed.</p>
        </div>
        
        <div class="footer">
            <p>If you have any questions, please contact our support team.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>