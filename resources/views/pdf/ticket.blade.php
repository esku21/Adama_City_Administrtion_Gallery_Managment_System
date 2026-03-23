<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Entry Ticket - {{ $booking->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .ticket-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            background-color: #ffffff;
        }
        .header {
            background-color: #4f46e5;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            padding: 30px;
        }
        .qr-section {
            text-align: center;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .qr-code {
            width: 180px;
            height: 180px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .label {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .value {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
        }
        .footer {
            padding: 20px;
            background-color: #f1f5f9;
            text-align: center;
            font-size: 10px;
            color: #64748b;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #fef3c7;
            color: #b45309;
        }
    </style>
</head>
<body>
    @php
        // Mapping Slot IDs to readable times
        $slotMapping = [
            'm1' => 'Morning Slot 1 (3:00 - 3:30)',
            'm2' => 'Morning Slot 2 (4:00 - 4:30)',
            'm3' => 'Morning Slot 3 (5:00 - 5:30)',
            'a1' => 'Afternoon Slot 1 (8:00 - 8:30)',
            'a2' => 'Afternoon Slot 2 (9:00 - 9:30)',
            'a3' => 'Afternoon Slot 3 (10:00 - 10:30)',
        ];
        $displayTime = $slotMapping[$booking->slot_id] ?? $booking->slot_id;
    @endphp

    <div class="ticket-container">
        <div class="header">
            <h1>Visitor Pass</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Booking ID: #{{ $booking->id }}</p>
        </div>

        <div class="content">
            <div class="qr-section">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
                <p style="font-size: 12px; margin-top: 10px; color: #64748b;">Scan at the entrance gate</p>
            </div>

            <table class="info-table">
                <tr>
                    <td width="50%">
                        <div class="label">Visitor Name</div>
                        <div class="value">{{ $booking->visitor_name }}</div>
                    </td>
                    <td width="50%">
                        <div class="label">Date of Visit</div>
                        <div class="value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="label">Visitor Category</div>
                        <div class="value">{{ $booking->visitor_category }} ({{ $booking->visitor_type }})</div>
                    </td>
                    <td>
                        <div class="label">Group Size</div>
                        <div class="value">{{ $booking->number_of_visitors }} Person(s)</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="label">Permitted Halls</div>
                        <div class="value">
                            @if($booking->halls && $booking->halls->count() > 0)
                                {{ $booking->halls->pluck('name')->implode(', ') }}
                            @else
                                General Access
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="label">Entry Status</div>
                        <div class="status-badge">{{ strtoupper($booking->status) }}</div>
                    </td>
                    <td>
                        <div class="label">Assigned Time Slot</div>
                        <div class="value" style="color: #4f46e5;">{{ $displayTime }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Generated on {{ date('Y-m-d H:i:s') }} (EAT)<br>
            Please bring a valid ID along with this ticket.
        </div>
    </div>
</body>
</html>