<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Reset Password</title>
    <style>
        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .otp-code {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 15px 30px;
            background-color: #f3f4f6;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #2563eb;
            border-radius: 8px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
        </div>
        <p>Halo,</p>
        <p>Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP berikut untuk melanjutkan proses reset password:</p>
        
        <div class="otp-code">
            {{ $otp }}
        </div>
        
        <p>Kode ini berlaku selama <strong>10 menit</strong>. Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
        
        <p>Terima kasih,<br>Tim {{ config('app.name') }}</p>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
