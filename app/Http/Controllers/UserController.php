<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index(){
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $role = DB::table('roles')->orderBy('id')->get();
        $users = DB::table('users')
        ->select('users.id','users.name','email','nama_cabang','roles.name as role')
        ->join('cabang','users.kode_cabang','=','cabang.kode_cabang')
        ->join('model_has_roles','users.id','=','model_has_roles.model_id')
        ->join('roles','model_has_roles.role_id','=','roles.id')
        ->get();

        return view('users.index', compact('users','cabang', 'role'));
    }

    public function store(Request $request){
        $nama_user = $request->nama_user;
        $email = $request->email;
        $kode_cabang = $request->kode_cabang;
        $role = $request->role;
        $password = bcrypt($request->password);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $nama_user,
                'email' => $email,
                'kode_cabang' => $kode_cabang,
                'password' => $password
            ]);

            $user->assignRole($role);

            DB::commit();

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }
}
