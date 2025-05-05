<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\cutiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;

// ✅ Redirect root ke halaman login
Route::redirect('/', '/login');

// ✅ Autentikasi
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// ✅ HRD/Admin Routes (hanya sampai Kelola Pegawai)
Route::middleware(['auth', 'check.role:hrd'])->prefix('hrd')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/kelola-karyawan', [KaryawanController::class, 'index'])->name('admin.karyawan');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('admin.absensi');
    Route::get('/cuti', [cutiController::class, 'index'])->name('admin.pengajuan_cuti');
});

    
    // // Kepala Yayasan Routes
    // Route::prefix('kepala')->middleware(['check.role:kepala'])->group(function () {
    //     Route::get('/dashboard', function () {
    //         return view('kepala.dashboard');
    //     })->name('kepala.dashboard');
        
    //     // Add other Kepala routes here
    // });
    
    // Pegawai Routes
  

    Route::prefix('pegawai')->middleware(['check.role:pegawai'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('karyawan.index');
    });
    






