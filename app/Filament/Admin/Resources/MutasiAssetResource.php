<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MutasiAssetResource\Pages;
use App\Models\MutasiAsset;
use App\Models\AssetBergerak;
use App\Services\MutasiAssetService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class MutasiAssetResource extends Resource
{
    protected static ?string $model = MutasiAsset::class;

    protected static ?string $navigationLabel = 'Mutasi Asset';
    protected static ?string $navigationGroup = 'Proses Aset';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('asset_bergerak_id')
                ->label('Asset')
                ->relationship('asset', 'kode_aset')
                ->required(),

            Forms\Components\Select::make('jenis_mutasi')
                ->options([
                    'klaim' => 'Klaim Asset',
                    'pengembalian' => 'Pengembalian',
                    'internal' => 'Mutasi Internal',
                    'antar_gedung' => 'Mutasi Antar Gedung',
                ])
                ->required(),

            Forms\Components\Textarea::make('keterangan')
                ->label('Alasan / Keterangan')
                ->required(),

            Forms\Components\Hidden::make('requested_by')
                ->default(fn () => Auth::id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.kode_aset')
                    ->label('Asset')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis_mutasi')
                    ->badge(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),

                TextColumn::make('requestedBy.name')
                    ->label('Diajukan Oleh'),

                TextColumn::make('approvedBy.name')
                    ->label('Disetujui Oleh')
                    ->toggleable(),

                TextColumn::make('approved_at')
                    ->label('Tanggal Approval')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (MutasiAsset $record) => $record->status === 'draft')
                    ->action(function (MutasiAsset $record) {
                        app(MutasiAssetService::class)
                            ->approve($record, Auth::id());
                    })
                    ->requiresConfirmation(),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (MutasiAsset $record) => $record->status === 'draft')
                    ->form([
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (MutasiAsset $record, array $data) {
                        app(MutasiAssetService::class)
                            ->reject($record, Auth::id(), $data['keterangan']);
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMutasiAssets::route('/'),
            'create' => Pages\CreateMutasiAsset::route('/create'),
        ];
    }
}
