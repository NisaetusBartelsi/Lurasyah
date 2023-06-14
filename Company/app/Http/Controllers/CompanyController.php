<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ImagesCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $result = CloudinaryStorage::upload($file->getRealPath(), $file->getClientOriginalName());
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

        CloudinaryStorage::delete($image->images);
        $image->delete();

        DB::statement('SET @i = 0');
        DB::table('images_companies')
            ->orderBy('id')
            ->update(['id' => DB::raw('(@i:=@i+1)')]);


        return response()->json([
            'message' => $image,
        ]);
    }
}
