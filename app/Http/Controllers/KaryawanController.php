<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    // Menampilkan halaman dashboard karyawan
    public function index()
    {
        $user = Auth::user(); // Ambil user yang sedang login

        // Bisa juga kirim data user ke view jika diperlukan
        return view('karyawan.index', compact('user'));
    }
}
