<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Entry Ticket - {{ $booking->id }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #334155;
            margin: 0;
            padding: 40px;
            background-color: #f8fafc;
        }
        .ticket-container {
            width: 100%;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
        }
        .header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 20px 30px;
            text-align: left;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-container {
            width: 70px;
            vertical-align: middle;
        }
        .logo-img {
            width: 60px;
            height: auto;
        }
        .header-text {
            vertical-align: middle;
            padding-left: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 11px;
            opacity: 0.9;
        }
        .content { padding: 30px; }
        .qr-section {
            text-align: center;
            padding: 15px;
            background-color: #f8fafc;
            margin-bottom: 20px;
            border-radius: 15px;
            border: 1px dashed #e2e8f0;
        }
        .qr-code { 
            width: 140px; 
            height: 140px; 
            display: block; 
            margin: 0 auto; 
        }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 12px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .label { font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .value { font-size: 12px; font-weight: bold; color: #1e293b; }
        .footer { padding: 15px; background-color: #f8fafc; text-align: center; font-size: 9px; color: #64748b; border-top: 1px solid #f1f5f9; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; background-color: #fef3c7; color: #b45309; }
        .status-approved { background-color: #dcfce7; color: #15803d; }
    </style>
</head>
<body>
    @php
        // 1. Convert Logo to Base64 (Using public_path directly for stability)
        $logoPath = public_path('storage/images/adama.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    @endphp

    <div class="ticket-container">
        <div class="header">
            <table class="header-table">
                <tr>
                    @if($logoBase64)
                    <td class="logo-container">
                        <img src="{{ $logoBase64 }}" class="logo-img">
                    </td>
                    @endif
                    <td class="header-text">
                        <h1>Adama City Administration</h1>
                        <p>Gallery Management System | Ref: #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <div class="qr-section">
                {{-- Ensure the controller sends $qrCode as a PNG base64 string --}}
                <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code">
                <p style="font-size: 10px; margin-top: 8px; color: #4f46e5; font-weight: bold;">
                    TOKEN: {{ $booking->qr_token }}
                </p>
            </div>

            <table class="info-table">
                <tr>
                    <td width="50%">
                        <div class="label">Visitor</div>
                        <div class="value">{{ $booking->visitor_name }}</div>
                    </td>
                    <td width="50%">
                        <div class="label">Visit Date</div>
                        <div class="value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('D, M d, Y') }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="label">Category</div>
                        <div class="value">{{ $booking->visitor_category }} ({{ $booking->visitor_type }})</div>
                    </td>
                    <td>
                        <div class="label">Group Size</div>
                        <div class="value">{{ $booking->number_of_visitors }} Person(s)</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="label">Access Granted For</div>
                        <div class="value" style="color: #4f46e5;">
                            {{-- Corrected to use the single 'hall' relationship --}}
                            {{ $booking->hall ? $booking->hall->name : 'General Access' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="label">Pass Status</div>
                        <div class="status-badge {{ $booking->status === 'approved' ? 'status-approved' : '' }}">
                            {{ strtoupper($booking->status) }}
                        </div>
                    </td>
                    <td>
                        <div class="label">Visit Time</div>
                        {{-- Uses the readable_slot attribute defined in your Booking model --}}
                        <div class="value">{{ $booking->readable_slot }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <strong>OFFICIAL VISITOR PASS</strong><br>
            Please present this ticket at the gate with a valid ID.<br>
            <span style="color: #94a3b8;">Issued at: {{ date('Y-m-d H:i') }}</span>
        </div>
    </div>
</body>
</html>