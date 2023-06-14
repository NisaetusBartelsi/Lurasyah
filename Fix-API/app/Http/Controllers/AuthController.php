<?php

namespace App\Http\Controllers;

use App\Mail\KirimEmail;
use App\Models\Company;
use App\Models\ImagesCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function registrasi_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'provinsi' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'desa' => $request->desa,
        ]);

        $otp = mt_rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expired = now()->addMinutes(3);
        $user->save();
        session(['registrasi_email' => $user->email]);
        $email = new KirimEmail($otp, $user);
        Mail::to($user->email)->send($email);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer',]);
    }










    public function verifikasi_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user && $user->otp_code == $request->input('otp')) {
            if ($user->otp_expired && now()->lt($user->otp_expired)) {
                $user->email_verified_at = now();
                $user->save();

                return response()->json(['message' => 'Verifikasi email berhasil Masuk Sirsak jan diluar']);
            } else {
                return response()->json(['message' => 'Kode OTP sudah kedaluwarsa']);
            }
        } else {
            return response()->json(['message' => 'Kode OTP tidak valid']);
        }
    }




    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect('/auth/google/callback');
    }

    public function callback()
    {
        $user = Socialite::driver('google')->stateless()->user();

        return view('home')->with(['name' => $user->name, 'email' => $user->email]);
    }





    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email Atau Password Salah'], 401);
        }

        $user_verification = User::where('email', $request->email)->whereNull('email_verified_at')->first();

        if ($user_verification) {
            return response()->json(['message' => 'Email ini belum diverifikasi'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
    }




    public function ChangeRole(Request $request, $id)
    {
        $user = User::find($id);
        $triger = $user->triger = true;

        if ($triger == true) {
            // Ubah peran pengguna (contoh: dari 'Pengguna' menjadi 'Admin')
            $role =  $user->role = 'Admin';
            $user->save();

            return response()->json(['role' => $role]);
        } else {
            return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
        }
    }













    public function logout(Request $request)
    {
        $user = auth('sanctum')->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out', 'user' => $user], 200);
    }
}
