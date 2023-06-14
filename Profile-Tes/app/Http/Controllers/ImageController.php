<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Image::get();
        return response()->json($images);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Create upload form']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $image  = $request->file('image');
        $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());
        Image::create(['image' => $result]);
        return response()->json(['message' => 'berhasil upload']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image, $id)
    {
        $image = Image::find($id);
        return response()->json(compact('image'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image, $id)
    {
        $image = Image::find($id);
        $file  = $request->file('image');
        $result = CloudinaryStorage::replace($image->image, $file->getRealPath(), $file->getClientOriginalName());
        $image->update(['image' => $result]);
        return response()->json(['message' => 'berhasil diupdate']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Image $image,$id)
    {
        $image = Image::find($id);
        CloudinaryStorage::delete($image->image);
        $image->delete();
        return response()->json(['message' => 'berhasil dihapus']);
    }
}
