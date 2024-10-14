<?php

namespace App\Http\Controllers;

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
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nik', $nik)
            ->count();
        $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
        $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $jamkerja = DB::table('konfigurasi_jamkerja')
        ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->where('nik', $nik)->where('hari', $namahari)->first();

        return view('presensi.create', compact('cek', 'lok_kantor', 'jamkerja'));
    }

    // Method untuk menyimpan data presensi (masuk & keluar)
    public function store(Request $request)
{
    $nik = Auth::guard('karyawan')->user()->nik;
    $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
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
    $namahari = $this->gethari();
    $jamkerja = DB::table('konfigurasi_jamkerja')
        ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        ->where('nik', $nik)->where('hari', $namahari)->first();

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

    public function izin() {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dataizin = DB::table('pengajuan_izin')->where('nik', $nik)->get();
        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin(){
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request){
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin' => $tgl_izin,
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
        ->select('presensi.*','nama_lengkap','nama_dept')
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
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $rekap = DB::table('presensi')
        ->selectRaw('presensi.nik, nama_lengkap,
            MAX(IF(DAY(tgl_presensi) = 1, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_1,
            MAX(IF(DAY(tgl_presensi) = 2, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_2,
            MAX(IF(DAY(tgl_presensi) = 3, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_3,
            MAX(IF(DAY(tgl_presensi) = 4, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_4,
            MAX(IF(DAY(tgl_presensi) = 5, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_5,
            MAX(IF(DAY(tgl_presensi) = 6, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_6,
            MAX(IF(DAY(tgl_presensi) = 7, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_7,
            MAX(IF(DAY(tgl_presensi) = 8, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_8,
            MAX(IF(DAY(tgl_presensi) = 9, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_9,
            MAX(IF(DAY(tgl_presensi) = 10, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_10,
            MAX(IF(DAY(tgl_presensi) = 11, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_11,
            MAX(IF(DAY(tgl_presensi) = 12, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_12,
            MAX(IF(DAY(tgl_presensi) = 13, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_13,
            MAX(IF(DAY(tgl_presensi) = 14, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_14,
            MAX(IF(DAY(tgl_presensi) = 15, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_15,
            MAX(IF(DAY(tgl_presensi) = 16, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_16,
            MAX(IF(DAY(tgl_presensi) = 17, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_17,
            MAX(IF(DAY(tgl_presensi) = 18, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_18,
            MAX(IF(DAY(tgl_presensi) = 19, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_19,
            MAX(IF(DAY(tgl_presensi) = 20, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_20,
            MAX(IF(DAY(tgl_presensi) = 21, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_21,
            MAX(IF(DAY(tgl_presensi) = 22, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_22,
            MAX(IF(DAY(tgl_presensi) = 23, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_23,
            MAX(IF(DAY(tgl_presensi) = 24, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_24,
            MAX(IF(DAY(tgl_presensi) = 25, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_25,
            MAX(IF(DAY(tgl_presensi) = 26, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_26,
            MAX(IF(DAY(tgl_presensi) = 27, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_27,
            MAX(IF(DAY(tgl_presensi) = 28, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_28,
            MAX(IF(DAY(tgl_presensi) = 29, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_29,
            MAX(IF(DAY(tgl_presensi) = 30, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_30,
            MAX(IF(DAY(tgl_presensi) = 31, CONCAT(jam_in, "-",IFNULL(jam_out,"00:00:00")), "")) as tgl_31')
        ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
        ->whereRaw('MONTH(tgl_presensi)="'.$bulan.'"')
        ->whereRaw('YEAR(tgl_presensi)="'.$tahun.'"')
        ->groupByRaw('presensi.nik, nama_lengkap')
        ->get();

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y_H-i-s");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Rekap_Presensi_Karyawan_$time.xls");

            // Mulai output HTML untuk Excel
            echo "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
            echo "<head>";
            echo "<meta http-equiv=\"content-type\" content=\"application/vnd.ms-excel; charset=UTF-8\">";
            echo "</head>";
            echo "<body>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>NIK</th>";
            echo "<th>Nama Lengkap</th>";

            // Menambahkan tanggal
            for ($i = 1; $i <= 31; $i++) {
                echo "<th>$i</th>";
            }
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach ($rekap as $row) {
                echo "<tr>";
                echo "<td>{$row->nik}</td>";
                echo "<td>{$row->nama_lengkap}</td>";

                // Menambahkan data untuk setiap tanggal
                for ($i = 1; $i <= 31; $i++) {
                    echo "<td>{$row->{'tgl_' . $i}}</td>";
                }
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</body>";
            echo "</html>";
            exit;
        }
        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap'));
    }

    public function izinsakit(Request $request){

        $query = Pengajuanizin::query();
        $query->select('id', 'tgl_izin', 'pengajuan_izin.nik', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan');
        $query->join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik');
        if(!empty($request->dari) && !empty($request->sampai)){
            $query->whereBetween('tgl_izin', [$request->dari, $request->sampai]);
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

        $query->orderBy('tgl_izin', 'desc');
        $izinsakit = $query->paginate(2);
        $izinsakit->appends($request->all());
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(Request $request){
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form; // Perbaikan disini
        $update = DB::table('pengajuan_izin')
                    ->where('id', $id_izinsakit_form)
                    ->update([
                        'status_approved' => $status_approved
                    ]);

        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning'=>'Data Gagal Diupdate']);
        }
    }

    public function batalkanizinsakit($id){
        $update = DB::table('pengajuan_izin')->where('id', $id)->update([
                        'status_approved' => 0
                    ]);

        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning'=>'Data Gagal Diupdate']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $nik = Auth::guard('karyawan')->user()->nik;

        $cek = DB::table('pengajuan_izin')->where('nik', $nik)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }
    }
