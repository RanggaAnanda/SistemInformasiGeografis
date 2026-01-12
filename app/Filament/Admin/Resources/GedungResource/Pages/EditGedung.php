<?php

namespace App\Filament\Admin\Resources\GedungResource\Pages;

use App\Filament\Admin\Resources\GedungResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGedung extends EditRecord
{
    protected static string $resource = GedungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected $listeners = ['updateLocation'];

    public function updateLocation($lat, $lng)
    {
        $this->form->fill([
            'latitude' => $lat,
            'longitude' => $lng,
        ]);
    }
}
