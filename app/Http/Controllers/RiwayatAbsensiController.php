<?php 
namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatAbsensiController extends Controller
{
    public function index()
{
    $user = Auth::user();

    if (!$user->pegawai) {
        return redirect()->back()->with('error', 'Anda tidak memiliki data pegawai. Silakan hubungi administrator.');
    }

    $pegawai = $user->pegawai; // Sudah otomatis terhubung via relasi
    $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A';
    $departemen = Departemen::orderBy('nama_departemen')->get();

    // Ambil data absensi berdasarkan id_pegawai yang terhubung dengan user
    $absensi = Absensi::with(['pegawai', 'pegawai.departemen'])
                      ->where('id_pegawai', $pegawai->id_pegawai)
                      ->orderBy('tanggal', 'desc')
                      ->get();

    // Pilih view berdasarkan role user
    $viewName = $user->role === 'pegawai' ? 'karyawan.absensi' : 'admin.RiwayatAbsensi';

    return view($viewName, compact('absensi', 'pegawai', 'nama_departemen', 'departemen'));
}
}