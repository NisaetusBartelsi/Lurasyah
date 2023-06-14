<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ImagesCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'CompanyName' => 'required|string|max:255|unique:companies',
            'phone_number' => 'required|string|max:13|unique:companies',
            'CompanyAddres' => 'required|string|unique:companies',
            'CompanyProvince' => 'required|string',
            'CompanyRegency' => 'required|string',
            'CompanyDistrict' => 'required|string',
            'lat' => 'required|string|unique:companies',
            'long' => 'required|string|unique:companies',
            'deskripsi' => 'required|string|unique:companies',
            'CompanyVillage' => 'required|string',
            'CompanyType' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    $allowed_types = ['kafe', 'masjid', 'atm', 'tempat wisata', 'minimarket'];
                    if (!in_array(strtolower($value), $allowed_types)) {
                        $fail("Kolom $attribute tidak valid.");
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Temukan user
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Pengguna tidak ditemukan']);
        }
        $user_id = $user->id;
        // Perbarui peran pengguna

        // Temukan atau buat perusahaan
        $company = Company::updateOrCreate(

            [
                'user_id' => $user_id,
                'phone_number' => $request->phone_number,
                'CompanyProvince' => $request->CompanyProvince,
                'CompanyRegency' => $request->CompanyRegency,
                'CompanyDistrict' => $request->CompanyDistrict,
                'CompanyName' => $request->CompanyName,
                'CompanyAddres' => $request->CompanyAddres,
                'CompanyType' => $request->CompanyType,
                'CompanyVillage' => $request->CompanyVillage,
                'lat' => $request->lat,
                'long' => $request->long,
                'deskripsi' => $request->deskripsi,
            ]
        );
        $company->save();
        $company_id = $company->id;
        $user->role = $request->input('CompanyName', '') ? 'admin' : 'user';
        $user->save();

        // Dapatkan gambar perusahaan yang sudah ada
        $images = ImagesCompany::where('company_id', $company_id)->get();

        // Hapus gambar terkait sebelum mengunggah yang baru
        foreach ($images as $image) {
            CloudinaryStorage::delete($image->images);
        }

        // ...

        $uploaded_images = array(); // array untuk menyimpan hasil unggahan

        for ($i = 1; $i <= 5; $i++) {
            $file = $request->file('images' . $i);

            if ($file) {
                $result = CloudinaryStorage2::upload($file->getRealPath(), $file->getClientOriginalName());
                $uploaded_images[] = $result; // menyimpan hasil unggahan ke dalam array
            }
        }

        // ...

        if (count($uploaded_images) > 0) {
            foreach ($uploaded_images as $image) {
                ImagesCompany::create([
                    'images' => $image,
                    'company_id' => $company_id
                ]);
            }
        }
        $updatedImages = ImagesCompany::where('company_id', $company_id)->get();

        $profile = array();
        foreach ($images as $img) {
            $profile[] = $img->images;
        }

        DB::statement('SET @i = 0');
        DB::table('images_companies')
            ->orderBy('id')
            ->update(['id' => DB::raw('(@i:=@i+1)')]);

        return response()->json([
            'message' => 'Gambar berhasil diunggah',
            'Company' => $company,
            'image_url' => $profile,
            'gambar-kafe' => $updatedImages,
            'pemilik' => $user,
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
        $user = auth('sanctum')->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out', 'user' => $user], 200);
    }
}
