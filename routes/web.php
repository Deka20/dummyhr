<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AbsensiController,
    CutiController,
    ProfileController,
    KaryawanController,
    KuisionerController,
    PenilaianController,
    LokasiKantorController,
    ListPengajuanController,
    AdminDashboardController,
    RiwayatAbsensiController,
    PegawaiDashboardController,
    PeriodeController,
    PeriodeKuisionerController,
    PegawaiKuisionerController,
    PegawaiCutiController
};

// Redirect root ke halaman login
Route::redirect('/', '/login');

// ====================
// Autentikasi
// ====================
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// ====================
// HRD / Admin Routes
// ====================
Route::middleware(['auth', 'check.role:hrd'])->prefix('hrd')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.index');
    Route::post('/absen', [AdminDashboardController::class, 'absen'])->name('admin.absensi');

    // Lokasi Kantor
    Route::resource('lokasi-kantor', LokasiKantorController::class)->names([
        'index'   => 'admin.LokasiKantor.index',
        'create'  => 'admin.LokasiKantor.tambah',
        'store'   => 'admin.LokasiKantor.store',
        'show'    => 'admin.LokasiKantor.show',
        'edit'    => 'admin.LokasiKantor.edit',
        'update'  => 'admin.LokasiKantor.update',
        'destroy' => 'admin.LokasiKantor.destroy',
    ]);
    Route::get('/lokasi-kantor/{id}', [AdminDashboardController::class, 'getLokasiKantor'])
        ->name('admin.lokasi-kantor.get')
        ->where('id', '[0-9]+');

    // Kelola Karyawan
    Route::prefix('karyawan')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('admin.karyawan');
        Route::get('/create', [KaryawanController::class, 'create'])->name('karyawan.create');
        Route::post('/', [KaryawanController::class, 'store'])->name('karyawan.store');
        Route::get('/{id}', [KaryawanController::class, 'show'])->name('pegawai.show');
        Route::get('/{id}/edit', [KaryawanController::class, 'edit'])->name('pegawai.edit');
        Route::put('/{id}', [KaryawanController::class, 'update'])->name('pegawai.update');
        Route::delete('/{id}', [KaryawanController::class, 'destroy'])->name('pegawai.destroy');
    });
     // Route untuk generate user account dan reset password
    Route::post('/karyawan/{id}/generate-account', [KaryawanController::class, 'generateUserAccount'])->name('karyawan.generateAccount');
    Route::post('/karyawan/{id}/reset-password', [KaryawanController::class, 'resetUserPassword'])->name('karyawan.resetPassword');


    // Absensi
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('admin.absensi');
        Route::get('/{id}/edit', [AbsensiController::class, 'edit'])->name('admin.absensi.edit');
        Route::put('/{id}', [AbsensiController::class, 'update'])->name('admin.absensi.update');
        Route::delete('/{id}', [AbsensiController::class, 'destroy'])->name('admin.absensi.destroy');
    });

    // Riwayat Absensi
    Route::get('/riwayat-absensi', [RiwayatAbsensiController::class, 'index'])->name('admin.RiwayatAbsensi');

    // Pengajuan Cuti
    Route::prefix('cuti-admin')->group(function () {
        Route::get('/', [CutiController::class, 'index'])->name('admin.pengajuan_cuti');
        Route::post('/', [CutiController::class, 'store'])->name('admin.cuti.store');
        Route::get('/{id}', [CutiController::class, 'show'])->name('admin.cuti.show');
    });

    //List Pengajuan Routes
Route::get('/list-pengajuan', [ListPengajuanController::class, 'index'])->name('admin.listPengajuan');
Route::put('/cuti/{id}/status', [ListPengajuanController::class, 'updateStatus'])->name('cuti.updateStatus');
Route::get('/cuti/{id}/detail', [ListPengajuanController::class, 'show'])->name('cuti.detail'); // Optional for future use
    // Kuisioner
    Route::prefix('kuisioner')->group(function () {
        Route::get('/', [KuisionerController::class, 'index'])->name('admin.kuisioner.index');
        Route::get('/create', [KuisionerController::class, 'create'])->name('admin.kuisioner.create');
        Route::post('/', [KuisionerController::class, 'store'])->name('admin.kuisioner.store');
        Route::get('/{kuisioner}', [KuisionerController::class, 'show'])->name('admin.kuisioner.show');
        Route::get('/{kuisioner}/edit', [KuisionerController::class, 'edit'])->name('admin.kuisioner.edit');
        Route::put('/{kuisioner}', [KuisionerController::class, 'update'])->name('admin.kuisioner.update');
        Route::patch('/{kuisioner}/toggle', [KuisionerController::class, 'toggle'])->name('admin.kuisioner.toggle');
        Route::delete('/{kuisioner}', [KuisionerController::class, 'destroy'])->name('admin.kuisioner.destroy');
    });

    // Periode
    Route::prefix('periode')->group(function () {
        Route::get('/', [PeriodeController::class, 'index'])->name('periode.index');
        Route::get('/create', [PeriodeController::class, 'create'])->name('periode.create');
        Route::post('/', [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('/{id}/edit', [PeriodeController::class, 'edit'])->name('periode.edit');
        Route::put('/{id}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::delete('/{id}', [PeriodeController::class, 'destroy'])->name('periode.destroy');

        Route::prefix('{periodeId}/kuisioner')->group(function () {
            Route::get('/', [PeriodeKuisionerController::class, 'index'])->name('periode.kuisioner.index');
            Route::put('/', [PeriodeKuisionerController::class, 'update'])->name('periode.kuisioner.update');
            Route::post('/copy', [PeriodeKuisionerController::class, 'copyFromPeriode'])->name('periode.kuisioner.copy');
            Route::delete('/reset', [PeriodeKuisionerController::class, 'reset'])->name('periode.kuisioner.reset');
            Route::post('/auto-select', [PeriodeKuisionerController::class, 'autoSelect'])->name('periode.kuisioner.auto-select');
        });
    });
    Route::post('/periode/{periode}/kuisioner/bulk-action', [PeriodeKuisionerController::class, 'bulkAction'])
        ->name('admin.periode.kuisioner.bulk-action');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.edit-profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ====================
// Pegawai Routes
// ====================
Route::prefix('pegawai')->middleware(['check.role:pegawai'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('karyawan.index');
    Route::post('/absen', [PegawaiDashboardController::class, 'absen'])->name('pegawai.absensi');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('karyawan.edit-profil');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Absensi
    Route::get('/absensi', [RiwayatAbsensiController::class, 'index'])->name('karyawan.absensi');

    // Kuisioner
    Route::prefix('kuisioner')->name('kuisioner.')->group(function () {
        Route::get('/', [PegawaiKuisionerController::class, 'index'])->name('index');

        // AJAX endpoints
        Route::get('/get-all-pegawai', [PegawaiKuisionerController::class, 'getAllPegawai']);
        Route::get('/debug/pegawai', [PegawaiKuisionerController::class, 'debugPegawai']);
        
        // Form kuisioner
        Route::get('/{periode}/{dinilai}', [PegawaiKuisionerController::class, 'show'])->name('show');
        Route::post('/{periode}/{dinilai}', [PegawaiKuisionerController::class, 'store'])->name('store');
        
        // Reset route - changed to GET for direct access from link
        Route::get('/{periode}/{dinilai}/reset', [PegawaiKuisionerController::class, 'reset'])->name('reset');
        
        // Hasil
        Route::get('/{periode}/{dinilai}/result', [PegawaiKuisionerController::class, 'result'])->name('result');
        Route::get('/{periode}/{dinilai}', [PegawaiKuisionerController::class, 'fill'])->name('fill');

        // Riwayat dan hapus
        Route::get('/history', [PegawaiKuisionerController::class, 'history'])->name('history');
        Route::delete('/{periode}/{dinilai}', [PegawaiKuisionerController::class, 'destroy'])->name('destroy');
    });

            Route::prefix('cuti')->name('cuti.')->group(function () {
            Route::get('/', [PegawaiCutiController::class, 'index'])->name('index');
            Route::get('/create', [PegawaiCutiController::class, 'create'])->name('create');
            Route::post('/', [PegawaiCutiController::class, 'store'])->name('store');
            Route::get('/{id}', [PegawaiCutiController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PegawaiCutiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PegawaiCutiController::class, 'update'])->name('update');
            Route::delete('/{id}', [PegawaiCutiController::class, 'destroy'])->name('destroy');
        });
  
});
