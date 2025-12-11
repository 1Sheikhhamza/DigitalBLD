<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>OCR Processing Result</title>
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
            color: #333333;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            margin: 15px 0;
        }
        .status {
            display: inline-block;
            padding: 15px 25px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 20px;
            font-weight: bold;
            color: white;
            background-color: {{ $success ? '#28a745' : '#dc3545' }};
        }
        .volume-info {
            margin-top: 10px;
            font-size: 14px;
            color: #666666;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #666666;
            padding: 20px;
            background-color: #f8f8f8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OCR Processing {{ $success ? 'Success' : 'Failure' }}</h1>
        </div>
        <div class="content">
            <div class="status">{{ $success ? 'Success' : 'Failed' }}</div>

            <p>
                @if ($success)
                    The OCR processing for your volume was completed successfully.
                @else
                    Unfortunately, the OCR processing encountered an error.
                @endif
            </p>

            <p><strong>Message:</strong></p>
            <p>
                @if ($success)
                    The OCR processing for your volume was completed successfully.
                @else
                    {{ $message }}
                @endif    
            
        </p>

            <p class="volume-info">Volume ID: {{ $volumeId }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Digital Bangladesh Legal Decisions. All rights reserved.
        </div>
    </div>
</body>
</html>
