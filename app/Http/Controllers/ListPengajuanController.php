<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Departemen;
use App\Models\JenisCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ListPengajuanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user yang login
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $nama_departemen = $pegawai && $pegawai->departemen ? $pegawai->departemen->nama_departemen : 'Tidak diketahui';

        // Query data cuti
        $query = Cuti::with(['pegawai.departemen', 'pegawai.jabatan', 'jenisCuti'])
            ->latest('tanggal_pengajuan');

        if ($request->filled('status')) {
            $query->where('status_cuti', $request->status);
        }
        if ($request->filled('departemen')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen);
            });
        }
        if ($request->filled('jenis_cuti')) {
            $query->where('id_jenis_cuti', $request->jenis_cuti);
        }

        $pengajuan_cuti = $query->get();

        // Hitung jumlah hari cuti
        foreach ($pengajuan_cuti as $cuti) {
            if ($cuti->tanggal_mulai && $cuti->tanggal_selesai) {
                $cuti->jumlah_hari = Carbon::parse($cuti->tanggal_mulai)
                    ->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
            } else {
                $cuti->jumlah_hari = 0;
            }
        }

        // Statistik
        $pending = $pengajuan_cuti->where('status_cuti', 'Menunggu')->count();
        $approved = $pengajuan_cuti->where('status_cuti', 'Disetujui')->count();
        $rejected = $pengajuan_cuti->where('status_cuti', 'Ditolak')->count();
        $total = $pengajuan_cuti->count();

        // Dropdown data
        $departemen = Departemen::all();
        $jenis_cuti_options = JenisCuti::pluck('nama_jenis_cuti', 'id_jenis_cuti');

        return view('admin.listPengajuan', compact(
            'pegawai',
            'nama_departemen',
            'pengajuan_cuti',
            'departemen',
            'jenis_cuti_options',
            'pending',
            'approved',
            'rejected',
            'total'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:Disetujui,Ditolak,Menunggu',
                'keterangan' => 'nullable|string|max:500',
            ]);

            $cuti = Cuti::findOrFail($id);
            
            // Check if cuti is still pending
            if ($cuti->status_cuti !== 'Menunggu') {
                return redirect()->back()->with('error', 'Pengajuan cuti sudah diproses sebelumnya.');
            }

            $cuti->status_cuti = $request->status;
            
            // Only add keterangan if provided or if status is rejected
            if ($request->status === 'Ditolak') {
                $cuti->keterangan = $request->keterangan ?: 'Pengajuan cuti ditolak';
            } elseif ($request->filled('keterangan')) {
                $cuti->keterangan = $request->keterangan;
            }
            
            $cuti->tanggal_validasi = now();
            $cuti->validator_id = Auth::id(); // Add validator ID if you have this field
            $cuti->save();

            $statusText = $request->status === 'Disetujui' ? 'disetujui' : 'ditolak';
            return redirect()->back()->with('success', "Pengajuan cuti berhasil {$statusText}.");
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating cuti status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pengajuan cuti. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        try {
            $cuti = Cuti::with(['pegawai.departemen', 'pegawai.jabatan', 'jenisCuti'])
                        ->findOrFail($id);
            
            // Calculate duration
            if ($cuti->tanggal_mulai && $cuti->tanggal_selesai) {
                $cuti->jumlah_hari = Carbon::parse($cuti->tanggal_mulai)
                    ->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
            } else {
                $cuti->jumlah_hari = 0;
            }

            return response()->json($cuti);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }
}