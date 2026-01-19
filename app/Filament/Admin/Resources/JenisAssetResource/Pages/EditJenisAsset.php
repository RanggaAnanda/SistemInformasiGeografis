<?php

namespace App\Filament\Admin\Resources\JenisAssetResource\Pages;

use App\Filament\Admin\Resources\JenisAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisAsset extends EditRecord
{
    protected static string $resource = JenisAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
