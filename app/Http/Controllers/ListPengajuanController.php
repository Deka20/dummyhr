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

        // Query data cuti dengan FIFO - HANYA TAMPILKAN YANG MENUNGGU
        $query = Cuti::with(['pegawai.departemen', 'pegawai.jabatan', 'jenisCuti'])
            ->where('status_cuti', 'Menunggu') // HANYA AMBIL YANG MENUNGGU
            ->orderBy('tanggal_pengajuan', 'ASC'); // FIFO: Yang pertama masuk, pertama keluar

        // Filter tambahan (opsional, karena sudah filtered ke Menunggu)
        if ($request->filled('departemen')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen);
            });
        }
        if ($request->filled('jenis_cuti')) {
            $query->where('id_jenis_cuti', $request->jenis_cuti);
        }

        $pengajuan_cuti = $query->get();

        // Hitung jumlah hari cuti dan nomor antrian FIFO
        $nomor_antrian = 1;
        foreach ($pengajuan_cuti as $index => $cuti) {
            if ($cuti->tanggal_mulai && $cuti->tanggal_selesai) {
                $cuti->jumlah_hari = Carbon::parse($cuti->tanggal_mulai)
                    ->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
            } else {
                $cuti->jumlah_hari = 0;
            }
            
            // Semua pengajuan yang ditampilkan adalah "Menunggu", jadi semua dapat nomor antrian
            $cuti->nomor_antrian = $nomor_antrian++;
        }

        // Statistik - Hitung dari semua data, bukan hanya yang ditampilkan
        $all_cuti = Cuti::all();
        $pending = $all_cuti->where('status_cuti', 'Menunggu')->count();
        $approved = $all_cuti->where('status_cuti', 'Disetujui')->count();
        $rejected = $all_cuti->where('status_cuti', 'Ditolak')->count();
        $total = $all_cuti->count();

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
                'keterangan' => 'nullable|string|max:255',
            ]);

            $cuti = Cuti::findOrFail($id);
            
            // Check if cuti is still pending
            if ($cuti->status_cuti !== 'Menunggu') {
                return redirect()->back()->with('error', 'Pengajuan cuti sudah diproses sebelumnya.');
            }

            // FIFO Validation: Pastikan tidak ada pengajuan yang lebih lama masih menunggu
            if ($request->status === 'Disetujui') {
                $pengajuan_lebih_lama = Cuti::where('status_cuti', 'Menunggu')
                    ->where('tanggal_pengajuan', '<', $cuti->tanggal_pengajuan)
                    ->exists();
                
                if ($pengajuan_lebih_lama) {
                    return redirect()->back()->with('error', 'Tidak dapat menyetujui pengajuan ini. Masih ada pengajuan yang lebih lama menunggu validasi (FIFO Policy).');
                }
            }

            $cuti->status_cuti = $request->status;
            
            // Only add keterangan if provided and status is rejected
            if ($request->status === 'Ditolak' && $request->filled('keterangan')) {
                $cuti->keterangan = $request->keterangan;
            }
            
            $cuti->save();

            $statusText = $request->status === 'Disetujui' ? 'disetujui' : 'ditolak';
            return redirect()->back()->with('success', "Pengajuan cuti berhasil {$statusText}. Pengajuan akan dihapus dari antrian.");
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating cuti status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pengajuan cuti. Silakan coba lagi.');
        }
    }

    // Method baru untuk melihat riwayat pengajuan yang sudah diproses
    public function riwayat(Request $request)
    {
        $query = Cuti::with(['pegawai.departemen', 'pegawai.jabatan', 'jenisCuti'])
            ->whereIn('status_cuti', ['Disetujui', 'Ditolak'])
            ->orderBy('updated_at', 'DESC'); // Urutkan berdasarkan tanggal update terbaru

        if ($request->filled('status')) {
            $query->where('status_cuti', $request->status);
        }
        if ($request->filled('departemen')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen);
            });
        }

        $riwayat_cuti = $query->get();

        // Hitung jumlah hari cuti
        foreach ($riwayat_cuti as $cuti) {
            if ($cuti->tanggal_mulai && $cuti->tanggal_selesai) {
                $cuti->jumlah_hari = Carbon::parse($cuti->tanggal_mulai)
                    ->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
            } else {
                $cuti->jumlah_hari = 0;
            }
        }

        $departemen = Departemen::all();
        
        return view('admin.riwayatPengajuan', compact('riwayat_cuti', 'departemen'));
    }

    public function show($id)
    {
        try {
            $cuti = Cuti::with(['pegawai.departemen', 'pegawai.jabatan', 'jenisCuti'])
                        ->findOrFail($id);
            
            // kalkulasi durasi
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

    /**
     * Get next pengajuan in FIFO queue
     */
    public function getNextFifo()
    {
        $next_cuti = Cuti::with(['pegawai.departemen', 'pegawai.jabatan', 'jenisCuti'])
            ->where('status_cuti', 'Menunggu')
            ->orderBy('tanggal_pengajuan', 'ASC')
            ->first();

        if (!$next_cuti) {
            return response()->json(['message' => 'Tidak ada pengajuan cuti dalam antrian']);
        }

        return response()->json($next_cuti);
    }

    /**
     * Get FIFO queue posisi untuk cuti
     */
    public function getFifoPosition($id)
    {
        $cuti = Cuti::findOrFail($id);
        
        if ($cuti->status_cuti !== 'Menunggu') {
            return response()->json(['message' => 'Pengajuan cuti tidak dalam antrian']);
        }

        $position = Cuti::where('status_cuti', 'Menunggu')
            ->where('tanggal_pengajuan', '<', $cuti->tanggal_pengajuan)
            ->count() + 1;

        return response()->json(['position' => $position]);
    }
}