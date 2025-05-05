<?php 
namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user || !$user->pegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan');
        }
        
        $pegawai = $user->pegawai;
        

        $absensi = Absensi::all();
        
        $nama_departemen = $pegawai->departemen ? $pegawai->departemen->nama_departemen : 'Tidak ada departemen';
        
        // Sesuaikan nama view yang seharusnya 'admin.absensi' karena ini AbsensiController
        return view('admin.absensi', compact('absensi', 'pegawai', 'nama_departemen'));
    }
    

}