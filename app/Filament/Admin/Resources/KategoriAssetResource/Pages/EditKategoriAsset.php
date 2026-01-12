<?php

namespace App\Filament\Admin\Resources\KategoriAssetResource\Pages;

use App\Filament\Admin\Resources\KategoriAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriAsset extends EditRecord
{
    protected static string $resource = KategoriAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
