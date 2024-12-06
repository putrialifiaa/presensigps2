<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DepartemenController extends Controller
{
    public function index(Request $request){
        $nama_dept = $request->nama_dept;
        $query = Departemen::query();
        $query->select('*');
        if(!empty($nama_dept)){
            $query->where('nama_dept', 'like', '%'.$nama_dept.'%');
        }
        $departemen = $query->get();
        //$departemen = DB::table('departemen')->orderBy('kode_dept')->get();
        return view('departemen.index', compact('departemen'));
    }

    public function store(Request $request){
        $kode_dept = $request->kode_dept;
        $nama_dept = $request->nama_dept;
        $data = [
            'kode_dept' => $kode_dept,
            'nama_dept' => $nama_dept
        ];

        $simpan = DB::table('departemen')->insert($data);
        if($simpan){
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit(Request $request){
        $kode_dept = $request->kode_dept;
        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        return view('departemen.edit', compact('departemen'));
    }

    public function update($kode_dept_lama, Request $request)
{
    $request->validate([
        'kode_dept' => 'required|max:255|unique:departemen,kode_dept,' . $kode_dept_lama . ',kode_dept',
        'nama_dept' => 'required|max:255',
    ]);

    $kode_dept_baru = $request->kode_dept;
    $nama_dept = $request->nama_dept;

    // Perbarui data di database
    $update = DB::table('departemen')
        ->where('kode_dept', $kode_dept_lama)
        ->update([
            'kode_dept' => $kode_dept_baru,
            'nama_dept' => $nama_dept,
        ]);

    if ($update) {
        return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
    } else {
        return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
    }
}

    public function delete($kode_dept){
        $hapus = DB::table('departemen')->where('kode_dept',$kode_dept)->delete();
        if($hapus){
            return Redirect::back()->with(['success'=>'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning'=>'Data Gagal Dihapus']);
        }
    }
}
