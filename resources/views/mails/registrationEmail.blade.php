<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your OTP for Verification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }

        table {
            border-spacing: 0;
            width: 100%;
        }

        td {
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #4CAF50;
            padding: 20px;
            color: #ffffff;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }

        .otp-box {
            background-color: #f4f4f7;
            padding: 15px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            color: #4CAF50;
            text-align: center;
            letter-spacing: 2px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #888888;
            background-color: #f4f4f7;
            border-top: 1px solid #dddddd;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            border-radius: 6px;
            font-weight: bold;
        }

        @media screen and (max-width: 600px) {
            .content {
                padding: 15px;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <table role="presentation" class="container">
        <tr>
            <td>
                <div class="header">
                    Cooperative
                </div>
                <div class="content">
                    <p>Hi <strong>{{ $data->name }}</strong>,</p>
                    <p>Thank you for signing up with <strong>Cooperative</strong>! To complete your
                        registration, please use the OTP below:</p>
                    <div class="otp-box">
                        {{ $data->otp_number }}
                    </div>
                    <p>This OTP is valid for <strong>5 minuite</strong> minutes. Please do not share it with anyone.</p>
                    <p>If you didn't request this, please ignore this email or contact our support team immediately.</p>

                </div>
                {{-- <div class="footer">
                    &copy; {{ CURRENT_YEAR }} {{ APP_NAME }}. All rights reserved.
                </div> --}}
            </td>
        </tr>
    </table>

</body>

</html>
