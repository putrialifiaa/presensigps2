<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Setjamkerja;
use App\Models\Setjamkerjadept;


class KonfigurasiController extends Controller
{
    public function lokasikantor()
    {
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('konfigurasi.lokasikantor', compact('lok_kantor'));
    }

    public function updatelokasikantor(Request $request){
        $lokasi_kantor = $request->lokasi_kantor;
        $radius = $request->radius;

        $update = DB::table('konfigurasi_lokasi')->where('id', 1)->update([
            'lokasi_kantor' => $lokasi_kantor,
            'radius' => $radius
        ]);

        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil Diupdate']);
        }else{
            return Redirect::back()->with(['warning'=>'Data Gagal Diupdate']);
        }
    }

    public function jamkerja(){
        $jam_kerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        return view('konfigurasi.jamkerja', compact('jam_kerja'));
    }

    public function storejamkerja(Request $request) {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nama_jam_kerja = $request->nama_jam_kerja;
        $awal_jam_masuk = $request->awal_jam_masuk;
        $jam_masuk = $request->jam_masuk;
        $akhir_jam_masuk = $request->akhir_jam_masuk;
        $jam_pulang = $request->jam_pulang;

        $data = [
            'kode_jam_kerja' => $kode_jam_kerja,
            'nama_jam_kerja' => $nama_jam_kerja,
            'awal_jam_masuk' => $awal_jam_masuk,
            'jam_masuk' => $jam_masuk,
            'akhir_jam_masuk' => $akhir_jam_masuk,
            'jam_pulang' => $jam_pulang
        ];
        try {
            DB::table('jam_kerja')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function editjamkerja(Request $request){
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jamkerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();
        return view('konfigurasi.editjamkerja', compact('jamkerja'));
    }

    public function updatejamkerja(Request $request) {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nama_jam_kerja = $request->nama_jam_kerja;
        $awal_jam_masuk = $request->awal_jam_masuk;
        $jam_masuk = $request->jam_masuk;
        $akhir_jam_masuk = $request->akhir_jam_masuk;
        $jam_pulang = $request->jam_pulang;

        $data = [
            'kode_jam_kerja' => $kode_jam_kerja,
            'nama_jam_kerja' => $nama_jam_kerja,
            'awal_jam_masuk' => $awal_jam_masuk,
            'jam_masuk' => $jam_masuk,
            'akhir_jam_masuk' => $akhir_jam_masuk,
            'jam_pulang' => $jam_pulang
        ];
        try {
            DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function delete($kode_jam_kerja)
{
    $hapus = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->delete();
    if($hapus){
        return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
    } else {
        return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
    }
}

    public function setjamkerja($nik){
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $cekjamkerja = DB::table('konfigurasi_jamkerja')->where('nik', $nik)->count();
        //dd($cekjamkerja);
        if($cekjamkerja > 0){
            $setjamkerja = DB::table('konfigurasi_jamkerja')->where('nik', $nik)->get();
            return view('konfigurasi.editsetjamkerja', compact('karyawan', 'jamkerja', 'setjamkerja'));
     } else {
        return view('konfigurasi.setjamkerja', compact('karyawan', 'jamkerja'));
     }
        }

    public function storesetjamkerja(Request $request)
    {
        $nik = $request->nik;
        $hari = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        for($i = 0; $i < count($hari); $i++) {
            $data[] = [
                'nik' => $nik,
                'hari' => $hari[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i]
             ];
        }

        try {
            Setjamkerja::insert($data);
            return redirect('/karyawan')->with(['success'=>'Jam Kerja Berhasil Disimpan']);
        } catch (\Exception $e) {
            return redirect('/karyawan')->with(['warning'=>'Jam Kerja Gagal Disimpan']);

           // dd($e);
        }
    }

    public function updatesetjamkerja(Request $request)
    {
        $nik = $request->nik;
        $hari = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        for($i = 0; $i < count($hari); $i++) {
            $data[] = [
                'nik' => $nik,
                'hari' => $hari[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i]
             ];
        }

        DB::beginTransaction();
        try {
            DB::table('konfigurasi_jamkerja')->where('nik', $nik)->delete();
            Setjamkerja::insert($data);
            DB::commit();
            return redirect('/karyawan')->with(['success'=>'Jam Kerja Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/karyawan')->with(['warning'=>'Jam Kerja Gagal Diupdate']);

           // dd($e);
        }
    }

    public function jamkerjadept(Request $request) {
        // Ambil parameter pencarian
        $kode_cabang = $request->input('kode_cabang'); // Mengambil nilai dari parameter GET

        // Mulai query untuk mengambil data jamkerjadept
        $jamkerjadept = DB::table('konfigurasi_jk_dept')
            ->join('cabang', 'konfigurasi_jk_dept.kode_cabang', '=', 'cabang.kode_cabang')
            ->join('departemen', 'konfigurasi_jk_dept.kode_dept', '=', 'departemen.kode_dept')
            ->select('konfigurasi_jk_dept.*', 'cabang.nama_cabang', 'departemen.nama_dept');

        // Jika ada filter berdasarkan kode_cabang, tambahkan kondisinya
        if ($kode_cabang) {
            $jamkerjadept->where('konfigurasi_jk_dept.kode_cabang', $kode_cabang);
        }

        // Lakukan pagination untuk hasil query
        $jamkerjadept = $jamkerjadept->paginate(10);

        // Ambil semua cabang untuk dropdown filter
        $cabang_all = DB::table('cabang')->get();

        // Kembalikan data ke view
        return view('konfigurasi.jamkerjadept', compact('jamkerjadept', 'cabang_all'));
    }

    public function createjamkerjadept() {
        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $cabang = DB::table('cabang')->get();
        $departemen = DB::table('departemen')->get();
        return view('konfigurasi.createjamkerjadept', compact('jamkerja', 'cabang', 'departemen'));
    }

    public function storejamkerjadept(Request $request)
{
    $request->validate([
        'kode_cabang' => 'required|exists:cabang,kode_cabang',
        'kode_dept' => 'required|exists:departemen,kode_dept',
        'hari' => 'required|array',
        'hari.*' => 'required|string',
        'kode_jam_kerja' => 'required|array',
        'kode_jam_kerja.*' => 'required|exists:jam_kerja,kode_jam_kerja',
    ], [
        'kode_cabang.required' => 'Kode cabang wajib diisi.',
        'kode_cabang.exists' => 'Kode cabang tidak valid.',
        'kode_dept.required' => 'Kode departemen wajib diisi.',
        'kode_dept.exists' => 'Kode departemen tidak valid.',
        'hari.required' => 'Hari wajib diisi.',
        'hari.array' => 'Hari harus berupa array.',
        'hari.*.required' => 'Setiap hari wajib diisi.',
        'kode_jam_kerja.required' => 'Kode jam kerja wajib diisi.',
        'kode_jam_kerja.array' => 'Kode jam kerja harus berupa array.',
        'kode_jam_kerja.*.exists' => 'Kode jam kerja tidak valid.',
    ]);

    $kode_cabang = $request->kode_cabang;
    $kode_dept = $request->kode_dept;
    $hari = $request->hari;
    $kode_jam_kerja = $request->kode_jam_kerja;
    $kode_jk_dept = "J" . $kode_cabang . $kode_dept;

    DB::beginTransaction();
    try {
        // Simpan data ke tabel konfigurasi_jk_dept
        DB::table('konfigurasi_jk_dept')->insert([
            'kode_jk_dept' => $kode_jk_dept,
            'kode_cabang' => $kode_cabang,
            'kode_dept' => $kode_dept
        ]);

        // Simpan detail jam kerja per hari
        $data = [];
        for ($i = 0; $i < count($hari); $i++) {
            $data[] = [
                'kode_jk_dept' => $kode_jk_dept,
                'hari' => $hari[$i],
                'kode_jam_kerja' => $kode_jam_kerja[$i]
            ];
        }
        Setjamkerjadept::insert($data);

        DB::commit();
        return redirect('konfigurasi/jamkerjadept')->with(['success' => 'Data Berhasil Disimpan']);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect('konfigurasi/jamkerjadept')->with([
            'warning' => 'Data Gagal Disimpan. Error: ' . $e->getMessage()
        ]);
    }
}

    public function editjamkerjadept($kode_jk_dept){

        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $cabang = DB::table('cabang')->get();
        $departemen = DB::table('departemen')->get();
        $jamkerjadept = DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->first();
        $jamkerjadept_detail = DB::table('konfigurasi_jk_dept_detail')->where('kode_jk_dept', $kode_jk_dept)->get();
        return view('konfigurasi.editjamkerjadept', compact('jamkerja', 'cabang', 'departemen', 'jamkerjadept', 'jamkerjadept_detail'));
    }

    public function updatejamkerjadept($kode_jk_dept, Request $request)
    {
        $hari = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        DB::beginTransaction();
        try {

            //Hapus Data Jan Kerja Sebelumnya
            DB::table('konfigurasi_jk_dept_detail')->where('kode_jk_dept', $kode_jk_dept)->delete();
            for($i = 0; $i < count($hari); $i++) {
                $data[] = [
                    'kode_jk_dept' => $kode_jk_dept,
                    'hari' => $hari[$i],
                    'kode_jam_kerja' => $kode_jam_kerja[$i]
                 ];
            }
            Setjamkerjadept::insert($data);
            DB::commit();
            return redirect('konfigurasi/jamkerjadept')->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('konfigurasi/jamkerjadept')->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function deletejamkerjadept($kode_jk_dept){
        try {
            DB::table('konfigurasi_jk_dept')->where('kode_jk_dept', $kode_jk_dept)->delete();
            return Redirect::back()->with([ 'success' => 'Data Berhasil Dihapus' ]);
        } catch (\Exception $e) {
            return Redirect::back()->with([ 'warning' => 'Data Gagal Dihapus' ]);

        }
    }
}
