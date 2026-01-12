<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\District;

class ImportBandungDistricts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:bandung-districts';

    /**
     * The console command description.
     */
    protected $description = 'Import data kecamatan Bandung dari GeoJSON';

    public function handle()
    {
        $path = public_path('geojson/bandung/kecamatan-bandung.json');

        if (!File::exists($path)) {
            $this->error('File GeoJSON tidak ditemukan.');
            return;
        }

        $geojson = json_decode(File::get($path), true);

        foreach ($geojson['features'] as $feature) {
            District::updateOrCreate(
                ['kode_kecamatan' => $feature['properties']['id_kecamatan']],
                [
                    'nama_kecamatan' => $feature['properties']['nama_kecamatan'],
                    'nama_wilayah'   => $feature['properties']['nama_wilayah'],
                ]
            );
        }

        $this->info('Import kecamatan Bandung selesai.');
    }
}
