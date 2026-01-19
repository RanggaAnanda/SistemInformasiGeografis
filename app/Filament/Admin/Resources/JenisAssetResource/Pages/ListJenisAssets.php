<?php

namespace App\Filament\Admin\Resources\JenisAssetResource\Pages;

use App\Filament\Admin\Resources\JenisAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisAssets extends ListRecords
{
    protected static string $resource = JenisAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
