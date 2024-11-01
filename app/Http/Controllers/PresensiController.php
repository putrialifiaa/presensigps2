<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Pengajuanizin;


class PresensiController extends Controller
{
    // Method untuk halaman create (presensi)
    public function gethari()
    {
        $hari = date("D");

        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

                case 'Mon':
                $hari_ini = "Senin";
                break;

                case 'Tue':
                $hari_ini = "Selasa";
                break;

                case 'Wed':
                $hari_ini = "Rabu";
                break;

                case 'Thu':
                $hari_ini = "Kamis";
                break;

                case 'Fri':
                $hari_ini = "Jumat";
                break;

                case 'Sat':
                $hari_ini = "Sabtu";
                break;

                default:
                $hari_ini = "Tidak Diketahui";
                break;
                }
                return $hari_ini;
    }

    public function create()
    {
        $hariini = date("Y-m-d");
        $namahari = $this->gethari();
        $nik = Auth::guard('karyawan')->user()->nik;
        $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nik', $nik)
            ->count();
        $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
        $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $jamkerja = DB::table('konfigurasi_jamkerja')
        ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->where('nik', $nik)->where('hari', $namahari)->first();

        if($jamkerja == null) {
            $jamkerja = DB::table('konfigurasi_jk_dept_detail')
            ->join('konfigurasi_jk_dept','konfigurasi_jk_dept_detail.kode_jk_dept','=','konfigurasi_jk_dept.kode_jk_dept')
            ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('kode_dept', $kode_dept)
            ->where('kode_cabang', $kode_cabang)
            ->where('hari', $namahari)->first();
        }

        if($jamkerja == null) {
                return view('presensi.notifjadwal');
            } else {
            return view('presensi.create', compact('cek', 'lok_kantor', 'jamkerja'));
        }
    }

    // Method untuk menyimpan data presensi (masuk & keluar)
    public function store(Request $request)
{
    $nik = Auth::guard('karyawan')->user()->nik;
    $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
    $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
    $tgl_presensi = date("Y-m-d");
    $jam = date("H:i:s");
    $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
    $lok = explode(",", $lok_kantor->lokasi_cabang);

    // Lokasi kantor (latitude, longitude)
    $latitudekantor = $lok[0];
    $longitudekantor = $lok[1];

    // Mendapatkan lokasi user dari request
    $lokasi = $request->lokasi;
    $lokasiuser = explode(",", $lokasi);
    $latitudeuser = $lokasiuser[0];
    $longitudeuser = $lokasiuser[1];

    // Menghitung jarak antara lokasi user dan kantor
    $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
    $radius_cabang = round($jarak['meters']);

    //Cek Jam Kerja Karyawan
    $namahari = $this->gethari();
    $jamkerja = DB::table('konfigurasi_jamkerja')
        ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->where('nik', $nik)->where('hari', $namahari)->first();

        if($jamkerja == null) {
            $jamkerja = DB::table('konfigurasi_jk_dept_detail')
            ->join('konfigurasi_jk_dept','konfigurasi_jk_dept_detail.kode_jk_dept','=','konfigurasi_jk_dept.kode_jk_dept')
            ->join('jam_kerja', 'konfigurasi_jk_dept_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('kode_dept', $kode_dept)
            ->where('kode_cabang', $kode_cabang)
            ->where('hari', $namahari)->first();
        }

    $maxRadius = 10000; // Radius maksimal dalam meter

    if ($radius_cabang > $maxRadius) {
        return response()->json([
            'status' => 'error',
            'message' => 'Anda berada di luar jangkauan radius',
            'distance' => $radius_cabang,
            'type' => 'radius'
        ], 400);
    }

    $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)
        ->where('nik', $nik)->count();

    $ket = ($cek > 0) ? "out" : "in";

    // Proses penyimpanan gambar presensi
    $image = $request->image;
    $image_parts = explode(";base64,", $image);

    if (count($image_parts) == 2) {
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $nik . "-" . $tgl_presensi . "-" . $ket . ".png";

        // Simpan file gambar
        $imageSaved = Storage::disk('public')->put("uploads/absensi/" . $fileName, $image_base64);

        if ($imageSaved) {
            if ($cek > 0) {
                if ($jam < $jamkerja->jam_pulang){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum Jam Pulang',
                        'type' => 'out'
                    ], 500);
                } else {
                // Jika sudah absen, update data jam_out
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi_out' => $lokasi,
                ];
                $update = DB::table('presensi')
                    ->where('tgl_presensi', $tgl_presensi)
                    ->where('nik', $nik)
                    ->update($data_pulang);

                if ($update) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Selamat Pulang, Terima Kasih untuk kerja hari ini',
                        'type' => 'out'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal memperbarui data presensi pulang',
                        'type' => 'out'
                    ], 500);
                }
            }

            } else {
                // Jika belum absen, cek apakah sudah masuk jam absen
                if ($jam < $jamkerja->awal_jam_masuk) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum Jam Absen',
                        'type' => 'in'
                    ], 400);
                } else if($jam > $jamkerja->akhir_jam_masuk) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Jam Absen Habis',
                        'type' => 'in'
                    ], 400);
                } else {
                    // Simpan data presensi masuk
                    $data_masuk = [
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'foto_in' => $fileName,
                        'lokasi_in' => $lokasi,
                        'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                        'status' => 'h'
                    ];
                    $simpan = DB::table('presensi')->insert($data_masuk);

                    if ($simpan) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Terima Kasih, Selamat Bekerja',
                            'type' => 'in'
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Gagal menyimpan data presensi masuk',
                            'type' => 'in'
                        ], 500);
                    }
                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan gambar presensi',
                'type' => 'image'
            ], 500);
        }
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal memproses gambar presensi',
            'type' => 'image'
        ], 500);
    }
}


    // Fungsi untuk menghitung jarak antara dua titik koordinat
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2)))
            + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $meters = $miles * 1609.344; // Konversi dari mil ke meter
        return compact('meters');
    }

    // Method untuk halaman edit profile
    public function editprofile()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    // Method untuk update profile
    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();

        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }

        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => Hash::make($request->password),
                'foto' => $foto
            ];
        }

        $update = DB::table('karyawan')->where('nik', $nik)->update($data);

        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Update Data Berhasil']);
        } else {
            return Redirect::back()->with(['error' => 'Update Data Gagal']);
        }
    }

    // Method untuk halaman history presensi
    public function history(){
        $namabulan = [
            "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        return view('presensi.history', compact('namabulan'));
    }

    // Method untuk handle request AJAX history
    public function gethistory(Request $request)
{
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $nik = Auth::guard('karyawan')->user()->nik;

    // Cek apakah bulan dan tahun diisi
    if (empty($bulan) || empty($tahun)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Bulan dan Tahun harus diisi!'
        ], 400);
    }

    // Query untuk mendapatkan data presensi berdasarkan bulan dan tahun
    $history = DB::table('presensi')
        ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
        ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
        ->where('nik', $nik)
        ->orderBy('tgl_presensi')
        ->get();

    // Jika data presensi tidak ditemukan
    if ($history->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Data presensi tidak ditemukan untuk bulan dan tahun yang dipilih.'
        ], 404);
    }

    // Tampilkan view jika data ditemukan
    return view('presensi.gethistory', compact('history'));
}

    public function izin(Request $request) {
        $nik = Auth::guard('karyawan')->user()->nik;

        if (!empty($request->bulan) && !empty($request->tahun)){
            $dataizin = DB::table('pengajuan_izin')
                ->leftJoin('master_cuti','pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
                ->orderBy('tgl_izin_dari', 'desc')
                ->where('nik', $nik)
                ->whereRaw('MONTH(tgl_izin_dari)="'.$request->bulan.'"')
                ->whereRaw('YEAR(tgl_izin_dari)="'.$request->tahun.'"')
                ->get();

        } else {
          $dataizin = DB::table('pengajuan_izin')
            ->leftJoin('master_cuti','pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
            ->orderBy('tgl_izin_dari', 'desc')
            ->where('nik', $nik)->limit(5)->orderBy('tgl_izin_dari', 'desc')
            ->get();
        }

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September",
        "Oktober", "November", "Desember"];
        return view('presensi.izin', compact('dataizin', 'namabulan'));
    }

    public function buatizin(){
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request){
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin_dari = $request->tgl_izin_dari;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin_dari' => $tgl_izin_dari,
            'status' =>$status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if($simpan){
            return redirect('/presensi/izin')->with(['success'=>'Data Berhasil Disimpan']);
        } else{
            return redirect('/presensi/izin')->with(key: ['error'=>'Data Gagal Disimpan']);
        }
    }

    public function monitoring(){
        return view('presensi.monitoring');
    }

    public function getpresensi(Request $request){
        $tanggal = $request->tanggal;
        $presensi = DB::table('presensi')
        ->select('presensi.*','nama_lengkap','karyawan.kode_dept', 'jam_masuk', 'nama_jam_kerja', 'jam_masuk', 'jam_pulang', 'keterangan')
        ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
        ->join('karyawan','presensi.nik','=','karyawan.nik')
        ->join('departemen','karyawan.kode_dept','=','departemen.kode_dept')
        ->where('tgl_presensi', $tanggal)
        ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function tampilkanpeta(Request $request) {
        $id = $request->id;
        $presensi = DB::table('presensi')->where('id', $id)
        ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
        ->first();
        return view('presensi.showmap', compact('presensi'));
    }

    public function laporan(){
        $namabulan = [
            "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request){
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->where('nik', $nik)
        ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
        ->first();

        $presensi = DB::table('presensi')
        ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->where('nik', $nik) // Gunakan koma untuk pemisah antara kolom dan variabel
        ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan]) // whereRaw() dengan parameter binding
        ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])  // whereRaw() dengan parameter binding
        ->orderBy('tgl_presensi')
        ->get();

        if (isset($_POST['export'])) {
            $time = date("H:i:S");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Presensi Karyawan $time.xls");
            return view('presensi.cetaklaporanexcel', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
        }
        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    }

    public function rekap(){
        $namabulan = [
            "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request) {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime ($dari));
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        while(strtotime($dari) <= strtotime($sampai)){
            $rangetanggal[] = $dari;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }

        $jmlhari = count($rangetanggal);
        $lastrange = $jmlhari - 1;
        $sampai = $rangetanggal[$lastrange];
        if($jmlhari==30){
            array_push($rangetanggal, NULL);
        } else if($jmlhari==29){
            array_push($rangetanggal,NULL,NULL);
        } else if($jmlhari==28){
            array_push($rangetanggal,NULL,NULL,NULL);
        }

        $query = Karyawan::query();
        $query->selectRaw("karyawan.nik, nama_lengkap, jabatan, tgl_1,
                            tgl_2,
                            tgl_3,
                            tgl_4,
                            tgl_5,
                            tgl_6,
                            tgl_7,
                            tgl_8,
                            tgl_9,
                            tgl_10,
                            tgl_11,
                            tgl_12,
                            tgl_13,
                            tgl_14,
                            tgl_15,
                            tgl_16,
                            tgl_17,
                            tgl_18,
                            tgl_19,
                            tgl_20,
                            tgl_21,
                            tgl_22,
                            tgl_23,
                            tgl_24,
                            tgl_25,
                            tgl_26,
                            tgl_27,
                            tgl_28,
                            tgl_29,
                            tgl_30,
                            tgl_31"
    );

        $query->leftJoin(
            DB::raw("(
            SELECT presensi.nik,
        MAX(IF(tgl_presensi = '$rangetanggal[0]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_1,

        MAX(IF(tgl_presensi = '$rangetanggal[1]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_2,

        MAX(IF(tgl_presensi = '$rangetanggal[2]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_3,

        MAX(IF(tgl_presensi = '$rangetanggal[3]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_4,

        MAX(IF(tgl_presensi = '$rangetanggal[4]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_5,

        MAX(IF(tgl_presensi = '$rangetanggal[5]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_6,

        MAX(IF(tgl_presensi = '$rangetanggal[6]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_7,

        MAX(IF(tgl_presensi = '$rangetanggal[7]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_8,

        MAX(IF(tgl_presensi = '$rangetanggal[8]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_9,

        MAX(IF(tgl_presensi = '$rangetanggal[9]',
                CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_10,

        MAX(IF(tgl_presensi = '$rangetanggal[10]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_11,

        MAX(IF(tgl_presensi = '$rangetanggal[11]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_12,

        MAX(IF(tgl_presensi = '$rangetanggal[12]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_13,

        MAX(IF(tgl_presensi = '$rangetanggal[13]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_14,

        MAX(IF(tgl_presensi = '$rangetanggal[14]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_15,

        MAX(IF(tgl_presensi = '$rangetanggal[15]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_16,

        MAX(IF(tgl_presensi = '$rangetanggal[16]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_17,

        MAX(IF(tgl_presensi = '$rangetanggal[17]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_18,

        MAX(IF(tgl_presensi = '$rangetanggal[18]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_19,

        MAX(IF(tgl_presensi = '$rangetanggal[19]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_20,

        MAX(IF(tgl_presensi = '$rangetanggal[20]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_21,

        MAX(IF(tgl_presensi = '$rangetanggal[21]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_22,

        MAX(IF(tgl_presensi = '$rangetanggal[22]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_23,

        MAX(IF(tgl_presensi = '$rangetanggal[23]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_24,

        MAX(IF(tgl_presensi = '$rangetanggal[24]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_25,

        MAX(IF(tgl_presensi = '$rangetanggal[25]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_26,

        MAX(IF(tgl_presensi = '$rangetanggal[26]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_27,

        MAX(IF(tgl_presensi = '$rangetanggal[27]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_28,

        MAX(IF(tgl_presensi = '$rangetanggal[28]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_29,

        MAX(IF(tgl_presensi = '$rangetanggal[29]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_30,

        MAX(IF(tgl_presensi = '$rangetanggal[30]',
               CONCAT(
                   IFNULL(jam_in, 'NA'), '|',
                   IFNULL(jam_out, 'NA'), '|',
                   IFNULL(presensi.status, 'NA'), '|',
                   IFNULL(nama_jam_kerja, 'NA'), '|',
                   IFNULL(jam_masuk, 'NA'), '|',
                   IFNULL(jam_pulang, 'NA'), '|',
                   IFNULL(presensi.kode_izin, 'NA'), '|',
                   IFNULL(keterangan, 'NA')
               ), NULL)) as tgl_31

        FROM presensi
        LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
        LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
        WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND '$sampai'
        GROUP BY nik

            ) presensi"),
             function($join){
                $join->on('karyawan.nik','=','presensi.nik');
             }
        );

        $query->orderBy('nama_lengkap');
        $rekap = $query->get();

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            //Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-md-excel");
            //Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Presensi Karyawan $time.xls");
        }
        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap', 'rangetanggal', 'jmlhari'));
        }

    public function izinsakit(Request $request){

        $query = Pengajuanizin::query();
        $query->select('kode_izin', 'tgl_izin_dari', 'tgl_izin_sampai', 'pengajuan_izin.nik', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan');
        $query->join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik');
        if(!empty($request->dari) && !empty($request->sampai)){
            $query->whereBetween('tgl_izin_dari', [$request->dari, $request->sampai]);
        }

        if(!empty($request->nik)) {
            $query->where('pengajuan_izin.nik', $request->nik);
        }

        if(!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%'. $request->nama_lengkap . '%');
        }

        if($request->status_approved === '0' || $request->status_approved === '1' || $request->status_approved === '2') {
            $query->where('status_approved', $request->status_approved);
        }

        $query->orderBy('tgl_izin_dari', 'desc');
        $izinsakit = $query->paginate(10);
        $izinsakit->appends($request->all());
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(Request $request){
        $status_approved = $request->status_approved;
        $kode_izin = $request->kode_izin_form; // Perbaikan disini
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $nik = $dataizin->nik;
        $tgl_dari = $dataizin->tgl_izin_dari;
        $tgl_sampai = $dataizin->tgl_izin_sampai;
        $status = $dataizin->status;
        DB::beginTransaction();
        try {
            if($status_approved == 1) {
                while(strtotime($tgl_dari) <= strtotime($tgl_sampai)){
                    DB::table('presensi')->insert([
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_dari,
                        'status' => $status,
                        'kode_izin' => $kode_izin
                    ]);
                    $tgl_dari = date("Y-m-d",strtotime("+1 days",strtotime($tgl_dari)));
                }
            }

            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update(['status_approved' => $status_approved]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diproses']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diproses']);
        }
        //$update = DB::table('pengajuan_izin')
        //            ->where('id', $kode_izin)
        //            ->update([
        //               'status_approved' => $status_approved
        //            ]);
        //
        //if($update){
        //    return Redirect::back()->with(['success'=>'Data Berhasil Diupdate']);
        //} else {
        //    return Redirect::back()->with(['warning'=>'Data Gagal Diupdate']);
        //}
    }

    public function batalkanizinsakit($kode_izin){

        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                'status_approved' => 0
            ]);
            DB::table('presensi')->where('kode_izin', $kode_izin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin_dari = $request->tgl_izin_dari;
        $nik = Auth::guard('karyawan')->user()->nik;

        $cek = DB::table('pengajuan_izin')->where('nik', $nik)->where('tgl_izin_dari', $tgl_izin_dari)->count();
        return $cek;
    }

    public function showact($kode_izin){
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin',$kode_izin)->first();
        return view('presensi.showact', compact('dataizin'));
    }

    public function deleteizin($kode_izin){
        $cekdataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $doc_sid = $cekdataizin->doc_sid;

        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->delete();
            if ($doc_sid != null){

                Storage::delete('/public/uploads/sid/'.$doc_sid);
            }
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error' => 'Data Berhasil Dihapus']);
        }
    }
}
