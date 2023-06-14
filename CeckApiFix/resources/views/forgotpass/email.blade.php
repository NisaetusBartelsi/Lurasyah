<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>

    <p>Hello {{$name}}</p>

    <p>You are receiving this email because we received a password reset request for your account.</p>

    <p>Click the button below to reset your password:</p>

    <p>
        <a href="{{ $resetUrl }}" target="_blank">Reset Password</a>
    </p>

    <p>If you did not request a password reset, no further action is required.</p>

    <p>Thank you!</p>
</body>
</html>
