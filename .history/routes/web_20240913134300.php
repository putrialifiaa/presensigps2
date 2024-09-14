<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view ('auth.login');
})->name('login');
Route::post('/proseslogin', [AuthController::class, 'proseslogin']);


Route::middleware(('auth:karyawan'))->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']); // Perbaikan penulisan DashboardController
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);
});
