<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class KaryawanController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->pegawai;
        return view('admin.karyawan', [
            'karyawan' => Pegawai::all(),
            'pegawai' => $pegawai,
            'nama_departemen' => $pegawai->departemen->nama_departemen ?? '',
            'departemen' => Departemen::all(),
            'jabatan' => Jabatan::all(),
        ]);
    }

    public function create()
    {
        return view('data_karyawan', [
            'jabatan' => Jabatan::all(),
            'departemen' => Departemen::all(),
            'pegawai' => Pegawai::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:pegawai,email',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'tanggal_masuk' => 'required|date',
            'jatahtahunan' => 'required|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            if ($request->hasFile('foto')) {
                $validated['foto'] = $request->file('foto')->store('uploads/pegawai', 'public');
            }

            Pegawai::create($validated);

            return redirect('/hrd/kelola-karyawan')->with([
                'notifikasi' => 'Data Pegawai berhasil ditambahkan!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan pegawai: ' . $e->getMessage());

            if (!empty($validated['foto'])) {
                Storage::disk('public')->delete($validated['foto']);
            }

            return back()->withInput()->with([
                'notifikasi' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'type' => 'danger'
            ]);
        }
    }

     public function show($id)
    {
        $pegawai = Pegawai::with(['jabatan', 'departemen'])->findOrFail($id);
        return view('pegawai.show', compact('pegawai'));
    }

    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('admin.edit-pegawai', [
            'pegawai' => $pegawai,
            'jabatan' => Jabatan::all(),
            'departemen' => Departemen::all(),
            'nama_departemen' => $pegawai->departemen->nama_departemen ?? '',
            'departemen' => Departemen::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $pegawai = Pegawai::findOrFail($id);
            
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat' => 'required|string',
                'no_hp' => 'required|string|max:20',
                'email' => 'required|email|max:255|unique:pegawai,email,' . $id . ',id_pegawai',
                'id_jabatan' => 'nullable|exists:jabatan,id_jabatan',
                'id_departemen' => 'required|exists:departemen,id_departemen',
                'tanggal_masuk' => 'required|date',
                'jatahtahunan' => 'nullable|integer|min:0',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle foto upload
           if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                $oldFilePath = public_path('uploads/pegawai/' . $pegawai->foto);
                if ($pegawai->foto && file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Upload foto baru ke folder public/uploads/pegawai
                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pegawai'), $fileName); // <-- perbaiki ini

                $validated['foto'] = $fileName;
            }



            // Update data pegawai 
            $pegawai->update($validated);

            return redirect()->route('admin.karyawan')->with([
                'notifikasi' => 'Data pegawai berhasil diperbarui!',
                'type' => 'success'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with([
                'notifikasi' => 'Terdapat kesalahan dalam validasi data!',
                'type' => 'error'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate pegawai: ' . $e->getMessage());
            
            // Clean up newly uploaded photo if there's an error
            if ($request->hasFile('foto') && isset($validated['foto'])) {
                Storage::disk('public')->delete('fotos/' . $validated['foto']);
            }

            return back()->withInput()->with([
                'notifikasi' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

}