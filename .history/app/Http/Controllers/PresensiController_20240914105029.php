<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        return view('presensi.create');
    }

    public function store(Request $request)
    {
        // Ambil data dari Auth
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $lokasi = $request->lokasi;
        $image = $request->image;
        $folderPath = "public/upload/absensi/";
        $formatName = $nik . "-" . $tgl_presensi;

        // Pisahkan base64 dan tipe data
        $image_parts = explode(";base_64,", $image);
        if (count($image_parts) == 2) {
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $formatName . ".png";
            $file = $folderPath . $fileName;

            // Buat folder jika belum ada
            if (!Storage::exists($folderPath)) {
                Storage::makeDirectory($folderPath);
            }

            // Simpan file
            Storage::put($file, $image_base64);
            return response()->json(['message' => 'Image uploaded successfully.'], 200);
        } else {
            return response()->json(['error' => 'Invalid image format'], 400);
        }
    }
}
