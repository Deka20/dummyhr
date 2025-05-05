<?php 
// File: app/Models/PeriodePenilaian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodePenilaian extends Model
{
    protected $table = 'hr_darussalam_periode_penilaian';
    protected $primaryKey = 'id_periode';
    public $timestamps = false;
    
    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai'
    ];
    
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];
    
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_periode');
    }
}
?>