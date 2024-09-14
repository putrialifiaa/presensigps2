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

    public function store(Request $request){
        // Mengambil data pengguna yang sedang login
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");

        // Mengambil data lokasi dan gambar dari request
        $lokasi = $request->lokasi;
        $image = $request->image;

        // Menyiapkan path folder untuk menyimpan gambar
        $folderPath = "public/upload/absensi/";
        $formatName = $nik . "-" . $tgl_presensi;

        // Memisahkan data gambar base64
        $image_parts = explode(";base64,", $image);

        // Pastikan base64 valid
        if(count($image_parts) > 1) {
            $image_base64 = base64_decode($image_parts[1]);
        } else {
            return response()->json(['error' => 'Invalid image data'], 400);
        }

        // Menambahkan titik sebelum ekstensi .png
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        // Menyimpan file gambar ke storage
        Storage::put($file, $image_base64);

        // Mengembalikan respons
        return response()->json(['success' => true, 'file' => $fileName], 200);
    }

}
