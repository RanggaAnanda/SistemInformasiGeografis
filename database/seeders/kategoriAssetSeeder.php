<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriAsset;

class KategoriAssetSeeder extends Seeder
{
    public function run(): void
    {
        KategoriAsset::create([
            'nama_kategori' => 'Kendaraan',
            'fields' => [
                ['name' => 'merk'],
                ['name' => 'tahun'],
                ['name' => 'nomor_polisi'],
            ],
        ]);

        KategoriAsset::create([
            'nama_kategori' => 'Laptop',
            'fields' => [
                ['name' => 'merk'],
                ['name' => 'ram'],
                ['name' => 'processor'],
            ],
        ]);
    }
}
