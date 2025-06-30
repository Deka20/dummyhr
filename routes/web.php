<?php 

use App\Models\Penilaian;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\cutiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KuisionerController;
use App\Http\Controllers\penilaianController;
use App\Http\Controllers\LokasiKantorController;
use App\Http\Controllers\ListPengajuanController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\RiwayatAbsensiController;
use App\Http\Controllers\PegawaiDashboardController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\PeriodeKuisionerController;

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
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.index');
    // Route::post('/dashboard/absen', [AdminDashboardController::class, 'absen'])->name('dashboard.absen');

    // Lokasi Kantor
    Route::resource('lokasi-kantor', LokasiKantorController::class)->names([
        'index' => 'admin.LokasiKantor.index',
        'create' => 'admin.LokasiKantor.tambah',
        'store' => 'admin.LokasiKantor.store',
        'show' => 'admin.LokasiKantor.show',
        'edit' => 'admin.LokasiKantor.edit',
        'update' => 'admin.LokasiKantor.update',
        'destroy' => 'admin.LokasiKantor.destroy'
    ]);
    
    // ✅ PERBAIKAN: Route untuk get lokasi kantor harus dalam prefix 'hrd'
    Route::get('/lokasi-kantor/{id}', [AdminDashboardController::class, 'getLokasiKantor'])
        ->name('admin.lokasi-kantor.get')
        ->where('id', '[0-9]+'); // Pastikan ID berupa angka
    
    // Route untuk absensi
    Route::post('/absen', [AdminDashboardController::class, 'absen'])->name('admin.absensi');
    
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
    Route::get('/absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('admin.absensi.edit');
    Route::put('/absensi/{id}', [AbsensiController::class, 'update'])->name('admin.absensi.update');
    Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('admin.absensi.destroy');

    //Riwayat
     Route::get('/RiwayatAbsensi', [RiwayatAbsensiController::class, 'index'])->name('admin.RiwayatAbsensi');

    // Pengajuan Cuti (existing route)
    Route::get('/cuti-admin', [CutiController::class, 'index'])->name('admin.pengajuan_cuti');
    
    // Additional cuti routes
    Route::post('/cuti-admin', [CutiController::class, 'store'])->name('admin.cuti.store');
    Route::get('/cuti-admin/{id}', [CutiController::class, 'show'])->name('admin.cuti.show');
    
    // List Pengajuan Cuti
    Route::get('/List-Pengajuan', [ListPengajuanController::class, 'index'])->name('admin.listPengajuan');

    // Penilaian
    Route::get('/penilaian', [KuisionerController::class, 'index' ,])->name('admin.kuisioner.index');
    Route::get('/kuisioner/create', [KuisionerController::class, 'create'])->name('admin.kuisioner.create');
    Route::post('/kuisioner', [KuisionerController::class, 'store'])->name('admin.kuisioner.store');
    Route::get('/kuisioner/{kuisioner}', [KuisionerController::class, 'show'])->name('admin.kuisioner.show');
    Route::get('/kuisioner/{kuisioner}/edit', [KuisionerController::class, 'edit'])->name('admin.kuisioner.edit');
    Route::put('/kuisioner/{kuisioner}', [KuisionerController::class, 'update'])->name('admin.kuisioner.update');
    Route::patch('/kuisioner/{kuisioner}/toggle', [KuisionerController::class, 'toggle'])->name('admin.kuisioner.toggle');
    Route::delete('/kuisioner/{kuisioner}', [KuisionerController::class, 'destroy'])->name('admin.kuisioner.destroy');


    Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
Route::get('/periode/create', [PeriodeController::class, 'create'])->name('periode.create');
Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
Route::get('/periode/{id}/edit', [PeriodeController::class, 'edit'])->name('periode.edit');
Route::put('/periode/{id}', [PeriodeController::class, 'update'])->name('periode.update');
Route::delete('/periode/{id}', [PeriodeController::class, 'destroy'])->name('periode.destroy');


Route::group(['prefix' => 'periode/{periodeId}/kuisioner'], function () {
    Route::get('/', [PeriodeKuisionerController::class, 'index'])->name('periode.kuisioner.index');
    Route::put('/', [PeriodeKuisionerController::class, 'update'])->name('periode.kuisioner.update');
    Route::post('/copy', [PeriodeKuisionerController::class, 'copyFromPeriode'])->name('periode.kuisioner.copy');
    Route::delete('/reset', [PeriodeKuisionerController::class, 'reset'])->name('periode.kuisioner.reset');
    Route::post('/auto-select', [PeriodeKuisionerController::class, 'autoSelect'])->name('periode.kuisioner.auto-select');
});
Route::post('/admin/periode/{periode}/kuisioner/bulk-action', [PeriodeKuisionerController::class, 'bulkAction'])
    ->name('admin.periode.kuisioner.bulk-action');

    // Profil HRD
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.edit-profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});






use App\Http\Controllers\PegawaiKuisionerController;

Route::prefix('pegawai')->middleware(['check.role:pegawai'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('karyawan.index');
    Route::post('/absen', [PegawaiDashboardController::class, 'absen'])->name('pegawai.absensi');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('karyawan.edit-profil');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Absensi
    Route::get('/absensi', [RiwayatAbsensiController::class, 'index'])->name('karyawan.absensi');

    // Kuisioner
Route::prefix('kuisioner')->name('kuisioner.')->group(function () {
         Route::get('/', [PegawaiKuisionerController::class, 'index'])->name('index');
        
        // AJAX endpoints
        Route::get('/get-pegawai/{departemen}', [PegawaiKuisionerController::class, 'getPegawaiByDepartemen'])->name('get-pegawai');
        Route::get('/get-periode/{tahun}', [PegawaiKuisionerController::class, 'getPeriodeByTahun'])->name('get-periode');
        Route::get('/check-status', [PegawaiKuisionerController::class, 'checkKuisionerStatus'])->name('check-status');
        Route::get('/multiple-status', [PegawaiKuisionerController::class, 'getMultipleStatus'])->name('multiple-status');
        
        // Form kuisioner
        Route::get('/{periode}/{dinilai}', [PegawaiKuisionerController::class, 'show'])->name('show');
        Route::post('/{periode}/{dinilai}', [PegawaiKuisionerController::class, 'store'])->name('store');
        Route::post('/{periode}/{dinilai}/reset', [PegawaiKuisionerController::class, 'reset'])->name('reset');
        
        // Hasil dan export
        Route::get('/{periode}/{dinilai}/result', [PegawaiKuisionerController::class, 'result'])->name('result');
        Route::get('/{periode}/{dinilai}/export', [PegawaiKuisionerController::class, 'export'])->name('export');
        
        // Riwayat dan hapus
        Route::get('/history', [PegawaiKuisionerController::class, 'history'])->name('history');
        Route::delete('/{periode}/{dinilai}', [PegawaiKuisionerController::class, 'destroy'])->name('destroy');
});

});
