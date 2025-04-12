<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/dashboard', function () {
    return view('dashboard.index');
});
Route::get('/sidebar', function () {
    return view('layouts.sidebar');
});
Route::get('/header-content', function () {
    return view('layouts.header-content');
});
Route::get('/', function(){
    return view('pages.login');
});
Route::get('/karyawan', function(){
    return view('dashboard.karyawan');
});
Route::get('/absensi', function(){
    return view('dashboard.absensi');
});
Route::get('/jabatan', function(){
    return view('dashboard.struktur');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
