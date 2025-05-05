<?php
// File: app/Http/Controllers/PenilaianController.php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Pegawai;
use App\Models\Pertanyaan;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    /**
     * Menampilkan daftar penilaian
     */
    public function index()
    {
        $penilaian = Penilaian::with(['pegawai', 'penilai', 'pertanyaan', 'periode'])->get();
        return view('penilaian.index', compact('penilaian'));
    }

    /**
     * Menampilkan form untuk membuat penilaian baru
     */
    public function create()
    {
        $pegawai = Pegawai::all();
        $pertanyaan = Pertanyaan::with('kriteria')->get();
        $periode = PeriodePenilaian::where('tanggal_selesai', '>=', now())->get();
        
        return view('penilaian.create', compact('pegawai', 'pertanyaan', 'periode'));
    }

    /**
     * Menyimpan penilaian baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pegawai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
            'id_penilai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
            'id_pertanyaan' => 'required|exists:hr_darussalam_pertanyaan,id_pertanyaan',
            'id_periode' => 'required|exists:hr_darussalam_periode_penilaian,id_periode',
            'skor' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
            'periode_penilaian' => 'required|string|max:50',
            'status' => 'required|in:draft,submitted',
        ]);

        $penilaian = Penilaian::create($request->all());
        
        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil ditambahkan');
    }

    /**
     * Menampilkan detail penilaian tertentu
     */
    public function show($id)
    {
        $penilaian = Penilaian::with(['pegawai', 'penilai', 'pertanyaan', 'periode'])->findOrFail($id);
        return view('penilaian.show', compact('penilaian'));
    }

    /**
     * Menampilkan form untuk edit penilaian
     */
    public function edit($id)
    {
        $penilaian = Penilaian::findOrFail($id);
        $pegawai = Pegawai::all();
        $pertanyaan = Pertanyaan::with('kriteria')->get();
        $periode = PeriodePenilaian::all();
        
        return view('penilaian.edit', compact('penilaian', 'pegawai', 'pertanyaan', 'periode'));
    }

    /**
     * Update penilaian di database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_pegawai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
            'id_penilai' => 'required|exists:hr_darussalam_pegawai,id_pegawai',
            'id_pertanyaan' => 'required|exists:hr_darussalam_pertanyaan,id_pertanyaan',
            'id_periode' => 'required|exists:hr_darussalam_periode_penilaian,id_periode',
            'skor' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
            'periode_penilaian' => 'required|string|max:50',
            'status' => 'required|in:draft,submitted',
        ]);

        $penilaian = Penilaian::findOrFail($id);
        $penilaian->update($request->all());
        
        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil diperbarui');
    }

    /**
     * Menghapus penilaian dari database
     */
    public function destroy($id)
    {
        $penilaian = Penilaian::findOrFail($id);
        $penilaian->delete();
        
        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil dihapus');
    }
}