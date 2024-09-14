<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        if (Auth::guard('karyawan')->attempt(['nik'=> $request->nik, 'password'=> $request->password])) {
            return 'Berhasil Login';
        } else {
            return 'Gagal Login';
        }
    }
}
