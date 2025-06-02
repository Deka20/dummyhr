<?php
// File: app/Models/Penilaian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';
    public $timestamps = false;
    
    protected $fillable = [
        'id_pegawai',
        'id_penilai',
        'id_pertanyaan',
        'id_periode',
        'skor',
        'komentar',
        'periode_penilaian',
        'status',
        'tanggal_penilaian'
    ];
    
    protected $casts = [
        'tanggal_penilaian' => 'date',
        'status' => 'string'
    ];
    
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
    
    public function penilai()
    {
        return $this->belongsTo(Pegawai::class, 'id_penilai');
    }
    
    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'id_pertanyaan');
    }
    
    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class, 'id_periode');
    }
}
