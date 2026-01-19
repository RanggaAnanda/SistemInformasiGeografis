<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetBergerak;
use App\Models\KategoriAsset;
use App\Models\Gedung;
use App\Models\Pegawai;

class AssetBergerakSeeder extends Seeder
{
    public function run(): void
    {
        $gedung = Gedung::first();
        $pegawai = Pegawai::first();

        $kendaraan = KategoriAsset::where('nama_kategori', 'Kendaraan')->first();
        $laptop = KategoriAsset::where('nama_kategori', 'Laptop')->first();

        AssetBergerak::create([
            'kode_aset' => 'KEN-001',
            'nama_aset'=>'Motor Vario',
            'jenis_asset_id' => 1,
            'kategori_asset_id' => 1,
            'gedung_id' => $gedung->id,
            'pegawai_id' => NULL,
            'status' => 'tersedia',
            'value' => [
                'merk' => 'Honda',
                'tahun' => '2020',
                'nomor_polisi' => 'D 1234 AB',
            ],
        ]);

        AssetBergerak::create([
            'kode_aset' => 'LAP-001',
            'nama_aset'=>'Laptop Lenovo',
            'jenis_asset_id' => 2,
            'kategori_asset_id' => 2,
            'gedung_id' => $gedung->id,
            'pegawai_id' => $pegawai->id,
            'status' => 'digunakan',
            'value' => [
                'merk' => 'Lenovo',
                'ram' => '16 GB',
                'processor' => 'Intel i5',
            ],
        ]);
    }
}
