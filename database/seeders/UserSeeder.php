<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('123'),
            'role' => 'super_admin',
        ]);

        User::create([
            'name' => 'Admin Asset',
            'email' => 'asset@mail.com',
            'password' => Hash::make('123'),
            'role' => 'admin_asset',
        ]);

        User::create([
            'name' => 'Pegawai Biasa',
            'email' => 'pegawai@mail.com',
            'password' => Hash::make('123'),
            'role' => 'pegawai',
        ]);
    }
}
