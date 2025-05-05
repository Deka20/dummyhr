<?php 
// File: app/Http/Controllers/KriteriaController.php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Menampilkan daftar kriteria
     */
    public function index()
    {
        $kriteria = Kriteria::all();
        return view('kriteria.index', compact('kriteria'));
    }

    /**
     * Menampilkan form untuk membuat kriteria baru
     */
    public function create()
    {
        return view('kriteria.create');
    }

    /**
     * Menyimpan kriteria baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:100|unique:hr_darussalam_kriteria',
        ]);

        $kriteria = Kriteria::create($request->all());
        
        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan');
    }

    /**
     * Menampilkan detail kriteria tertentu
     */
    public function show($id)
    {
        $kriteria = Kriteria::with('pertanyaan')->findOrFail($id);
        return view('kriteria.show', compact('kriteria'));
    }

    /**
     * Menampilkan form untuk edit kriteria
     */
    public function edit($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('kriteria.edit', compact('kriteria'));
    }

    /**
     * Update kriteria di database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:100|unique:hr_darussalam_kriteria,nama_kriteria,'.$id.',id_kriteria',
        ]);

        $kriteria = Kriteria::findOrFail($id);
        $kriteria->update($request->all());
        
        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui');
    }

    /**
     * Menghapus kriteria dari database
     */
    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();
        
        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus');
    }
}

?>