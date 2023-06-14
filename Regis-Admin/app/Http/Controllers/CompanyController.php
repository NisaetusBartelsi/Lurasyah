<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ImagesCompany;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function CompanyImages(Request $request, $id)
    {
        $company = Company::find($id);
        $company_id = $company->id;
        $images = ImagesCompany::where('company_id', $company_id)->get();

        // Menghapus gambar-gambar terkait sebelum unggahan baru
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
            'image_url' => $profile,
            'gambar-kafe' => $updatedImages,
        ]);
    }

    public function deleteCompanyImages(Request $request, $id)
    {

        $image = ImagesCompany::find($id);

        if (!$image) {
            return response()->json([
                'message' => 'Gambar tidak ditemukan',
            ], 404);
        }

        CloudinaryStorage2::delete($image->images);
        $image->delete();

        DB::statement('SET @i = 0');
        DB::table('images_companies')
            ->orderBy('id')
            ->update(['id' => DB::raw('(@i:=@i+1)')]);


        return response()->json([
            'message' => $image,
        ]);
    }

    public function addcomment(Request $request, $id)
    {

        $user = User::find($id);
        $user_id = $user->id;
        DB::table('comments')
            ->insert([
                'comment' => $request->comment,
                'images_companies_id' => $request->images_companies_id,
                'user_id' => $user_id,
            ]);

        $count = DB::table('comments')->count();
        $comments = DB::table('comments')->get();
        return response()->json(['comments' => $comments]);
    }

    public function addlike(Request $request, $id)
    {
        $user = User::find($id);
        $user_id = $user->id;
        $image = ImagesCompany::find($request->images_companies_id);
        $image_id = $image->id;

        $existingLike = DB::table('likes')
            ->where('user_id', $user_id)
            ->where('images_companies_id', $image_id)
            ->first();

        if ($existingLike) {
            if ($existingLike->like == false) {
                DB::table('likes')
                    ->where('user_id', $user_id)
                    ->where('images_companies_id', $image_id)
                    ->update(['like' => true]);

                $message = 'Liked successfully.';
            } else {
                DB::table('likes')
                    ->where('user_id', $user_id)
                    ->where('images_companies_id', $image_id)
                    ->update(['like' => false]);

                $message = 'Like removed successfully.';
            }
        } else {
            DB::table('likes')
                ->insert([
                    'user_id' => $user_id,
                    'images_companies_id' => $image_id,
                    'like' => true,
                ]);

            $message = 'Liked successfully.';
        }

        $totalLikes = DB::table('likes')
            ->where('images_companies_id', $image_id)
            ->where('like', true)
            ->count();

        return response()->json([
            'message' => $message,
            'total_likes' => $totalLikes,
        ]);
    }

    public function Mix(Request $request, $id)
    {
        // Validasi permintaan
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
                'CompanyProvince' => $request->CompanyProvince,
                'CompanyRegency' => $request->CompanyRegency,
                'CompanyDistrict' => $request->CompanyDistrict,
                'CompanyName' => $request->CompanyName,
                'CompanyAddres' => $request->CompanyAddres,
                'CompanyType' => $request->CompanyType,
                'CompanyVillage' => $request->CompanyVillage,
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
}
