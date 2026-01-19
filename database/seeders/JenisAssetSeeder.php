<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisAsset;

class JenisAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisAsset::insert([
            [
                'kode' => '101',
                'nama_jenis' => 'Kendaraan',
            ],
            [
                'kode' => '201',
                'nama_jenis' => 'Elektronik',
            ],
        ]);
    }
}
