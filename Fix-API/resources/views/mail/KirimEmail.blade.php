
<h1>Assalamualaikum {{$user->name}}</h1>
<br>
<h2> OTP: {{ $otp }}</h2>
@php
    $now = now();
    $otpExpired = \Carbon\Carbon::parse($user->otp_expired);
    $remainingTime = $now->diffInSeconds($otpExpired);
    $minutes = floor($remainingTime / 60);
    $seconds = $remainingTime % 60;
@endphp

<h3>Expired OTP:{{ $minutes }} minute {{ $seconds }} second </h3>
<p>Gunakan kode OTP ini untuk Memverifikasi akun kamu dan <br> melanjutkan menggunakan aplikasi kami </p>



