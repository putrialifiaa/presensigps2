<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IzinabsenController extends Controller
{
    public function create(){
        return view('izin.create');
    }
}
