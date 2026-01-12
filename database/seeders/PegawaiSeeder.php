<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Budi Santoso',
                'nip' => '198701012010011001',
                'jabatan' => 'Staff IT',
                'gedung_id' => '1',
            ],
            [
                'nama' => 'Siti Aminah',
                'nip' => '198902022012022002',
                'jabatan' => 'Admin Aset',
                'gedung_id' => '1',
            ],
        ];

        foreach ($data as $item) {
            Pegawai::create($item);
        }
    }
}
