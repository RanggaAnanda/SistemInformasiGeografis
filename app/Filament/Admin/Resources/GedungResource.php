<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Gedung;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Dotswan\MapPicker\Fields\Map;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Placeholder;
use App\Filament\Admin\Resources\GedungResource\Pages;

class GedungResource extends Resource
{
    protected static ?string $model = Gedung::class;
    protected static ?string $navigationLabel = 'Gedung';
    protected static ?string $navigationGroup = 'Data Aset';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Lokasi Gedung')
            ->schema([
                Placeholder::make('map')
                    ->content(fn($get) => view('filament.components.leaflet-map', [
                        'latitude' => $get('latitude') ?? -6.9175,
                        'longitude' => $get('longitude') ?? 107.6191,
                    ])),

                TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->reactive(),

                TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->reactive(),
            ]),

        Section::make('Informasi Gedung')
            ->schema([
                Forms\Components\Select::make('district_id')
                    ->label('Kecamatan')
                    ->relationship('district', 'nama_kecamatan')
                    ->searchable()
                    ->required()
                    ->preload(),

                TextInput::make('nama_gedung')->required(),
                TextInput::make('jenis')->required(),

                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull()
                    ->rows(3)
                    ->reactive(), // penting supaya Livewire bisa update
            ])->columns(2),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_gedung')->searchable(),
                TextColumn::make('jenis'),
                TextColumn::make('district.nama_kecamatan')->label('Kecamatan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGedungs::route('/'),
            'create' => Pages\CreateGedung::route('/create'),
            'edit' => Pages\EditGedung::route('/{record}/edit'),
        ];
    }
}
