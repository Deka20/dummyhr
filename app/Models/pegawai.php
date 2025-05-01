<?php
// app/Models/Pegawai.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_pegawai';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
    ];

    /**
     * Get the user that belongs to this pegawai.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id_pegawai', 'id_pegawai');
    }
}