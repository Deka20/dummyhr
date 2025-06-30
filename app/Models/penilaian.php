<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'periode_id',
        'penilai_id_user',
        'dinilai_id_user',
        'total_nilai',
        'komentar',
        'status',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'total_nilai' => 'decimal:2',
        'tanggal_penilaian' => 'datetime',
    ];

    // Relasi ke periode penilaian
    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    // Relasi ke user penilai
    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id_user');
    }

    // Relasi ke user yang dinilai
    public function dinilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_id_user');
    }

    // Relasi ke jawaban kuisioner
    public function jawabanKuisioner(): HasMany
    {
        return $this->hasMany(JawabanKuisioner::class, 'penilaian_id');
    }

    // Scope untuk penilaian yang sudah selesai
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Scope untuk penilaian yang belum diisi
    public function scopeBelumDiisi($query)
    {
        return $query->where('status', 'belum_diisi');
    }

    // Scope untuk periode tertentu
    public function scopePeriode($query, $periodeId)
    {
        return $query->where('periode_id', $periodeId);
    }

    // Scope untuk penilai tertentu
    public function scopePenilai($query, $penilaiId)
    {
        return $query->where('penilai_id_user', $penilaiId);
    }

    // Scope untuk yang dinilai tertentu
    public function scopeDinilai($query, $dinilaiId)
    {
        return $query->where('dinilai_id_user', $dinilaiId);
    }

    // Accessor untuk status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'belum_diisi' => 'warning',
            'selesai' => 'success',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Accessor untuk progress percentage
    public function getProgressPercentageAttribute()
    {
        if ($this->status === 'selesai') {
            return 100;
        }

        $totalKuisioner = $this->periode->kuisioner()->count();
        $jawabanTerisi = $this->jawabanKuisioner()->count();

        if ($totalKuisioner === 0) {
            return 0;
        }

        return round(($jawabanTerisi / $totalKuisioner) * 100, 2);
    }

    // Method untuk menghitung total nilai berdasarkan bobot
    public function hitungTotalNilai()
    {
        $jawaban = $this->jawabanKuisioner()->with('kuisioner')->get();
        
        if ($jawaban->isEmpty()) {
            return 0;
        }

        $totalSkor = 0;
        $totalBobot = 0;

        foreach ($jawaban as $jwb) {
            $totalSkor += $jwb->skor * $jwb->kuisioner->bobot;
            $totalBobot += $jwb->kuisioner->bobot;
        }

        return $totalBobot > 0 ? round($totalSkor / $totalBobot, 2) : 0;
    }

    // Method untuk auto calculate dan update total nilai
    public function updateTotalNilai()
    {
        $this->total_nilai = $this->hitungTotalNilai();
        $this->save();

        return $this->total_nilai;
    }

    // Method untuk menandai penilaian selesai
    public function markAsSelesai()
    {
        $this->status = 'selesai';
        $this->tanggal_penilaian = now();
        $this->updateTotalNilai();
        $this->save();

        return $this;
    }

    // Method untuk cek apakah penilaian sudah lengkap
    public function isLengkap()
    {
        $totalKuisioner = $this->periode->kuisioner()->count();
        $jawabanTerisi = $this->jawabanKuisioner()->count();

        return $totalKuisioner === $jawabanTerisi;
    }
    public function scopeSameDepartment($query, $userId)
{
    return $query->whereHas('penilai.pegawai', function($q) use ($userId) {
        $q->whereHas('departemen', function($dept) use ($userId) {
            $dept->whereHas('pegawai.user', function($user) use ($userId) {
                $user->where('id_user', $userId);
            });
        });
    });
}

// Method untuk cek apakah boleh menilai
public function canEvaluate($penilaiId, $dinilaiId)
{
    $penilai = User::find($penilaiId);
    $dinilai = User::find($dinilaiId);
    
    return $penilai->pegawai->id_departemen === $dinilai->pegawai->id_departemen;
}
public static function boot()
{
    parent::boot();
    
    static::creating(function($model) {
        // Cek tidak boleh menilai diri sendiri
        if ($model->penilai_id_user === $model->dinilai_id_user) {
            throw new \Exception('Tidak boleh menilai diri sendiri');
        }
        
        // Cek harus se-departemen
        if (!$model->canEvaluate($model->penilai_id_user, $model->dinilai_id_user)) {
            throw new \Exception('Hanya boleh menilai rekan se-departemen');
        }
    });
}
}

