<?php 
// File: app/Models/Kriteria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'hr_darussalam_kriteria';
    protected $primaryKey = 'id_kriteria';
    public $timestamps = false;
    
    protected $fillable = [
        'nama_kriteria'
    ];
    
    public function pertanyaan()
    {
        return $this->hasMany(Pertanyaan::class, 'id_kriteria');
    }
}
?>