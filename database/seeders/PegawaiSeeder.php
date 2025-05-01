<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run()
    {
        DB::table('pegawai')->insert([
            'nama' => 'Danu Yudistia',
            'tempat_lahir' => 'Batam',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jalan Contoh No. 1',
            'no_hp' => '081234567890',
            'email' => 'danu@example.com',
            'id_jabatan' => 1, // pastikan id_jabatan 1 ada
            'id_departemen' => 1, // pastikan id_departement 1 ada
            'tanggal_masuk' => '2023-01-01',
            'foto' => 'default.jpg',
            'jatahtahunan' => 12
        ]);
    }
}

?>