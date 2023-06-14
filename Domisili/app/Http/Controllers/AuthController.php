<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registrasi(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:8|confirmed'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors());
        // }

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'nomor_hp' => $request->nomor_hp,
        //     'password' => bcrypt($request->password),
        //     'role' => $request->nomor_hp ? 'admin' : 'user', 
        // ]);


        // $token = $user->createToken('auth_token')->plainTextToken;

        // return response()
        //     ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer',]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'province_id' => 'required|exists:provinces,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'password' => bcrypt($request->password),
            'role' => $request->nomor_hp ? 'admin' : 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json([
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
    }





    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()
                ->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
    }



    // public function logout(Request $request)
    // {
    //     $request->user()->token()->revoke();
    //     return response()->json(["status" => "Logged Out"],200);
    // }


    public function logout(Request $request)
    {
        auth('sanctum')->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
