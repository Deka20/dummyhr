<?php
// app/Models/Pegawai.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $timestamps = false; // kalau memang tidak pakai created_at dan updated_at

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
        'jatahtahunan'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
    ];

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
}
