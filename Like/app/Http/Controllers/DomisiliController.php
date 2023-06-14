<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomisiliController extends Controller
{
    public function province()
    {
        $data = DB::table('provinces')->get();
        return response()->json(['provinsi' => $data], 200);
    }

    public function regency()
    {
        $data = DB::table('regencies')->get();
        return response()->json(['kabupaten' => $data], 200);
    }

    public function district()
    {
        $data = DB::table('districts')->get();
        return response()->json(['kecamatan' => $data], 200);
    }

    public function village()
    {
        $data = DB::table('villages')->get();
        return response()->json(['dusun/desa' => $data], 200);
    }
}
