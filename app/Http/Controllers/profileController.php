<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;

class ProfileController extends Controller
{
   public function edit()
{
     $user = Auth::user();
        $pegawai = $user->pegawai; 
        $nama_jabatan = $pegawai->jabatan->nama_jabatan;
        $nama_departemen = $pegawai->departemen->nama_departemen;
        return view('admin.edit-profil', compact('pegawai','nama_departemen','nama_jabatan'));


}


    public function update(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'tempat_lahir' => 'nullable|string|max:100',
        'tanggal_lahir' => 'nullable|date',
        'jenis_kelamin' => 'nullable|in:L,P',
        'alamat' => 'nullable|string|max:255',
        'no_hp' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $pegawai = Auth::user()->pegawai;

    if (!$pegawai) {
        return redirect()->back()->with('error', 'Data pegawai tidak ditemukan.');
    }

    $data = $request->only([
        'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'alamat', 'no_hp', 'email'
    ]);

    // Handle upload foto jika ada
    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $namaFile = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/pegawai', 'public'), $namaFile);
        $data['foto'] = $namaFile;
    }

    $pegawai->update($data);

    return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
}
}

?>