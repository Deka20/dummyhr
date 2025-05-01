<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class authController extends Controller
{

    public function showLoginForm()
    {
        // Periksa apakah ada pengalihan tertunda (untuk SweetAlert)

        if (session('login_success') && session('redirect_to')) {
            $redirectTo = session('redirect_to');
            return view('auth.login', [
                'login_success' => true,
                'redirect_to' => $redirectTo
            ]);
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi data nya
        $request->validate([
            'role' => ['required', 'string', 'in:hrd,kepala,pegawai'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

       // Coba autentikasi dengan kondisi tambahan pada peran

        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt(array_merge($credentials, ['role' => $request->role]))) {
            // Autentikasi berhasil
            $user = Auth::user();
            $pegawai = $user->pegawai; // Ambil data pegawai
            $request->session()->regenerate();
            
            // Dapatkan rute dashboard target berdasarkan peran

            $redirectTo = $this->getRedirectRoute($request->role);
            
          // Kembali ke halaman login dengan sukses flag dan rute target untuk pengalihan

            return redirect()->route('login')
                ->with('login_success', true)
                ->with('redirect_to', $redirectTo);
        }

        // Authentication Gagal
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

    protected function getRedirectRoute($role)
    {
        return match ($role) {
            'hrd' => route('admin.index'),
            'kepala' => route('kepala.dashboard'),
            'pegawai' => route('karyawan.index'),
            default => route('home'),
        };
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
       // Batalkan sesi dan buat ulang token CSRF

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Balik Ke login page
        return redirect()->route('login');
    }
}