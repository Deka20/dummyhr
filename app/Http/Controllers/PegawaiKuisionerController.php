<?php

namespace App\Http\Controllers;

use App\Models\Kuisioner;
use App\Models\JawabanKuisioner;
use App\Models\PeriodePenilaian;
use App\Models\Penilaian;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PegawaiKuisionerController extends Controller
{
    /**
     * Tampilkan halaman pemilihan untuk kuisioner
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $pegawai = $user->pegawai;
            
            if (!$pegawai) {
                return redirect()->back()->with('error', 'Data pegawai tidak ditemukan.');
            }
            
            $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A';
            
            // Ambil semua departemen untuk dropdown
            $departemen = Departemen::orderBy('nama_departemen', 'asc')->get();
            
            // Ambil semua periode penilaian yang aktif dengan pengurutan
            $periode = PeriodePenilaian::where('status', 'aktif')
                ->orderBy('tahun', 'desc')
                ->orderBy('semester', 'desc')
                ->get();
            
            // Ambil tahun ajaran yang tersedia (distinct dari periode aktif)
            $tahunAjaran = PeriodePenilaian::select('tahun')
                ->where('status', 'aktif')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->get();

            return view('karyawan.kuisioner.index', compact(
                'pegawai',
                'nama_departemen',
                'departemen',
                'periode',
                'tahunAjaran'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in kuisioner index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman.');
        }
    }

    /**
     * Method untuk mendapatkan pegawai berdasarkan departemen (AJAX)
     */
    public function getPegawaiByDepartemen($departemenId)
    {
        try {
            // Validasi departemen exists
            $departemen = Departemen::find($departemenId);
            if (!$departemen) {
                return response()->json(['error' => 'Departemen tidak ditemukan'], 404);
            }

            // Ambil pegawai dengan relasi user dan departemen
            $pegawai = Pegawai::where('id_departemen', $departemenId)
                ->with(['user', 'departemen'])
                ->whereHas('user') // Pastikan pegawai memiliki user account
                ->orderBy('nama', 'asc')
                ->get();
            
            // Transform data untuk response
            $pegawaiData = $pegawai->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'jabatan' => $p->jabatan ?? 'Dosen/Laboran',
                    'email' => $p->user->email ?? 'N/A',
                    'departemen' => $p->departemen->nama_departemen ?? 'N/A',
                    'user' => [
                        'id_user' => $p->user->id_user ?? null,
                        'email' => $p->user->email ?? null,
                        'name' => $p->user->name ?? $p->nama
                    ]
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $pegawaiData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting pegawai by departemen: ' . $e->getMessage());
            Log::error('Departemen ID: ' . $departemenId);
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data pegawai: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Method untuk mendapatkan periode berdasarkan tahun (AJAX)
     */
    public function getPeriodeByTahun($tahun)
    {
        try {
            $periode = PeriodePenilaian::where('tahun', $tahun)
                ->where('status', 'aktif')
                ->orderBy('semester', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $periode
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting periode by tahun: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data periode'
            ], 500);
        }
    }

    /**
     * Method untuk cek status kuisioner dengan detail
     */
    public function checkKuisionerStatus(Request $request)
    {
        try {
            $periodeId = $request->get('periode_id');
            $dinilaiId = $request->get('dinilai_id');
            $penilaiId = Auth::id();

            // Validasi input
            if (!$periodeId || !$dinilaiId) {
                return response()->json(['error' => 'Parameter tidak lengkap'], 400);
            }

            // Cek apakah sudah ada penilaian
            $penilaian = Penilaian::where('periode_id', $periodeId)
                ->where('dinilai_id_user', $dinilaiId)
                ->where('penilai_id_user', $penilaiId)
                ->first();

            $status = 'belum_mulai';
            $progress = 0;
            $totalKuisioner = 0;
            $sudahDiisi = 0;
            $komentar = null;

            // Get total kuisioner dari periode
            $periode = PeriodePenilaian::with('kuisioner')->find($periodeId);
            if ($periode) {
                $totalKuisioner = $periode->kuisioner->count();
            }

            if ($penilaian) {
                $sudahDiisi = $penilaian->jawabanKuisioner()->count();
                $progress = $totalKuisioner > 0 ? round(($sudahDiisi / $totalKuisioner) * 100) : 0;
                $komentar = $penilaian->komentar;
                
                if ($progress == 100) {
                    $status = 'selesai';
                } elseif ($progress > 0) {
                    $status = 'berlangsung';
                }
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'progress' => $progress,
                'total_kuisioner' => $totalKuisioner,
                'sudah_diisi' => $sudahDiisi,
                'komentar' => $komentar,
                'penilaian_id' => $penilaian ? $penilaian->id : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking kuisioner status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengecek status kuisioner'
            ], 500);
        }
    }

    /**
     * Method untuk mendapatkan status multiple pegawai sekaligus
     */
    public function getMultipleStatus(Request $request)
    {
        try {
            $periodeId = $request->get('periode_id');
            $pegawaiIds = $request->get('pegawai_ids', []);
            $penilaiId = Auth::id();

            if (!$periodeId || empty($pegawaiIds)) {
                return response()->json(['error' => 'Parameter tidak lengkap'], 400);
            }

            $statuses = [];
            foreach ($pegawaiIds as $dinilaiId) {
                $penilaian = Penilaian::where('periode_id', $periodeId)
                    ->where('dinilai_id_user', $dinilaiId)
                    ->where('penilai_id_user', $penilaiId)
                    ->first();

                $status = 'belum_mulai';
                $progress = 0;

                if ($penilaian) {
                    $periode = PeriodePenilaian::with('kuisioner')->find($periodeId);
                    $totalKuisioner = $periode ? $periode->kuisioner->count() : 0;
                    $sudahDiisi = $penilaian->jawabanKuisioner()->count();
                    $progress = $totalKuisioner > 0 ? round(($sudahDiisi / $totalKuisioner) * 100) : 0;
                    
                    if ($progress == 100) {
                        $status = 'selesai';
                    } elseif ($progress > 0) {
                        $status = 'berlangsung';
                    }
                }

                $statuses[$dinilaiId] = [
                    'status' => $status,
                    'progress' => $progress
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $statuses
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting multiple status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil status'
            ], 500);
        }
    }

    /**
     * Tampilkan form kuisioner
     */
    public function show($periodeId, $dinilaiId)
    {
        try {
            $penilaiId = Auth::id();
            $user = Auth::user();
            $pegawai = $user->pegawai;
            
            if (!$pegawai) {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Data pegawai tidak ditemukan.');
            }
            
            $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A';
            
            // Validasi periode
            $periode = PeriodePenilaian::with('kuisioner')->find($periodeId);
            if (!$periode) {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Periode penilaian tidak ditemukan.');
            }

            if ($periode->status !== 'aktif') {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Periode penilaian tidak aktif.');
            }
            
            // Validasi user yang dinilai
            $dinilai = User::find($dinilaiId);
            if (!$dinilai) {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Pegawai yang akan dinilai tidak ditemukan.');
            }
            
            // Cek apakah penilaian sudah ada atau buat baru
            $penilaian = Penilaian::firstOrCreate(
                [
                    'periode_id' => $periodeId,
                    'dinilai_id_user' => $dinilaiId,
                    'penilai_id_user' => $penilaiId,
                ],
                [
                    'status' => 'belum_diisi',
                    'total_nilai' => 0,
                ]
            );

            // Ambil jawaban kuisioner yang sudah ada
            $existingAnswers = JawabanKuisioner::where('penilaian_id', $penilaian->id)
                ->pluck('skor', 'kuisioner_id');

            // Group kuisioner by kategori
            $kuisionerByKategori = $periode->kuisioner->groupBy('kategori');

            return view('karyawan.kuisioner.form', compact(
                'periode',
                'kuisionerByKategori',
                'existingAnswers',
                'dinilaiId',
                'pegawai',
                'dinilai',
                'nama_departemen',
                'penilaian'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error showing kuisioner form: ' . $e->getMessage());
            return redirect()->route('kuisioner.index')
                ->with('error', 'Terjadi kesalahan saat memuat form kuisioner.');
        }
    }

    /**
     * Simpan jawaban kuisioner
     */
    public function store(Request $request, $periodeId, $dinilaiId)
    {
        // Validasi input
        $request->validate([
            'jawaban' => 'required|array|min:1',
            'jawaban.*' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000'
        ], [
            'jawaban.required' => 'Jawaban kuisioner harus diisi',
            'jawaban.array' => 'Format jawaban tidak valid',
            'jawaban.min' => 'Minimal satu jawaban harus diisi',
            'jawaban.*.required' => 'Semua pertanyaan harus dijawab',
            'jawaban.*.integer' => 'Jawaban harus berupa angka',
            'jawaban.*.min' => 'Nilai jawaban minimal 1',
            'jawaban.*.max' => 'Nilai jawaban maksimal 5',
            'komentar.max' => 'Komentar maksimal 1000 karakter'
        ]);

        $penilaiId = Auth::id();

        DB::beginTransaction();
        try {
            // Validasi periode masih aktif
            $periode = PeriodePenilaian::find($periodeId);
            if (!$periode || $periode->status !== 'aktif') {
                throw new \Exception('Periode penilaian tidak aktif.');
            }

            // Validasi user yang dinilai
            $dinilai = User::find($dinilaiId);
            if (!$dinilai) {
                throw new \Exception('Pegawai yang dinilai tidak ditemukan.');
            }

            // Cari atau buat penilaian
            $penilaian = Penilaian::firstOrCreate(
                [
                    'periode_id' => $periodeId,
                    'dinilai_id_user' => $dinilaiId,
                    'penilai_id_user' => $penilaiId,
                ],
                [
                    'status' => 'belum_diisi',
                    'total_nilai' => 0,
                ]
            );

            // Simpan atau update jawaban
            foreach ($request->input('jawaban') as $kuisionerId => $skor) {
                // Validasi kuisioner exists dan belongs to periode
                $kuisioner = Kuisioner::where('id', $kuisionerId)
                    ->whereHas('periodePenilaian', function($q) use ($periodeId) {
                        $q->where('id', $periodeId);
                    })->first();
                
                if (!$kuisioner) {
                    throw new \Exception("Kuisioner dengan ID {$kuisionerId} tidak valid untuk periode ini.");
                }

                JawabanKuisioner::updateOrCreate(
                    [
                        'penilaian_id' => $penilaian->id,
                        'kuisioner_id' => $kuisionerId,
                    ],
                    ['skor' => $skor]
                );
            }

            // Update komentar
            if ($request->has('komentar')) {
                $penilaian->komentar = $request->input('komentar');
            }

            // Update total nilai dan status
            $this->updatePenilaianStatus($penilaian, $periode);
            
            DB::commit();
            
            // Check if AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jawaban berhasil disimpan!',
                    'status' => $penilaian->status,
                    'progress' => $this->calculateProgress($penilaian, $periode)
                ]);
            }
            
            return redirect()->back()->with('success', 'Jawaban berhasil disimpan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing kuisioner: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan jawaban: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menyimpan jawaban: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Helper method untuk update status penilaian
     */
    private function updatePenilaianStatus($penilaian, $periode)
    {
        // Hitung total nilai
        $totalNilai = $penilaian->jawabanKuisioner()->sum('skor');
        $penilaian->total_nilai = $totalNilai;
        
        // Update status berdasarkan progress
        $totalKuisioner = $periode->kuisioner->count();
        $sudahDiisi = $penilaian->jawabanKuisioner()->count();
        
        if ($sudahDiisi >= $totalKuisioner) {
            $penilaian->status = 'selesai';
        } elseif ($sudahDiisi > 0) {
            $penilaian->status = 'berlangsung';
        } else {
            $penilaian->status = 'belum_diisi';
        }
        
        $penilaian->save();
    }

    /**
     * Helper method untuk hitung progress
     */
    private function calculateProgress($penilaian, $periode)
    {
        $totalKuisioner = $periode->kuisioner->count();
        $sudahDiisi = $penilaian->jawabanKuisioner()->count();
        
        return $totalKuisioner > 0 ? round(($sudahDiisi / $totalKuisioner) * 100) : 0;
    }

    /**
     * Reset jawaban kuisioner
     */
    public function reset($periodeId, $dinilaiId)
    {
        try {
            $penilaiId = Auth::id();

            DB::beginTransaction();

            $penilaian = Penilaian::where('periode_id', $periodeId)
                ->where('penilai_id_user', $penilaiId)
                ->where('dinilai_id_user', $dinilaiId)
                ->first();

            if ($penilaian) {
                // Hapus semua jawaban
                $penilaian->jawabanKuisioner()->delete();
                
                // Reset status dan nilai
                $penilaian->status = 'belum_diisi';
                $penilaian->total_nilai = 0;
                $penilaian->komentar = null;
                $penilaian->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Jawaban berhasil direset.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting kuisioner: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mereset jawaban.');
        }
    }

    /**
     * Tampilkan hasil penilaian
     */
    public function result($periodeId, $dinilaiId)
    {
        try {
            $penilaiId = Auth::id();
            $user = Auth::user();
            $pegawai = $user->pegawai;
            
            if (!$pegawai) {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Data pegawai tidak ditemukan.');
            }
            
            $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A';

            // Validasi periode
            $periode = PeriodePenilaian::find($periodeId);
            if (!$periode) {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Periode penilaian tidak ditemukan.');
            }
            
            // Validasi user yang dinilai
            $dinilai = User::find($dinilaiId);
            if (!$dinilai) {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Pegawai yang dinilai tidak ditemukan.');
            }

            // Ambil data penilaian
            $penilaian = Penilaian::where('periode_id', $periodeId)
                ->where('dinilai_id_user', $dinilaiId)
                ->where('penilai_id_user', $penilaiId)
                ->first();

            if (!$penilaian) {
                return redirect()->route('kuisioner.index')
                    ->with('error', 'Belum ada penilaian untuk pegawai ini.');
            }

            // Ambil jawaban dengan kuisioner
            $jawaban = $penilaian->jawabanKuisioner()->with('kuisioner')->get();

            if ($jawaban->isEmpty()) {
                return redirect()->route('kuisioner.show', [$periodeId, $dinilaiId])
                    ->with('info', 'Belum ada jawaban yang tersimpan. Silakan isi kuisioner terlebih dahulu.');
            }

            // Group hasil by kategori
            $hasilByKategori = $jawaban->groupBy('kuisioner.kategori');
            
            // Hitung rata-rata keseluruhan
            $rataRata = $jawaban->avg('skor');
            
            // Hitung rata-rata per kategori
            $rataRataKategori = [];
            foreach ($hasilByKategori as $kategori => $items) {
                $rataRataKategori[$kategori] = $items->avg('skor');
            }

            // Hitung statistik tambahan
            $totalSkor = $jawaban->sum('skor');
            $maxSkor = $jawaban->count() * 5; // Asumsi skala 1-5
            $persentase = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 2) : 0;

            return view('karyawan.kuisioner.result', compact(
                'periode',
                'hasilByKategori',
                'rataRata',
                'rataRataKategori',
                'dinilaiId',
                'dinilai',
                'pegawai',
                'nama_departemen',
                'penilaian',
                'totalSkor',
                'maxSkor',
                'persentase'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error showing kuisioner result: ' . $e->getMessage());
            return redirect()->route('kuisioner.index')
                ->with('error', 'Terjadi kesalahan saat memuat hasil penilaian.');
        }
    }

    /**
     * Export hasil penilaian ke PDF atau Excel
     */
    public function export(Request $request, $periodeId, $dinilaiId)
    {
        try {
            $format = $request->get('format', 'pdf'); // pdf atau excel
            $penilaiId = Auth::id();
            
            // Ambil data yang sama seperti di result()
            $penilaian = Penilaian::where('periode_id', $periodeId)
                ->where('dinilai_id_user', $dinilaiId)
                ->where('penilai_id_user', $penilaiId)
                ->with(['periode', 'dinilai', 'penilai'])
                ->firstOrFail();

            $jawaban = $penilaian->jawabanKuisioner()->with('kuisioner')->get();
            $hasilByKategori = $jawaban->groupBy('kuisioner.kategori');
            $rataRata = $jawaban->avg('skor');

            if ($format === 'pdf') {
                // Logic untuk export PDF
                // Implementasi sesuai dengan library PDF yang digunakan
                return response()->json(['message' => 'Export PDF belum diimplementasi']);
            } else {
                // Logic untuk export Excel
                // Implementasi sesuai dengan library Excel yang digunakan
                return response()->json(['message' => 'Export Excel belum diimplementasi']);
            }
            
        } catch (\Exception $e) {
            Log::error('Error exporting kuisioner result: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengexport hasil penilaian.');
        }
    }

    /**
     * Tampilkan riwayat penilaian
     */
    public function history()
    {
        try {
            $penilaiId = Auth::id();
            $user = Auth::user();
            $pegawai = $user->pegawai;
            
            if (!$pegawai) {
                return redirect()->back()->with('error', 'Data pegawai tidak ditemukan.');
            }

            // Ambil riwayat penilaian yang pernah dilakukan
            $riwayatPenilaian = Penilaian::where('penilai_id_user', $penilaiId)
                ->with(['periode', 'dinilai.pegawai', 'penilai'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            return view('karyawan.kuisioner.history', compact(
                'riwayatPenilaian',
                'pegawai'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error showing kuisioner history: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat riwayat penilaian.');
        }
    }

    /**
     * Hapus penilaian (jika diizinkan)
     */
    public function destroy($periodeId, $dinilaiId)
    {
        try {
            $penilaiId = Auth::id();

            DB::beginTransaction();

            $penilaian = Penilaian::where('periode_id', $periodeId)
                ->where('dinilai_id_user', $dinilaiId)
                ->where('penilai_id_user', $penilaiId)
                ->first();

            if (!$penilaian) {
                throw new \Exception('Penilaian tidak ditemukan.');
            }

            // Cek apakah periode masih aktif (hanya bisa hapus pada periode aktif)
            $periode = PeriodePenilaian::find($periodeId);
            if (!$periode || $periode->status !== 'aktif') {
                throw new \Exception('Tidak dapat menghapus penilaian pada periode yang tidak aktif.');
            }

            // Hapus jawaban kuisioner
            $penilaian->jawabanKuisioner()->delete();
            
            // Hapus penilaian
            $penilaian->delete();

            DB::commit();
            
            return redirect()->route('kuisioner.index')
                ->with('success', 'Penilaian berhasil dihapus.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting kuisioner: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menghapus penilaian: ' . $e->getMessage());
        }
    }
}