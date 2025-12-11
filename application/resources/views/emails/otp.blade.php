<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(90deg, #003092, #0051c4);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 50px auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .header {
            background: linear-gradient(90deg, #003092, #0051c4);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
            color: #333333;
        }
        .content p {
            font-size: 16px;
            margin: 15px 0;
        }
        .otp-code {
            display: inline-block;
            background-color: #f0f4ff;
            color: #003092;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 6px;
            padding: 20px 30px;
            border-radius: 10px;
            margin: 30px 0;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #666666;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .note {
            font-size: 13px;
            color: #999999;
            margin-top: 10px;
        }
        .btnVerify {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(90deg, #003092, #0051c4);
            color: #ffffff !important;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 25px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your One-Time Password</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Please use the OTP code below to verify your identity:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>This code is valid for 5 minutes only.</p>
            <a href="{{ route('subscriber.otp') }}" class="btnVerify">Verify Now</a>
            <p class="note">* Please do not share this code with anyone for security reasons.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Digital Bangladesh Legal Decisions. All rights reserved.
        </div>
    </div>
</body>
</html>
