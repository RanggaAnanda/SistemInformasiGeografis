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
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;



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

                    Forms\Components\Select::make('status')
                        ->options([
                            'aktif' => 'Aktif',
                            'nonaktif' => 'Nonaktif',
                            'renovasi' => 'Renovasi',
                        ])
                        ->required(),
                    Forms\Components\Toggle::make('is_public')
                        ->label('Gedung Publik')
                        ->default(false)
                        ->helperText('Centang jika gedung dapat diakses masyarakat umum'),
                ])->columns(2),


            Section::make('Jam Operasional')
                ->schema([
                    Repeater::make('operatingHours')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('day_of_week')
                                ->options([
                                    0 => 'Minggu',
                                    1 => 'Senin',
                                    2 => 'Selasa',
                                    3 => 'Rabu',
                                    4 => 'Kamis',
                                    5 => 'Jumat',
                                    6 => 'Sabtu',
                                ])
                                ->required(),

                            Toggle::make('is_closed')
                                ->label('Tutup'),

                            TimePicker::make('open_time')
                                ->label('Buka')
                                ->visible(fn($get) => !$get('is_closed')),

                            TimePicker::make('close_time')
                                ->label('Tutup')
                                ->visible(fn($get) => !$get('is_closed')),
                        ])
                        ->columns(4)
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_gedung')
                    ->label('Nama Gedung')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->sortable(),

                TextColumn::make('district.nama_kecamatan')
                    ->label('Kecamatan')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status Gedung')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'renovasi',
                        'gray'    => 'nonaktif',
                    ]),

                BadgeColumn::make('operasional_hari_ini')
                    ->label('Operasional')
                    ->getStateUsing(
                        fn(Gedung $record) =>
                        $record->isOpenNow() ? 'Buka' : 'Tutup'
                    )
                    ->colors([
                        'success' => 'Buka',
                        'danger'  => 'Tutup',
                    ]),
                BadgeColumn::make('is_public')
                    ->label('Akses')
                    ->formatStateUsing(fn($state) => $state ? 'Publik' : 'Internal')
                    ->colors([
                        'success' => true,
                        'gray' => false,
                    ]),
            ])
            ->defaultSort('nama_gedung')
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
