<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login page
Route::redirect('/', '/login');

// Authentication Routes
Route::controller(authController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Role-based Routes with Role Middleware Check
Route::middleware(['auth'])->group(function () {
    
    // HRD Routes
    Route::prefix('hrd')->middleware(['check.role:hrd'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.index');
        })->name('admin.index');
        
        // Add other HRD routes here
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
        Route::get('/dashboard', function () {
            return view('karyawan.index');
        })->name('karyawan.index');
        
        // Add other Pegawai routes here
    });
});