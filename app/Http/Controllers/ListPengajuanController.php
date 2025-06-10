<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\Departemen;
use App\Models\JenisCuti;
use Carbon\Carbon;

class ListPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $cutiQuery = Cuti::with(['pegawai', 'jenisCuti']);

        if ($request->filled('status')) {
            $cutiQuery->where('status_cuti', $request->status);
        }

        if ($request->filled('departemen')) {
            $cutiQuery->whereHas('pegawai', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen);
            });
        }

        if ($request->filled('jenis_cuti')) {
            $cutiQuery->where('id_jenis_cuti', $request->jenis_cuti);
        }

        $pengajuan_cuti = $cutiQuery
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(10);

        foreach ($pengajuan_cuti as $cuti) {
            if ($cuti->tanggal_mulai && $cuti->tanggal_selesai) {
                $cuti->durasi = Carbon::parse($cuti->tanggal_mulai)
                    ->diffInDays(Carbon::parse($cuti->tanggal_selesai)) + 1;
            }
        }

        $departemen = Departemen::all();
        $jenis_cuti_options = JenisCuti::pluck('nama_jenis_cuti', 'id_jenis_cuti');

        return view('admin.listPengajuan', [
            'pengajuan_cuti' => $pengajuan_cuti,
            'departemen' => $departemen,
            'jenis_cuti_options' => $jenis_cuti_options,
            'pegawai' => $this->pegawai,
            'nama_departemen' => $this->nama_departemen,
            'nama_jabatan' => $this->nama_jabatan,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak,Menunggu',
            'keterangan' => 'nullable|string|max:500'
        ]);

        $cuti = Cuti::findOrFail($id);
        $cuti->status_cuti = $request->status;
        $cuti->keterangan = $request->keterangan;
        $cuti->save();

        return response()->json(['success' => true, 'message' => 'Status diperbarui.']);
    }
}
