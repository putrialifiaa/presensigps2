<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index(Request $request) {
        // Ambil semua data cabang untuk dropdown
        $cabang_all = DB::table('cabang')->orderBy('nama_cabang')->get();

        // Ambil parameter pencarian
        $kode_cabang = $request->kode_cabang;

        // Query data dengan filter (jika ada)
        $query = DB::table('cabang')->orderBy('kode_cabang');
        if (!empty($kode_cabang)) {
            $query->where('kode_cabang', $kode_cabang);
        }

        // Paginate hasilnya
        $cabang = $query->paginate(10);

        // Tambahkan query string agar pagination tetap membawa parameter pencarian
        $cabang->appends(['kode_cabang' => $kode_cabang]);

        return view('cabang.index', compact('cabang', 'cabang_all'));
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $nama_cabang = $request->nama_cabang;
        $lokasi_cabang =  $request->lokasi_cabang;
        $radius_cabang = $request->radius_cabang;

        try {
            $data = [
                'kode_cabang' => $kode_cabang,
                'nama_cabang' => $nama_cabang,
                'lokasi_cabang' => $lokasi_cabang,
                'radius_cabang' => $radius_cabang
            ];

            DB::table('cabang')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit(Request $request){
        $kode_cabang = $request->kode_cabang;
        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        return view('cabang.edit', compact('cabang'));
    }

    public function update(Request $request)
{
    $kode_cabang_lama = $request->kode_cabang_lama; // Kode lama
    $kode_cabang_baru = $request->kode_cabang;     // Kode baru
    $nama_cabang = $request->nama_cabang;
    $lokasi_cabang = $request->lokasi_cabang;
    $radius_cabang = $request->radius_cabang;

    try {
        // Cek apakah kode cabang baru sudah digunakan
        $exists = DB::table('cabang')
            ->where('kode_cabang', $kode_cabang_baru)
            ->where('kode_cabang', '!=', $kode_cabang_lama)
            ->exists();

        if ($exists) {
            return Redirect::back()->with(['warning' => 'Kode Cabang Baru Sudah Digunakan']);
        }

        // Update data
        $data = [
            'kode_cabang' => $kode_cabang_baru,
            'nama_cabang' => $nama_cabang,
            'lokasi_cabang' => $lokasi_cabang,
            'radius_cabang' => $radius_cabang,
        ];

        DB::table('cabang')
            ->where('kode_cabang', $kode_cabang_lama)
            ->update($data);

        return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
    } catch (\Exception $e) {
        return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
    }
}

    public function delete($kode_cabang){
        $hapus = DB::table('cabang')->where('kode_cabang',$kode_cabang)->delete();
        if($hapus){
            return Redirect::back()->with(['success'=>'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning'=>'Data Gagal Dihapus']);
        }
}
}
