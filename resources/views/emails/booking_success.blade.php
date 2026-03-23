<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px; }
        .header { background-color: #1e40af; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .details { margin: 20px 0; }
        .qr-code { text-align: center; background: #f9f9f9; padding: 20px; border: 1px dashed #ccc; }
        .footer { font-size: 12px; text-align: center; color: #777; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>ADAMA CITY GALLERY</h2>
        </div>
        <p>Dear Visitor,</p>
        <p>Your booking for the Adama City Gallery has been successfully registered. Please find your details below:</p>
        
        <div class="details">
            <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
            <p><strong>Date:</strong> {{ $booking->booking_date }}</p>
            <p><strong>Time Slot:</strong> {{ $booking->slot_id }}</p>
            <p><strong>Category:</strong> {{ $booking->visitor_category }}</p>
            <p><strong>Halls:</strong> {{ $booking->halls->pluck('name')->implode(', ') }}</p>
        </div>

        <div class="qr-code">
            <p><strong>Your Entry QR Code:</strong></p>
            <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={{ $booking->qr_token }}&choe=UTF-8" alt="QR Code">
            <p><small>{{ $booking->qr_token }}</small></p>
        </div>

        <p>Please present this QR code at the entrance for verification.</p>
        
        <div class="footer">
            <p>This is an automated message from the Adama Smart City Office.</p>
        </div>
    </div>
</body>
</html>