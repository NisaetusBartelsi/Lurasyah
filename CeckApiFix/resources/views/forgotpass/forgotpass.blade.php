<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password</title>
</head>
<body>
    <h1>Lupa Password</h1>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>
        <div>
            <button type="submit">Kirim Link Reset Password</button>
        </div>
    </form>
</body>
</html>
