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
            'username' => 'nina',
            'password' => Hash::make('nina'),
            'role' => 'kepala_yayasan', // bisa 'hrd', 'pegawai', atau 'kepala'
            'id_pegawai' => 7 // pastikan ini sesuai ID dari PegawaiSeeder
        ]);
    }
}


?>