<?php 
// File: app/Http/Controllers/PeriodePenilaianController.php

namespace App\Http\Controllers;

use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;

class PeriodePenilaianController extends Controller
{
    /**
     * Menampilkan daftar periode penilaian
     */
    public function index()
    {
        $periode = PeriodePenilaian::all();
        return view('periode.index', compact('periode'));
    }

    /**
     * Menampilkan form untuk membuat periode baru
     */
    public function create()
    {
        return view('periode.create');
    }

    /**
     * Menyimpan periode baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $periode = PeriodePenilaian::create($request->all());
        
        return redirect()->route('periode.index')
            ->with('success', 'Periode penilaian berhasil ditambahkan');
    }

    /**
     * Menampilkan detail periode tertentu
     */
    public function show($id)
    {
        $periode = PeriodePenilaian::with('penilaian')->findOrFail($id);
        return view('periode.show', compact('periode'));
    }

    /**
     * Menampilkan form untuk edit periode
     */
    public function edit($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);
        return view('periode.edit', compact('periode'));
    }

    /**
     * Update periode di database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $periode = PeriodePenilaian::findOrFail($id);
        $periode->update($request->all());
        
        return redirect()->route('periode.index')
            ->with('success', 'Periode penilaian berhasil diperbarui');
    }

    /**
     * Menghapus periode dari database
     */
    public function destroy($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);
        $periode->delete();
        
        return redirect()->route('periode.index')
            ->with('success', 'Periode penilaian berhasil dihapus');
    }
}
?>