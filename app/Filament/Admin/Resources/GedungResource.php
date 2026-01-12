<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GedungResource\RelationManagers;
use Dom\Text;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Admin\Resources\GedungResource\Pages;
use App\Models\Gedung;
use BcMath\Number;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Section;

class GedungResource extends Resource
{
    protected static ?string $model = Gedung::class;
    protected static ?string $navigationLabel = 'Gedung';
    protected static ?string $navigationGroup = 'Data Aset';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Lokasi Geografis')
            ->description('Gunakan kotak pencarian atau klik langsung pada peta untuk menentukan lokasi gedung.')
            ->schema([
                Map::make('location')
                    ->label('Pilih Lokasi Gedung')
                    ->columnSpanFull()
                    ->live()
                    // Mengaktifkan fitur kontrol Leaflet (termasuk search & geoman jika terpasang)
                    ->showMarker()
                    // Marker otomatis pindah ke titik klik
                    ->draggable() 
                    /** * Untuk GeoJSON di Dotswan v1, jika method ->geoJson() tidak ada,
                     * biasanya menggunakan konfigurasi extra atau dipanggil via script.
                     * Untuk sementara kita hapus agar tidak error, atau gunakan:
                     */
                    // ->geoMan(true) 
                    ->afterStateUpdated(function (Forms\Set $set, ?array $state) {
                        if (!$state) return;

                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);

                        try {
                            $response = \Illuminate\Support\Facades\Http::withHeaders([
                                'User-Agent' => 'SistemInformasiGeografis/1.0'
                            ])->get("https://nominatim.openstreetmap.org/reverse", [
                                'format' => 'json',
                                'lat' => $state['lat'],
                                'lon' => $state['lng'],
                                'addressdetails' => 1,
                            ])->json();

                            $kecamatanName = $response['address']['suburb'] 
                                ?? $response['address']['village'] 
                                ?? $response['address']['city_district'] 
                                ?? null;

                            if ($kecamatanName) {
                                $district = \App\Models\District::where('nama_kecamatan', 'LIKE', "%{$kecamatanName}%")->first();
                                if ($district) {
                                    $set('district_id', $district->id);
                                }
                            }
                            
                            if (isset($response['display_name'])) {
                                $set('alamat', $response['display_name']);
                            }
                        } catch (\Exception $e) {}
                    })
                    ->afterStateHydrated(function ($set, $record) {
                        if ($record) {
                            $set('location', [
                                'lat' => (float) $record->latitude,
                                'lng' => (float) $record->longitude
                            ]);
                        }
                    })
                    ->defaultLocation(-6.9175, 107.6191)
                    ->zoom(13)
                    ->tilesUrl('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),

                Forms\Components\Grid::make(2)->schema([
                    TextInput::make('latitude')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set, Forms\Get $get) => 
                            $set('location', ['lat' => $state, 'lng' => $get('longitude')])
                        ),
                    TextInput::make('longitude')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set, Forms\Get $get) => 
                            $set('location', ['lat' => $get('latitude'), 'lng' => $state])
                        ),
                ]),
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
                    ->rows(3),
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
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
