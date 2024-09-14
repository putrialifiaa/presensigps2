<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function create()
    {
        return view('presensi.create');
    }

    public function store(Request $request){
        $lokasi = $request->lokasi;
        $image = $request->image;
    }
}
