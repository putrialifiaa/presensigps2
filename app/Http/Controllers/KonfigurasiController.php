<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class KonfigurasiController extends Controller
{
    public function lokasikantor()
    {
        return view('konfigurasi.lokasikantor');
    }
}
