<?php
// app/Models/Pegawai.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'email',
        'id_jabatan',
        'id_departemen',
        'tanggal_masuk',
        'foto',
        'jatahtahunan',
        'golongan' // Tambahkan field golongan
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
    ];

    // Konstanta untuk golongan
    const GOLONGAN_A = 'A'; // Direktur/Manager
    const GOLONGAN_B = 'B'; // Supervisor/Team Lead
    const GOLONGAN_C = 'C'; // Staff Senior
    const GOLONGAN_D = 'D'; // Staff Junior

    public static function getGolonganOptions()
    {
        return [
            self::GOLONGAN_A => 'Golongan A (Direktur/Manager)',
            self::GOLONGAN_B => 'Golongan B (Supervisor/Team Lead)',
            self::GOLONGAN_C => 'Golongan C (Staff Senior)',
            self::GOLONGAN_D => 'Golongan D (Staff Junior)',
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id_pegawai', 'id_pegawai');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'id_pegawai');
    }

    public function getCutiTerpakaiAttribute()
    {
        return $this->cuti()
            ->where('status_cuti', 'Disetujui')
            ->get()
            ->sum('jumlah_hari');
    }

    public function getSisaCutiAttribute()
    {
        return $this->jatahtahunan - $this->cuti_terpakai;
    }

    // Method untuk mendapatkan nama golongan
    public function getNamaGolonganAttribute()
    {
        $golonganOptions = self::getGolonganOptions();
        return $golonganOptions[$this->golongan] ?? 'Tidak Diketahui';
    }

    // Alternative: Menggunakan jabatan sebagai dasar golongan
    public function getGolonganByJabatanAttribute()
    {
        if (!$this->jabatan) {
            return 'D'; // Default
        }

        // Logika pengelompokan berdasarkan nama jabatan
        $namaJabatan = strtolower($this->jabatan->nama_jabatan);
        
        if (str_contains($namaJabatan, 'direktur') || str_contains($namaJabatan, 'manager')) {
            return 'A';
        } elseif (str_contains($namaJabatan, 'supervisor') || str_contains($namaJabatan, 'team lead')) {
            return 'B';
        } elseif (str_contains($namaJabatan, 'senior') || str_contains($namaJabatan, 'koordinator')) {
            return 'C';
        } else {
            return 'D';
        }
    }
}