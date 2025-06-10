<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\JenisCuti;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function index()
    {
        // Ambil cuti milik pegawai yang sedang login
        $cuti = Cuti::with('jenisCuti')
            ->where('id_pegawai', $this->pegawai->id_pegawai)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('admin.pengajuan_cuti', [
            'cuti' => $cuti,
            'pegawai' => $this->pegawai,
            'nama_departemen' => $this->nama_departemen,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'id_jenis_cuti' => 'required|exists:jenis_cuti,id_jenis_cuti',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'tanggal_pengajuan' => 'required|date',
            'status_cuti' => 'required|in:Menunggu,Disetujui,Ditolak',
            'konfirmasi' => 'required|accepted',
        ]);

        $jenisCuti = JenisCuti::findOrFail($request->id_jenis_cuti);
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);

        // Hitung hari kerja
        $workingDays = 0;
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        if ($workingDays > $jenisCuti->max_hari_cuti) {
            return redirect()->back()
                ->with('error', "Jumlah hari cuti ({$workingDays} hari) melebihi batas maksimal ({$jenisCuti->max_hari_cuti} hari) untuk jenis cuti ini.")
                ->withInput();
        }

        $cuti = new Cuti();
        $cuti->id_pegawai = $request->id_pegawai;
        $cuti->id_jenis_cuti = $request->id_jenis_cuti;
        $cuti->tanggal_pengajuan = $request->tanggal_pengajuan;
        $cuti->tanggal_mulai = $request->tanggal_mulai;
        $cuti->tanggal_selesai = $request->tanggal_selesai;
        $cuti->status_cuti = $request->status_cuti;
        $cuti->keterangan = $request->keterangan;
        $cuti->save();

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim dan sedang menunggu persetujuan.');
    }

    public function show($id)
    {
        $cuti = Cuti::with(['pegawai', 'jenisCuti'])->findOrFail($id);

        if ($this->pegawai->id_pegawai !== $cuti->id_pegawai) {
            return response()->json(['error' => 'Anda tidak berhak mengakses data ini'], 403);
        }

        return response()->json([
            'id_cuti' => $cuti->id_cuti,
            'pegawai' => [
                'nama_pegawai' => $cuti->pegawai->nama_pegawai,
                'nip' => $cuti->pegawai->nip
            ],
            'jenis_cuti' => [
                'nama_jenis_cuti' => $cuti->jenisCuti->nama_jenis_cuti,
                'max_hari_cuti' => $cuti->jenisCuti->max_hari_cuti
            ],
            'tanggal_pengajuan' => $cuti->tanggal_pengajuan,
            'tanggal_mulai' => $cuti->tanggal_mulai,
            'tanggal_selesai' => $cuti->tanggal_selesai,
            'status_cuti' => $cuti->status_cuti,
            'keterangan' => $cuti->keterangan,
        ]);
    }
}
