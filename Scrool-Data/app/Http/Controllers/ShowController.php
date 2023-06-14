<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImagesCompany;


class ShowController extends Controller
{
    public function showAllImages()
    {
        $images = ImagesCompany::all();
    
        return response()->json(['gambar' => $images],200);
    }
    
    
}
