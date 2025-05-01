<?php 
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('user')->insert([
            'username' => 'danu',
            'password' => Hash::make('password123'),
            'role' => 'pegawai', // bisa 'hrd', 'pegawai', atau 'kepala'
            'id_pegawai' => 1 // pastikan ini sesuai ID dari PegawaiSeeder
        ]);
    }
}


?>