<?php 
namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Pastikan pegawai ada
        if (!$pegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan. Silakan hubungi administrator.');
        }
        
        $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A';
        $departemen = Departemen::orderBy('nama_departemen')->get();
        
        // Cek role user
        if ($user->role === 'pegawai') {
            // Jika role pegawai, hanya tampilkan absensi mereka sendiri
            $absensi = Absensi::with(['pegawai', 'pegawai.departemen'])
                              ->where('id_pegawai', $pegawai->id)
                              ->orderBy('tanggal', 'desc')
                              ->get();
            

            
            // Return view khusus untuk pegawai
            return view('karyawan.absensi', compact('absensi','pegawai','nama_departemen','departemen'));
        } else {
            // Jika role admin/lainnya, tampilkan semua absensi
            $absensi = Absensi::with(['pegawai', 'pegawai.departemen'])
                              ->orderBy('tanggal', 'desc')
                              ->get();
            
            // Return view khusus untuk admin
            return view('admin.absensi', compact('absensi','pegawai','nama_departemen','departemen'));
        }
    }
    
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role === 'pegawai') {
            // Jika role pegawai, hanya bisa membuat absensi untuk diri sendiri
            $pegawai = Pegawai::where('id', $user->pegawai->id)->get();
            return view('karyawan.absensi.create', compact('pegawai'));
        } else {
            // Jika role admin, bisa membuat absensi untuk semua pegawai
            $pegawai = Pegawai::all();
            return view('admin.absensi.create', compact('pegawai'));
        }
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
            'status_kehadiran' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'waktu_keluar' => 'nullable|date_format:H:i',
        ]);

        // Jika role pegawai, pastikan hanya bisa membuat absensi untuk diri sendiri
        if ($user->role === 'pegawai' && $request->id_pegawai != $user->pegawai->id) {
            return redirect()->back()->with('error', 'Anda hanya bisa membuat absensi untuk diri sendiri.');
        }

        Absensi::create($request->all());
        
        // Return berdasarkan role
        if ($user->role === 'pegawai') {
            return redirect()->route('karyawan.absensi')
                            ->with('success', 'Data absensi berhasil ditambahkan.');
        } else {
            return redirect()->route('admin.absensi')
                            ->with('success', 'Data absensi berhasil ditambahkan.');
        }
    }
    
    public function edit($id)
    {
        $user = Auth::user();
        $absensi = Absensi::findOrFail($id);
        
        // Jika role pegawai, cek apakah absensi milik mereka
        if ($user->role === 'pegawai' && $absensi->id_pegawai != $user->pegawai->id) {
            return redirect()->route('karyawan.absensi')
                            ->with('error', 'Anda tidak memiliki akses untuk mengedit absensi ini.');
        }
        
        if ($user->role === 'pegawai') {
            $pegawai = Pegawai::where('id', $user->pegawai->id)->get();
            return view('karyawan.absensi.edit', compact('absensi', 'pegawai'));
        } else {
            $pegawai = Pegawai::all();
            return view('admin.absensi.edit', compact('absensi', 'pegawai'));
        }
    }
    
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $absensi = Absensi::findOrFail($id);
        
        // Jika role pegawai, cek apakah absensi milik mereka
        if ($user->role === 'pegawai' && $absensi->id_pegawai != $user->pegawai->id) {
            return redirect()->route('karyawan.absensi')
                            ->with('error', 'Anda tidak memiliki akses untuk mengubah absensi ini.');
        }
        
        $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
            'status_kehadiran' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'waktu_masuk' => 'nullable|date_format:H:i',
            'waktu_keluar' => 'nullable|date_format:H:i',
        ]);

        // Jika role pegawai, pastikan tidak mengubah id_pegawai
        if ($user->role === 'pegawai' && $request->id_pegawai != $user->pegawai->id) {
            return redirect()->back()->with('error', 'Anda hanya bisa mengubah absensi untuk diri sendiri.');
        }

        $absensi->update($request->all());
        
        // Return berdasarkan role
        if ($user->role === 'pegawai') {
            return redirect()->route('karyawan.absensi')
                            ->with('success', 'Data absensi berhasil diperbarui.');
        } else {
            return redirect()->route('admin.absensi')
                            ->with('success', 'Data absensi berhasil diperbarui.');
        }
    }
    
    public function destroy($id)
    {
        $user = Auth::user();
        $absensi = Absensi::findOrFail($id);
        
        // Jika role pegawai, cek apakah absensi milik mereka
        if ($user->role === 'pegawai' && $absensi->id_pegawai != $user->pegawai->id) {
            return redirect()->route('karyawan.absensi')
                            ->with('error', 'Anda tidak memiliki akses untuk menghapus absensi ini.');
        }
        
        $absensi->delete();
        
        // Redirect berdasarkan role user
        $redirectRoute = $user->role === 'pegawai' ? 'karyawan.absensi' : 'admin.absensi';
        return redirect()->route($redirectRoute)
                        ->with('success', 'Data absensi berhasil dihapus.');
    }
    
    // Method untuk API jika diperlukan
    public function apiIndex()
    {
        $user = Auth::user();
        
        if ($user->role === 'pegawai') {
            $absensi = Absensi::with(['pegawai', 'pegawai.departemen'])
                              ->where('id_pegawai', $user->pegawai->id)
                              ->get();
        } else {
            $absensi = Absensi::with(['pegawai', 'pegawai.departemen'])->get();
        }
        
        return response()->json($absensi);
    }
    
    public function apiShow($id)
    {
        $user = Auth::user();
        $absensi = Absensi::with(['pegawai', 'pegawai.departemen'])->findOrFail($id);
        
        // Jika role pegawai, cek apakah absensi milik mereka
        if ($user->role === 'pegawai' && $absensi->id_pegawai != $user->pegawai->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($absensi);
    }
    
    // Untuk AJAX request
    public function getDepartemen()
    {
        $departemen = Departemen::select('id_departemen', 'nama_departemen', 'kepala_departemen')
                                ->orderBy('nama_departemen')
                                ->get();
        
        return response()->json($departemen);
    }
    
    // Untuk mendapatkan departemen dengan jumlah pegawai
    public function getDepartemenWithCount()
    {
        $departemen = Departemen::withCount('pegawai')
                                ->orderBy('nama_departemen')
                                ->get();
        
        return view('your-view', compact('departemen'));
    }
}