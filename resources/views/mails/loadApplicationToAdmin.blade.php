@php
    use Illuminate\Support\Facades\Crypt;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Loan Application</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table width="100%" bgcolor="#f4f4f4" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                    style="border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td bgcolor="#D32F2F" style="padding: 20px 30px; color: #ffffff; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px;">New Loan Application</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px;">Hello Admin,</p>

                            <p style="font-size: 15px; color: #333;">A new loan application has been submitted by the
                                following customer:</p>

                            <!-- Customer Info -->
                            <h3 style="margin-top: 20px; font-size: 18px;">Customer Information</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Name:</strong></td>
                                    <td>{{ $getUser->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Email:</strong></td>
                                    <td>{{ $getUser->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;"><strong>Phone:</strong></td>
                                    <td>{{ $getUser->phone_number }}</td>
                                </tr>
                            </table>

                            <!-- Loan Details -->
                            <h3 style="margin-top: 20px; font-size: 18px;">Loan Details</h3>
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border: 1px solid #ddd; border-radius: 6px;">
                                {{-- <tr style="background-color: #f9f9f9;">
                                    <td style="padding: 12px 20px;"><strong>Loan Type</strong></td>
                                    <td style="padding: 12px 20px;">
                                        {{ $loanTypeNames[$loan->type] ?? ucfirst($loan->type) }}
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td style="padding: 12px 20px;">Amount</td>
                                    <td style="padding: 12px 20px;">
                                        ₦{{ number_format(Crypt::decryptString($loan->amount, 2)) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px;">Duration:</td>
                                    <td style="padding: 12px 20px;">{{ $loan->duration_months }} months</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 20px;">Total Repayment:</td>
                                    <td style="padding: 12px 20px;">
                                        ₦{{ number_format(Crypt::decryptString($loan->total_payable)) }} </td>
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

                            <p style="font-size: 15px; margin-top: 30px;">Please review this application in the admin
                                dashboard.</p>

                            <a href="{{ url('/admin/loans') }}"
                                style="display: inline-block; margin-top: 20px; background-color: #D32F2F; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;">
                                View Application
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td bgcolor="#f0f0f0" style="padding: 20px; text-align: center; font-size: 12px; color: #999;">
                            © {{ date('Y') }} Co-operative. Admin Notification Email.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
