<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>

    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required autofocus>
            @error('email')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            @error('password')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>

        <div>
            <button type="submit">Reset Password</button>
        </div>
    </form>
</body>
</html>
