<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function store(Request $request){
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $lokasi = $request->lokasi;
        $image = $request->image; // Data base64 dari request

        // Memastikan bahwa ada gambar yang terkirim
        if($image) {
            $folderPath = "public/upload/absensi/";
            $formatName = $nik."-".$tgl_presensi;

            // Memecah data base64 menjadi dua bagian
            $image_parts = explode(";base64,", $image);
            if (count($image_parts) === 2) {
                // Mendecode data base64
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = $formatName . ".png"; // Pastikan ada titik sebelum png
                $file = $folderPath . $fileName;

                // Simpan file ke storage
                Storage::put($file, $image_base64);

                echo "Image uploaded successfully";
            } else {
                echo "Invalid image data";
            }
        } else {
            echo "No image found";
        }
    }
}
