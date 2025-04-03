<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333;
            line-height: 1.5;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin-top: 10px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Your OTP Code</h2>
        <p>Hello, {{ $data->name }}</p>
        <p>Use the OTP code below to resent your password:</p>
        <p class="otp-code">{{ $data->reset_password }}</p>
        <p>This code will expire in 10 minutes. If you did not request this, please ignore this email.</p>
        <div class="footer">
            <p>Thank you,<br>Your App Team</p>
        </div>
    </div>
</body>

</html>
