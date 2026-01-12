<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;

class GedungSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_gedung' => 'Gedung A',
                'jenis' => 'Perkantoran',
                'district_id' => 1,
                'status' => 'aktif',
                'is_public' => true,
                'latitude' => -6.917463,
                'longitude' => 107.619123,
            ],
            [
                'nama_gedung' => 'Gedung B',
                'jenis' => 'Pendidikan',
                'district_id' => 1,
                'status' => 'aktif',
                'is_public' => false,
                'latitude' => -6.914744,
                'longitude' => 107.609810,
            ],
        ];

        foreach ($data as $item) {
            Gedung::create($item);
        }
    }
}
