<?php
namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index', [
            'pegawai' => $this->pegawai,
            'nama_departemen' => $this->nama_departemen,
            'nama_jabatan' => $this->nama_jabatan,
        ]);
    }
}
