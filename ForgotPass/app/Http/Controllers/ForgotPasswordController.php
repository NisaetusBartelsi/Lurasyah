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

class ForgotPasswordController extends Controller
{

    public function oi()
    {
        return view('forgotpass.forgotpass');
    }



    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        $verify = $user->email_verified_at;

        if (!$user) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        if (is_null($verify)) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        $resetLink = $this->generateResetLink($user);
        $token = Password::getRepository()->create($user);
        Mail::to($user->email)->send(new ResetLinkEmail($user, $resetLink,$token));

        return $this->sendResetLinkResponse();
    }



    protected function sendResetLinkResponse()
    {
        return response()->json(['message' => 'Tautan pengaturan ulang kata sandi telah dikirim'], 200);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['error' => 'Email anda salah atau belum diverifikasi'], 400);
    }


    private function generateResetLink($user)
    {
        $token = Password::getRepository()->create($user);

        return route('password.reset', ['token' => $token]);
    }



    public function reset(Request $request)
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
            return response()->json(['message' => 'Password berhasil direset'], 200);
        } else {
            return response()->json(['error' => 'Gagal mereset password'], 400);
        }
    }

    public function showResetForm(Request $request, $token)
    {
        return route('password.reset', ['token' => $token]);
    }
}
