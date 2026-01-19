<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MutasiAsset;
use App\Models\AssetBergerak;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Services\MutasiAssetService;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Filament\Admin\Resources\MutasiAssetResource\Pages;

class MutasiAssetResource extends Resource
{
    protected static ?string $model = MutasiAsset::class;

    protected static ?string $navigationLabel = 'Mutasi Asset';
    protected static ?string $navigationGroup = 'Proses Aset';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('asset_bergerak_id')
                ->label('Asset')
                ->relationship(
                    'asset',
                    'kode_aset',
                    function ($query) {
                        $user = Auth::user();

                        // Super admin: lihat semua asset
                        if ($user->role === 'super_admin') {
                            return $query;
                        }

                        // Pegawai wajib punya gedung
                        if (! $user->pegawai) {
                            return $query->whereRaw('1 = 0');
                        }

                        // Pegawai hanya asset di gedung sendiri
                        return $query->where('gedung_id', $user->pegawai->gedung_id);
                    }
                )
                ->required()
                ->searchable()
                ->preload()
                ->live(),



            Select::make('jenis_mutasi')
                ->required()
                ->options(function (Get $get) {

                    $assetId = $get('asset_bergerak_id');
                    if (! $assetId) {
                        return [];
                    }

                    $asset = AssetBergerak::find($assetId);
                    if (! $asset) {
                        return [];
                    }

                    return match ($asset->status) {
                        'tersedia' => [
                            'klaim' => 'Klaim Asset',
                            'antar_gedung' => 'Pindah Gedung',
                        ],
                        'digunakan' => [
                            'pengembalian' => 'Pengembalian',
                        ],
                        default => [],
                    };
                })
                ->live(),

            Select::make('to_pegawai_id')
                ->label('Digunakan Oleh')
                ->relationship('toPegawai', 'nama')
                ->visible(fn(Get $get) => $get('jenis_mutasi') === 'klaim')
                ->disabled(fn() => Auth::user()->role === 'pegawai')
                ->default(
                    fn() =>
                    Auth::user()->role === 'pegawai'
                        ? Auth::user()->pegawai->id
                        : null
                ),


            Select::make('to_gedung_id')
                ->relationship('toGedung', 'nama_gedung')
                ->visible(fn(Get $get) => $get('jenis_mutasi') === 'antar_gedung')
                ->required(fn(Get $get) => $get('jenis_mutasi') === 'antar_gedung'),


            Forms\Components\Textarea::make('keterangan')->required(),

            Forms\Components\Hidden::make('requested_by')
                ->default(fn() => Auth::id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.kode_aset')->searchable(),
                Tables\Columns\TextColumn::make('jenis_mutasi')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('requestedBy.name')->label('Pemohon'),
                Tables\Columns\TextColumn::make('approvedBy.name')->label('Approver')->toggleable(),
                Tables\Columns\TextColumn::make('approved_at')->dateTime()->toggleable(),
            ])
            ->actions([
                Action::make('approve')
                    ->visible(
                        fn(MutasiAsset $record) =>
                        Auth::check() && Auth::user()->can('approve', $record)
                    )
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(
                        fn(MutasiAsset $record) =>
                        app(MutasiAssetService::class)
                            ->approve($record, Auth::id())
                    ),

                Action::make('reject')
                    ->visible(
                        fn(MutasiAsset $record) =>
                        Auth::check() && Auth::user()->can('approve', $record)
                    )
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('keterangan')->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(
                        fn(MutasiAsset $record, array $data) =>
                        app(MutasiAssetService::class)
                            ->reject($record, Auth::id(), $data['keterangan'])
                    ),
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
