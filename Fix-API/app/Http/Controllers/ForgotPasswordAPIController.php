<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ResetLinkEmail;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\View;

class ForgotPasswordAPIController extends Controller
{

    public function SendGmailUser(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email tidak ditemukan'], 404);
        }

        if (is_null($user->email_verified_at)) {
            return response()->json(['error' => 'Email tidak valid atau belum diverifikasi'], 400);
        }

        $resetLink = 'https://www.google.co.id';

        Mail::to($user->email)->send(new ResetLinkEmail($user, $resetLink));

        return response()->json(['message' => 'Tautan pengaturan ulang kata sandi telah dikirim'], 200);
    }


    public function ChangePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password berhasil direset' . 'Silahkan login kembali'], 200);
        } else {
            return response()->json(['error' => 'Gagal mereset password'], 400);
        }
    }







    public function GetLinkToken(Request $request, $id)
    {
        $user = User::find($id);
        $token = Password::getRepository()->create($user);
        return response()->json(['token' => $token], 200);
    }
}
