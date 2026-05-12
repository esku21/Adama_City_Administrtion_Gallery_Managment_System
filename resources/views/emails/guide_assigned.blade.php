<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px; }
        .header { background: #1e293b; color: #ffffff; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .credentials { background: #f8fafc; border: 1px dashed #cbd5e1; padding: 15px; margin: 20px 0; }
        .button { display: inline-block; padding: 12px 25px; background: #2563eb; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { font-size: 12px; color: #64748b; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ACAGMS</h1>
            <p>Adama City Administration Gallery Management System</p>
        </div>
        <div class="content">
            <h2>Hello, {{ $guide->name }}!</h2>
            <p>You have been officially registered as a <strong>Guide</strong> for the Adama City Gallery. You can now log in to the portal to manage bookings and scan visitor tickets.</p>
            
            <div class="credentials">
                <p><strong>Login URL:</strong> {{ route('guide.login') }}</p>
                <p><strong>Email:</strong> {{ $guide->email }}</p>
                <p><strong>Temporary Password:</strong> <span style="color: #e11d48; font-family: monospace; font-size: 18px;">{{ $password }}</span></p>
            </div>

            <p>For security reasons, please change your password immediately after your first login.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('guide.login') }}" class="button">Login to Dashboard</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Adama City Administration. All rights reserved.</p>
        </div>
    </div>
</body>
</html>