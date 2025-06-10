<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $primaryKey = 'id_cuti';

    protected $fillable = [
        'id_pegawai',
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'id_jenis_cuti',
        'status_cuti',
        'keterangan',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'id_jenis_cuti');
    }
        public function getJumlahHariAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_mulai)
            ->diffInDays(\Carbon\Carbon::parse($this->tanggal_selesai)) + 1;
    }
}
