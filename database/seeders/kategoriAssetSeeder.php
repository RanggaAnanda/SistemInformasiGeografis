<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriAsset;
use App\Models\JenisAsset;

class KategoriAssetSeeder extends Seeder
{
    public function run(): void
    {
        $kendaraan = JenisAsset::where('nama_jenis', 'Kendaraan')->first();
        $elektronik = JenisAsset::where('nama_jenis', 'Elektronik')->first();

        KategoriAsset::create([
            'nama_kategori' => 'Mobil',
            'jenis_asset_id' => $kendaraan->id,
            'fields' => [
                ['name' => 'merk'],
                ['name' => 'tahun'],
                ['name' => 'nomor_polisi'],
            ],
        ]);

        KategoriAsset::create([
            'nama_kategori' => 'Laptop',
            'jenis_asset_id' => $elektronik->id,
            'fields' => [
                ['name' => 'merk'],
                ['name' => 'ram'],
                ['name' => 'processor'],
            ],
        ]);
    }
}
