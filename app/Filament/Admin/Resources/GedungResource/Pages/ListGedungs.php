<?php

namespace App\Filament\Admin\Resources\GedungResource\Pages;

use App\Filament\Admin\Resources\GedungResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGedungs extends ListRecords
{
    protected static string $resource = GedungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
