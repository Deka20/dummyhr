<?php
// app/Models/Absensi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'kehadiran';
    protected $primaryKey = 'id_kehadiran';

    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'status_kehadiran',
        
    ];

    public function pegawai()
{
    return $this->belongsTo(Pegawai::class, 'id_pegawai');
}

}