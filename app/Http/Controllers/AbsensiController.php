<?php 
namespace App\Http\Controllers;

use App\Models\Absensi;


class AbsensiController extends Controller
{
    public function index()
{
    $absensi = Absensi::all();

    return view('admin.absensi', [
        'absensi' => $absensi,
        'pegawai' => $this->pegawai,
        'nama_departemen' => $this->nama_departemen,
    ]);
}
    

}