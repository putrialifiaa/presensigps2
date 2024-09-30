<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PresensiController extends Controller
{
    // Method untuk halaman create (presensi)
    public function create()
    {
        $hariini = date("Y-m-d");
        $nik = Auth::guard('karyawan')->user()->nik;
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nik', $nik)
            ->count();
        return view('presensi.create', compact('cek'));
    }

    // Method untuk menyimpan data presensi (masuk & keluar)
    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");

        // Lokasi kantor (latitude, longitude)
        $latitudekantor = -7.170690135108098;
        $longitudekantor = 112.65269280809838;

        // Mendapatkan lokasi user dari request
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        // Menghitung jarak antara lokasi user dan kantor
        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak['meters']);
        $maxRadius = 100; // Radius maksimal dalam meter

        if ($radius > $maxRadius) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda berada di luar jangkauan radius kantor',
                'distance' => $radius,
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
                } else {
                    // Jika belum absen, simpan data masuk
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
}
