<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/dashboard', function () {
    return view('admin.index');
});
Route::get('/sidebar', function () {
    return view('layouts.sidebar');
});
Route::get('/header-content', function () {
    return view('layouts.header-content');
});
Route::get('/', function(){
    return view('auth.login');
});
Route::get('/karyawan', function(){
    return view('admin.karyawan');
});
Route::get('/absensi', function(){
    return view('admin.absensi');
});
Route::get('/penilaian', function(){
    return view('admin.penilaian');
});
Route::get('/penilaian-karyawan', function(){
    return view('admin.penilaian-karyawan');
});
Route::get('/jabatan', function(){
    return view('admin.struktur');
});
Route::get('/pengaturan', function(){
    return view('admin.edit-profil');
});
Route::get('/cuti', function(){
    return view('admin.pengajuan_cuti');
});
Route::get('/dashboard-karyawan', function(){
    return view('karyawan.index');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
