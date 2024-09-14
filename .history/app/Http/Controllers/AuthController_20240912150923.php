<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth fasad
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        // Validasi input
        $request->validate([
            'nik' => 'required',
            'password' => 'required',
        ]);

        // Coba login dengan guard 'karyawan' menggunakan nik dan password
        if (Auth::guard('karyawan')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            // Login berhasil, redirect ke halaman dashboard atau halaman lain
            return redirect()->route('dashboard')->with('success', 'Berhasil Login');
        } else {
            // Login gagal, kembali ke halaman login dengan pesan error
            return back()->withErrors([
                'loginError' => 'NIK atau Password salah, coba lagi.',
            ])->withInput($request->only('nik'));
        }
    }
}
