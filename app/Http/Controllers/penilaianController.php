<?php
// File: app/Http/Controllers/PenilaianController.php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Penilaian;
use App\Models\Departemen;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    /**
     * Menampilkan daftar penilaian
     */
    public function index()
    {
        $penilaian = Penilaian::with(['pegawai', 'penilai', 'pertanyaan', 'periode'])->get();
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $nama_departemen = $pegawai->departemen->nama_departemen;
        return view('admin.penilaian', compact('penilaian','pegawai' ,'nama_departemen'));

    }
    public function index2()
    {
        $penilaian = Penilaian::with(['pegawai', 'penilai', 'pertanyaan', 'periode'])->get();
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $nama_departemen = $pegawai->departemen->nama_departemen;
        return view('admin.penilaian-karyawan', compact('penilaian','pegawai' ,'nama_departemen'));

    }

    // /**
    //  * Menampilkan form untuk membuat penilaian baru
    //  */
    // public function create()
    // {
    //     $pegawai = Pegawai::all();
    //     $pertanyaan = Pertanyaan::with('kriteria')->get();
    //     $periode = PeriodePenilaian::where('tanggal_selesai', '>=', now())->get();
        
    //     return view('penilaian.create', compact('pegawai', 'pertanyaan', 'periode'));
    // }

    // /**
    //  * Menyimpan penilaian baru ke database
    //  */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'id_pegawai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
    //         'id_penilai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
    //         'id_pertanyaan' => 'required|exists:hr_darussalam_pertanyaan,id_pertanyaan',
    //         'id_periode' => 'required|exists:hr_darussalam_periode_penilaian,id_periode',
    //         'skor' => 'required|integer|min:1|max:5',
    //         'komentar' => 'nullable|string',
    //         'periode_penilaian' => 'required|string|max:50',
    //         'status' => 'required|in:draft,submitted',
    //     ]);

    //     $penilaian = Penilaian::create($request->all());
        
    //     return redirect()->route('penilaian.index')
    //         ->with('success', 'Penilaian berhasil ditambahkan');
    // }

    // /**
    //  * Menampilkan detail penilaian tertentu
    //  */
    // public function show($id)
    // {
    //     $penilaian = Penilaian::with(['pegawai', 'penilai', 'pertanyaan', 'periode'])->findOrFail($id);
    //     return view('penilaian.show', compact('penilaian'));
    // }

    // /**
    //  * Menampilkan form untuk edit penilaian
    //  */
    // public function edit($id)
    // {
    //     $penilaian = Penilaian::findOrFail($id);
    //     $pegawai = Pegawai::all();
    //     $pertanyaan = Pertanyaan::with('kriteria')->get();
    //     $periode = PeriodePenilaian::all();
        
    //     return view('penilaian.edit', compact('penilaian', 'pegawai', 'pertanyaan', 'periode'));
    // }

    // /**
    //  * Update penilaian di database
    //  */
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'id_pegawai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
    //         'id_penilai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
    //         'id_pertanyaan' => 'required|exists:hr_darussalam_pertanyaan,id_pertanyaan',
    //         'id_periode' => 'required|exists:hr_darussalam_periode_penilaian,id_periode',
    //         'skor' => 'required|integer|min:1|max:5',
    //         'komentar' => 'nullable|string',
    //         'periode_penilaian' => 'required|string|max:50',
    //         'status' => 'required|in:draft,submitted',
    //     ]);

    //     $penilaian = Penilaian::findOrFail($id);
    //     $penilaian->update($request->all());
        
    //     return redirect()->route('penilaian.index')
    //         ->with('success', 'Penilaian berhasil diperbarui');
    // }

    // /**
    //  * Menghapus penilaian dari database
    //  */
    // public function destroy($id)
    // {
    //     $penilaian = Penilaian::findOrFail($id);
    //     $penilaian->delete();
        
    //     return redirect()->route('penilaian.index')
    //         ->with('success', 'Penilaian berhasil dihapus');
    // }
}