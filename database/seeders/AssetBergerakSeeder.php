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
            'nama_aset'=>'s',
            'jenis' => 'kendaraan',
            'kategori_asset_id' => $kendaraan->id,
            'gedung_id' => $gedung->id,
            'pegawai_id' => null,
            'status' => 'tersedia',
            'value' => [
                'merk' => 'Honda',
                'tahun' => '2020',
                'nomor_polisi' => 'D 1234 AB',
            ],
        ]);

        AssetBergerak::create([
            'kode_aset' => 'LAP-001',
            'nama_aset'=>'s',
            'jenis' => 'barang elektronik',
            'kategori_asset_id' => $laptop->id,
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
