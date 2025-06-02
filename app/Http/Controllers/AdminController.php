<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai; 
        $nama_jabatan = $pegawai->jabatan->nama_jabatan;
        $nama_departemen = $pegawai->departemen->nama_departemen;
        return view('admin.index', compact('pegawai','nama_departemen','nama_jabatan'));
    }
    
}