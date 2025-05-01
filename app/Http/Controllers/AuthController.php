<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class authController extends Controller
{
    /**
     * Show the login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Validate login data
        $request->validate([
            'role' => ['required', 'string', 'in:hrd,kepala,pegawai'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to authenticate with additional condition on role
        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt(array_merge($credentials, ['role' => $request->role]))) {
            // Authentication was successful
            $request->session()->regenerate();
            
            // Redirect to appropriate dashboard based on role
            return $this->redirectTo($request->role);
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

    /**
     * Redirect based on role after successful login.
     *
     * @param  string  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectTo($role)
    {
        return match ($role) {
            'hrd' => redirect()->route('hrd.dashboard'),
            'kepala' => redirect()->route('kepala.dashboard'),
            'pegawai' => redirect()->route('karyawan.index'),
            default => redirect()->route('home'),
        };
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect to login page
        return redirect()->route('login');
    }
}