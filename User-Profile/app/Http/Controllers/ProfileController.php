<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{



    public function edit($id)
    {
        $user = User::find($id);
        $images = $user->images;
        return response()->json(['Profile' => $images]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $images = $user->images;
        $file = $request->file('images');

        if ($file) {
            CloudinaryStorage::delete($images);
            $result = CloudinaryStorage::upload($file->getRealPath(), $file->getClientOriginalName());
            $user->update(['images' => $result]);
        }

        $profile = $user->images;
        return response()->json([
            'message' => 'Gambar profil berhasil diupdate',
            'image_url' => $profile,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $user = User::find($id);
        CloudinaryStorage::delete($user->images);
        $user->delete();
        return response()->json(['message' => 'berhasil dihapus']);
    }
}
