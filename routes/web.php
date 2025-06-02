<?php 

use App\Models\Penilaian;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\cutiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\penilaianController;

// ✅ Redirect root ke halaman login
Route::redirect('/', '/login');

// Autentikasi
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// HRD/Admin Routes (hanya sampai Kelola Pegawai)
Route::middleware(['auth', 'check.role:hrd'])->prefix('hrd')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.index');

    // List karyawan di /kelola-karyawan

    Route::get('/kelola-karyawan', [KaryawanController::class, 'index'])->name('admin.karyawan');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show'])->name('pegawai.show');
    Route::get('/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('pegawai.edit');
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('pegawai.update');
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('pegawai.destroy');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('admin.absensi');

    // Cuti
    Route::get('/cuti', [CutiController::class, 'index'])->name('admin.pengajuan_cuti');

    // Penilaian
    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('admin.penilaian');
    Route::get('/penilaian-karyawan', [PenilaianController::class, 'index2'])->name('admin.penilaian-karyawan');

    // Profil HRD
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.edit-profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
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
    






