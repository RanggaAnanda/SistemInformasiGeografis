<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\User;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawaiData = [
            [
                'nama' => 'Budi Santoso',
                'nip' => '198701012010011001',
                'jabatan' => 'Staff IT',
                'gedung_id' => 1,
                'email' => 'pegawai@mail.com', // mapping ke user
            ],
            [
                'nama' => 'Siti Aminah',
                'nip' => '198902022012022002',
                'jabatan' => 'Admin Aset',
                'gedung_id' => 1,
                'email' => 'asset@mail.com',
            ],
        ];

        foreach ($pegawaiData as $item) {

            $user = User::where('email', $item['email'])->first();

            if (! $user) {
                throw new \Exception("User dengan email {$item['email']} tidak ditemukan");
            }

            Pegawai::create([
                'user_id' => $user->id,
                'nama' => $item['nama'],
                'nip' => $item['nip'],
                'jabatan' => $item['jabatan'],
                'gedung_id' => $item['gedung_id'],
            ]);
        }
    }
}
