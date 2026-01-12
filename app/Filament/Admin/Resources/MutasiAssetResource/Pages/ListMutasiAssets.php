<?php

namespace App\Filament\Admin\Resources\MutasiAssetResource\Pages;

use App\Filament\Admin\Resources\MutasiAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMutasiAssets extends ListRecords
{
    protected static string $resource = MutasiAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
