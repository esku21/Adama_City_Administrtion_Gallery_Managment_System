<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Credentials</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        }
        .container { 
            width: 100%; 
            max-width: 600px; 
            margin: 20px auto; 
            border: 1px solid #e2e8f0; 
            padding: 0; 
            border-radius: 10px; 
            overflow: hidden;
        }
        .header { 
            background: #1e293b; 
            color: #ffffff; 
            padding: 30px; 
            text-align: center; 
        }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; opacity: 0.8; }
        .content { padding: 30px; background: #ffffff; }
        .content h2 { color: #1e293b; margin-top: 0; }
        .credentials { 
            background: #f8fafc; 
            border: 1px dashed #cbd5e1; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px;
        }
        .credentials p { margin: 8px 0; font-size: 15px; }
        .password-box { 
            color: #e11d48; 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 20px; 
            font-weight: bold; 
            letter-spacing: 1px;
        }
        .button-wrapper { text-align: center; margin-top: 30px; }
        .button { 
            display: inline-block; 
            padding: 14px 30px; 
            background: #2563eb; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: bold; 
        }
        .footer { 
            font-size: 12px; 
            color: #64748b; 
            text-align: center; 
            padding: 20px; 
            background: #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ACAGMS</h1>
            <p>Adama City Gallery Management System</p>
        </div>
        <div class="content">
            <h2>Hello, {{ $guide->name }}!</h2>
            <p>Identity verified successfully. Your account as a <strong>Guide</strong> is now fully active. You can use the credentials below to access the staff portal.</p>
            
            <div class="credentials">
                <p><strong>Login URL:</strong> {{ route('guide.login') }}</p>
                <p><strong>Email Address:</strong> {{ $guide->email }}</p>
                <p><strong>Your New Password:</strong> <span class="password-box">{{ $password }}</span></p>
            </div>

            <p style="color: #64748b; font-size: 14px;"><em>Note: For security reasons, please change this temporary password as soon as you log in via the Account Settings page.</em></p>
            
            <div class="button-wrapper">
                <a href="{{ route('guide.login') }}" class="button">Login to Staff Dashboard</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Adama City Administration. All rights reserved.</p>
            <p>This is an automated security email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>