<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Dapatkan user yang sedang login
        $pegawai = $user->pegawai; // Ambil data pegawai terkait
        $nama_departemen = $pegawai->departemen->nama_departemen;
        return view('karyawan.index', compact('pegawai','nama_departemen'));
    }
}

