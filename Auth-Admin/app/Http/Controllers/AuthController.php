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
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:15',
            'company_name' => 'required|string|max:255',
            'company_type' => ['required','string','max:255',
                function ($attribute, $value, $fail) {
                    $allowed_types = ['kafe', 'masjid', 'atm', 'tempat wisata', 'minimarket'];
                    if (!in_array(strtolower($value), $allowed_types)) {
                        $fail("The $attribute field is invalid.");
                    }
                },
            ],
        
        
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
    

$user = User::create([

    'phone_number' => $request->phone_number,
    'company_name' => $request->company_name,
    'company_type' => $request->company_type,
    'role' => $request->company_name ? 'admin' : 'user', 
]);


$token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json(['Data' => $user,'access_token' => $token, 'token_type' => 'Bearer']);
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
