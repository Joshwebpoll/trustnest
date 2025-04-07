<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Loan Application Confirmation</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table width="100%" bgcolor="#f4f4f4" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                    style="border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td bgcolor="#4A90E2" style="padding: 20px 30px; color: #ffffff; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px;">Loan Application Received</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px;">Hello <strong>{{ $getUser->name }}</strong>,</p>

                            <p style="font-size: 15px; color: #333;">Thank you for applying for a loan with us. Your
                                application has been received and is currently being reviewed by our team.</p>

                            <!-- Loan Summary Card -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin: 20px 0; border: 1px solid #ddd; border-radius: 6px;">
                                <tr>
                                    <td colspan="2"
                                        style="background-color: #f9f9f9; padding: 12px 20px; font-weight: bold; border-bottom: 1px solid #ddd;">
                                        Loan Application Summary
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 12px 20px;">Amount Requested:</td>
                                    <td style="padding: 12px 20px;">₦{{ number_format($loan->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px;">Duration:</td>
                                    <td style="padding: 12px 20px;">{{ $loan->duration_months }} months</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px;">Total Repayment:</td>
                                    <td style="padding: 12px 20px;">₦{{ number_format($loan->total_payable) }} </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px;">Start Date:</td>
                                    <td style="padding: 12px 20px;">
                                        {{ \Carbon\Carbon::parse($loan->start_date)->format('d M Y') }} </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px;">End Date:</td>
                                    <td style="padding: 12px 20px;">
                                        {{ \Carbon\Carbon::parse($loan->end_date)->format('d M Y') }} </td>
                                </tr>

                            </table>

                            <p style="font-size: 15px; color: #333;">We will notify you once your application has been
                                processed. If you have any questions, please don't hesitate to contact our support team.
                            </p>

                            <p style="margin-top: 30px; font-size: 14px; color: #666;">Thank you,<br><strong>Your Loan
                                    Team</strong></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td bgcolor="#f0f0f0" style="padding: 20px; text-align: center; font-size: 12px; color: #999;">
                            © {{ date('Y') }} Your Company Name. All rights reserved.<br>
                            Need help? <a href="mailto:support@example.com" style="color: #4A90E2;">Contact Support</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
