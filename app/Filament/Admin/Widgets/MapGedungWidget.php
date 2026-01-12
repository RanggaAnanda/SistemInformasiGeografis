<?php

namespace App\Filament\Widgets;

use App\Models\Gedung;
use Filament\Widgets\Widget;

class MapGedungWidget extends Widget
{
    protected static string $view = 'filament.widgets.map-gedung-widget';

    // Membuat widget tampil satu baris penuh
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'locations' => Gedung::with('district')->get()->map(function ($gedung) {
                return [
                    'lat' => (float) $gedung->latitude,
                    'lng' => (float) $gedung->longitude,
                    'nama' => $gedung->nama_gedung,
                    'jenis' => $gedung->jenis,
                    'kecamatan' => $gedung->district?->nama_kecamatan ?? '-',
                ];
            })->toArray(),
        ];
    }
}
