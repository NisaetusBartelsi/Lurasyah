<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .button {
            display: inline-block;
            background-color:black;
            border: none;
            color: white;
            text-align: center;
            font-size: 16px;
            padding: 10px 20px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>Reset Password</h2>
    <p>Halo {{ $user->name }},</p>
    <p>Klik tombol di bawah ini untuk mereset password Anda:</p>
    <div>
        <a href="{{ $resetLink }}" class="button">Reset Password</a>
    </div>
    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
</body>
</html>
