<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horus APP - OTP Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        table {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        strong {
            color: #F5903F;
        }
        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <img src="{{ asset('HoursLogo.jpeg') }}" alt="Horus" style="max-width: 150px; margin-bottom: 20px;">
                <h2>Horus APP</h2>
            </td>
        </tr>
        <tr>
            <td>
                <p>Dear User,</p>
                <p>Your OTP for verification is: <strong>{{ $otp }}</strong></p>
                <p>Please use this OTP to verify your email address.</p>
            </td>
        </tr>
        <tr>
            <td>
                <hr>
                <p class="footer">This email is auto-generated. Please do not reply.</p>
            </td>
        </tr>
    </table>
</body>
</html>