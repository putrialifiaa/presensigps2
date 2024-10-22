<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CutiController extends Controller
{
    public function index(){
        $cuti = DB::table('master_cuti')->orderBy('kode_cuti','asc')->get();
        return view('cuti.index', compact('cuti'));
    }

    public function store(Request $request){
        $kode_cuti = $request->kode_cuti;
        $nama_cuti = $request->nama_cuti;
        $jml_hari = $request->jml_hari;

        $cekcuti = DB::table('master_cuti')->where('kode_cuti',$kode_cuti)->count();
        if($cekcuti > 0) {
            return Redirect::back()->with(['warning' => 'Data Kode Cuti Sudah Ada']);
        }

        try {
            DB::table('master_cuti')->insert([
                'kode_cuti' => $kode_cuti,
                'nama_cuti' => $nama_cuti,
                'jml_hari' => $jml_hari
            ]);
            return Redirect::back()->with(['success'=> 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning'=> 'Data Gagal Disimpan' . $e->getMessage()]);

            //throw $th;
        }
    }
}
