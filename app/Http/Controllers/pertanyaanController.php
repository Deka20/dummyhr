<?php 
// File: app/Http/Controllers/PertanyaanController.php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    /**
     * Menampilkan daftar pertanyaan
     */
    public function index()
    {
        $pertanyaan = Pertanyaan::with('kriteria')->get();
        return view('pertanyaan.index', compact('pertanyaan'));
    }

    /**
     * Menampilkan form untuk membuat pertanyaan baru
     */
    public function create()
    {
        $kriteria = Kriteria::all();
        return view('pertanyaan.create', compact('kriteria'));
    }

    /**
     * Menyimpan pertanyaan baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:hr_darussalam_kriteria,id_kriteria',
            'teks_pertanyaan' => 'required|string',
        ]);

        $pertanyaan = Pertanyaan::create($request->all());
        
        return redirect()->route('pertanyaan.index')
            ->with('success', 'Pertanyaan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail pertanyaan tertentu
     */
    public function show($id)
    {
        $pertanyaan = Pertanyaan::with('kriteria')->findOrFail($id);
        return view('pertanyaan.show', compact('pertanyaan'));
    }

    /**
     * Menampilkan form untuk edit pertanyaan
     */
    public function edit($id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $kriteria = Kriteria::all();
        return view('pertanyaan.edit', compact('pertanyaan', 'kriteria'));
    }

    /**
     * Update pertanyaan di database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:hr_darussalam_kriteria,id_kriteria',
            'teks_pertanyaan' => 'required|string',
        ]);

        $pertanyaan = Pertanyaan::findOrFail($id);
        $pertanyaan->update($request->all());
        
        return redirect()->route('pertanyaan.index')
            ->with('success', 'Pertanyaan berhasil diperbarui');
    }

    /**
     * Menghapus pertanyaan dari database
     */
    public function destroy($id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);
        $pertanyaan->delete();
        
        return redirect()->route('pertanyaan.index')
            ->with('success', 'Pertanyaan berhasil dihapus');
    }
}
?>