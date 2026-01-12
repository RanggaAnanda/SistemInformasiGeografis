<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;

use App\Filament\Admin\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
