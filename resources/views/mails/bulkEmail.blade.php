<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Email Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #1e40af;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }

        .footer {
            background-color: #f1f1f1;
            color: #888888;
            text-align: center;
            padding: 15px;
            font-size: 12px;
        }

        a.button {
            display: inline-block;
            background-color: #1e40af;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        @media only screen and (max-width: 600px) {
            .body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $details['subject'] }}</h1>
        </div>

        <div class="body">
            {{-- <p>Hi {{ $user->name }},</p> --}}

            <p>
                We're reaching out to share an important update or message with you.
            </p>

            <p>
                {{ $details['message'] }}
            </p>

            <a href="{{ $actionUrl ?? '#' }}" class="button">
                Take Action
            </a>

            <p style="margin-top: 30px;">
                If you have any questions, feel free to reply to this email.
            </p>

            <p>Thanks,<br />The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
