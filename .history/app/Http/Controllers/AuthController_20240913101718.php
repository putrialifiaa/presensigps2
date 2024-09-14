<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        // Validasi input
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        // Ambil data user dari database berdasarkan NIK
        $user = \App\Models\User::where('nik', $request->nik)->first();

        // Jika user ditemukan dan password cocok
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->intended('dashboard');
        } else {
            return back()->withErrors([
                'loginError' => 'NIK atau password salah.',
            ]);
        }
    }

    public function generateHash(Request $request)
    {
        // Contoh metode untuk menghasilkan hash untuk testing/debugging
        $pass = '123'; // Gunakan string untuk konsistensi
        $hash = Hash::make($pass);
        return $hash; // Return hash sebagai respons
    }
}
