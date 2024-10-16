<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index(Request $request) {
        $query = Karyawan::query();
        $query->select('karyawan.*', 'nama_dept');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->orderBy('nama_lengkap');

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        $karyawan = $query->paginate(perPage: 10);
        $departemen = DB::table('departemen')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('karyawan.index', compact('karyawan', 'departemen', 'cabang'));
    }

    public function store(Request $request) {

        // Validasi input
        $request->validate([
            'nik' => 'required|unique:karyawan,nik',
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
            'no_hp' => 'required',
            'kode_dept' => 'required',
            'foto' => 'nullable|image|max:2048', // opsional
        ]);

        // Ambil data dari request
        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $password = Hash::make('12345');
        $kode_cabang = $request->kode_cabang;

        // Mengatur nama file foto
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null; // Atau Anda bisa memberikan nilai default jika tidak ada foto
        }

        try {
            // Menyimpan data ke dalam model Karyawan
            $karyawan = new Karyawan();
            $karyawan->nik = $nik;
            $karyawan->nama_lengkap = $nama_lengkap;
            $karyawan->jabatan = $jabatan;
            $karyawan->no_hp = $no_hp;
            $karyawan->kode_dept = $kode_dept;
            $karyawan->foto = $foto;
            $karyawan->password = $password;
            $karyawan->kode_cabang = $kode_cabang;

            // Simpan data ke database
            if ($karyawan->save()) {
                // Simpan foto jika ada
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            }
        } catch (\Exception $e) {

            if($e->getCode()==23000){
                $message = "Data dengan NIK" . $nik ."Sudah Ada";
            }
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request) {
        $nik = $request->nik;
        $departemen = DB::table('departemen')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        return view('karyawan.edit', compact ('departemen', 'karyawan', 'cabang'));
    }

    public function update($nik, Request $request){
        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $password = Hash::make('12345');
        $kode_cabang = $request->kode_cabang;
        $old_foto = $request->old_foto;

        // Mengatur nama file foto
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            // Ambil data karyawan berdasarkan NIK
            $karyawan = Karyawan::where('nik', $nik)->first();

            if (!$karyawan) {
                return Redirect::back()->with(['warning' => 'Data Karyawan Tidak Ditemukan']);
            }

            $karyawan->nama_lengkap = $nama_lengkap;
            $karyawan->jabatan = $jabatan;
            $karyawan->no_hp = $no_hp;
            $karyawan->kode_dept = $kode_dept;
            $karyawan->foto = $foto;
            $karyawan->password = $password;
            $karyawan->kode_cabang = $kode_cabang;

            // Simpan data ke database
            if ($karyawan->save()) {
                // Simpan foto jika ada
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $folderPathOld = "public/uploads/karyawan/".$old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function delete($nik)
{
    $delete = DB::table('karyawan')->where('nik', $nik)->delete();
    if($delete){
        return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
    } else {
        return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
    }
}}
