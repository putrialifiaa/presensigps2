<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    $hariini = date("Y-m-d");
    $bulanini = date("m") * 1; // Menyimpan bulan ini
    $tahunini = date("Y"); // Menyimpan tahun ini
    $nik = Auth::guard('karyawan')->user()->nik;

    // Query untuk presensi hari ini
    $presensihariini = DB::table('presensi')
    ->where('nik', $nik) // Pastikan user yang login diambil dengan NIK yang benar
    ->where('tgl_presensi', $hariini)
    ->first();

    // Query untuk histori bulan ini
    $historibulanini = DB::table('presensi')
        ->select('presensi.*', 'keterangan', 'jam_kerja.*', 'doc_sid', 'nama_cuti')
        ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
        ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
         ->where('presensi.nik', $nik)
        ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
        ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
        ->orderBy('tgl_presensi')
        ->get();

    $rekappresensi = DB::table('presensi')
    ->selectRaw('COUNT(nik) as jumlah_hadir, SUM(IF(jam_in > jam_masuk ,1,0)) as jumlah_terlambat')
    ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=','jam_kerja.kode_jam_kerja')
    ->where('nik', $nik)
        ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
        ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
    ->first();

    $leaderboard = DB::table('presensi')
    ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
    ->where('tgl_presensi', $hariini)
    ->orderBy('jam_in')
    ->get();
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September",
        "Oktober", "November", "Desember"];

    $rekapizin = DB::table('pengajuan_izin')
    ->selectRaw('SUM(IF(status="i", 1, 0)) as jmlizin, SUM(IF(status="s", 1, 0)) as jmlsakit')
    ->where('nik', $nik)
    ->whereRaw('MONTH(tgl_izin_dari) = ?', [$bulanini])
        ->whereRaw('YEAR(tgl_izin_dari) = ?', [$tahunini])
    ->where('status_approved', 1)
    ->first();

    // Menambahkan $bulanini ke dalam compact
    return view('dashboard.dashboard', compact('presensihariini', 'historibulanini',
    'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'leaderboard', 'rekapizin'));
}

    public function dashboardadmin(){
        $hariini = date("Y-m-d");
        $rekappresensi = DB::table('presensi')
    ->selectRaw('COUNT(nik) as jumlah_hadir, SUM(IF(jam_in >"07:30",1,0)) as jumlah_terlambat')
    ->where('tgl_presensi', $hariini)
    ->first();

    $rekapizin = DB::table('pengajuan_izin')
    ->selectRaw('SUM(IF(status="i", 1, 0)) as jmlizin, SUM(IF(status="s", 1, 0)) as jmlsakit')
    ->where('status_approved', 1)
    ->where('tgl_izin_dari', $hariini)
    ->first();
        return view('dashboard.dashboardadmin', compact('rekappresensi', 'rekapizin'));
    }
}
