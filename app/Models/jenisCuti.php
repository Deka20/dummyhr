<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    protected $table = 'jenis_cuti';
    protected $primaryKey = 'id_jenis_cuti';

    protected $fillable = [
        'nama_jenis_cuti',
        'max_hari_cuti',
    ];

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'id_jenis_cuti');
    }
}
