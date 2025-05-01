<?php 
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Pegawai;

class KaryawanController extends Controller
{
    // Menampilkan daftar karyawan
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai; // Ambil data pegawai
    
        $karyawan = Pegawai::all();
        $nama_departemen = $pegawai->departemen->nama_departemen;
    
        return view('admin.karyawan', compact('karyawan', 'pegawai' ,'nama_departemen'));
    }
    

}
?>