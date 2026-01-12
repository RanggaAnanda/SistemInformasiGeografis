<?php

namespace App\Filament\Admin\Resources\AssetBergerakResource\Pages;

use App\Filament\Admin\Resources\AssetBergerakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetBergerak extends EditRecord
{
    protected static string $resource = AssetBergerakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
