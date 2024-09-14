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
        // Validasi input (opsional, tapi direkomendasikan)
        $request->validate([
            'nik' => 'required',
            'password' => 'required',
        ]);

        // Coba login dengan guard 'karyawan' menggunakan nik dan password
        if (Auth::guard('karyawan')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            echo 'Berhasil Login';
        } else {
            echo 'Gagal Login';
        }
    }
}
