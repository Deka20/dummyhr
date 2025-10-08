<?php
// app/Models/Departemen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model untuk tabel 'departemen'

class Departemen extends Model
{
    protected $table = 'departemen';
    protected $primaryKey = 'id_departemen';

    protected $fillable = [
        'nama_departemen',
        'kepala_departemen',
        
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_departemen', 'id_departemen');
    }
}
