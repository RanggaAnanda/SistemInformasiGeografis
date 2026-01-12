<?php

namespace App\Filament\Admin\Resources\GedungResource\Pages;

use App\Filament\Admin\Resources\GedungResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGedung extends CreateRecord
{
    protected static string $resource = GedungResource::class;
    protected $listeners = ['updateLocation'];

    public function updateLocation($lat, $lng)
    {
        $this->form->fill([
            'latitude' => $lat,
            'longitude' => $lng,
        ]);
    }
}
