<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(){
        $users = DB::table('users')
        ->select('users.id','users.name','email','nama_cabang','roles.name as role')
        ->join('cabang','users.kode_cabang','=','cabang.kode_cabang')
        ->join('model_has_roles','users.id','=','model_has_roles.model_id')
        ->join('roles','model_has_roles.role_id','=','roles.id')
        ->get();

        return view('users.index', compact('users'));
    }
}
