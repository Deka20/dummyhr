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
            'username' => 'ellsa',
            'password' => Hash::make('ellsa'),
            'role' => 'pegawai', // bisa 'hrd', 'pegawai', atau 'kepala'
            'id_pegawai' => 7 // pastikan ini sesuai ID dari PegawaiSeeder
        ]);
    }
}


?>