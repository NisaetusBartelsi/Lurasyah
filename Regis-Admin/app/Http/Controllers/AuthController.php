<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer',]);
    }


    public function registrasi_admin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'CompanyName' => 'required|string|max:255',
            'CompanyAddres' => 'required|string',
            'CompanyProvince' => 'required|string',
            'CompanyRegency' => 'required|string',
            'CompanyDistrict' => 'required|string',
            'CompanyVillage' => 'required|string',
            'CompanyType' => [
                'required', 'string', 'max:255',
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

        $user = User::find($id);
        $user_id = $user->id;

        if (!$user) {
            return response()->json(['error' => 'User not found']);
        }
        $company = Company::create([
            'user_id' => $user_id,
            'CompanyProvince' => $request->CompanyProvince,
            'CompanyRegency' => $request->CompanyRegency,
            'CompanyDistrict' => $request->CompanyDistrict,
            'CompanyName' => $request->CompanyName,
            'CompanyAddres' => $request->CompanyAddres,
            'CompanyType' => $request->CompanyType,
            'CompanyVillage' => $request->CompanyVillage,
        ]);

        $user->role = $request->input('CompanyName', '') ? 'admin' : 'user';
        $user->save();

        return response()->json(['data' => $company, 'user' => $user]);
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
