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
            'username' => 'wera',
            'password' => Hash::make('wera'),
            'role' => 'hrd', // bisa 'hrd', 'pegawai', atau 'kepala'
            'id_pegawai' => 4 // pastikan ini sesuai ID dari PegawaiSeeder
        ]);
    }
}


?>