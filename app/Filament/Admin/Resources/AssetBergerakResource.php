<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetBergerakResource\Pages;
use App\Models\AssetBergerak;
use App\Models\KategoriAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;

use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;

use App\Models\Pegawai;


class AssetBergerakResource extends Resource
{
    protected static ?string $model = AssetBergerak::class;
    protected static ?string $navigationLabel = 'Asset Bergerak';
    protected static ?string $navigationGroup = 'Data Aset';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Utama')
                    ->schema([
                        TextInput::make('kode_aset')
                            ->required()
                            ->readonly()
                            ->unique(ignoreRecord: true)
                            ->helperText('Otomatis dibuat berdasarkan kategori yang dipilih'),

                        Select::make('jenis_asset_id')
                            ->label('Jenis Asset')
                            ->relationship('jenis', 'nama_jenis')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn($set) => $set('kategori_asset_id', null)),


                        Select::make('gedung_id')
                            ->label('Gedung')
                            ->relationship('gedung', 'nama_gedung')
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(fn($state, callable $set) => $set('pegawai_id', null)),

                        Select::make('pegawai_id')
                            ->label('Penanggung Jawab (Pegawai)')
                            ->options(function (Get $get) {
                                if (!$get('gedung_id')) {
                                    return [];
                                }

                                return Pegawai::where('gedung_id', $get('gedung_id'))
                                    ->where('status', 'aktif')
                                    ->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->placeholder('Aset berada di gedung')
                            ->helperText('Kosongkan jika aset berada di gedung')
                            ->disabled(fn(Get $get) => !$get('gedung_id')),

                        Select::make('status')
                            ->options([
                                'tersedia' => 'Tersedia (di Gedung)',
                                'digunakan' => 'Digunakan Pegawai',
                                'rusak'     => 'Rusak',
                                'hilang'    => 'Hilang',
                            ])
                            ->default('tersedia')
                            ->required(),
                    ])->columns(2),

                Section::make('Atribut Berdasarkan Kategori')
                    ->schema([
                        TextInput::make('nama_aset'),
                        Select::make('kategori_asset_id')
                            ->label('Kategori Aset')
                            ->options(function (Get $get) {
                                $jenisId = $get('jenis_asset_id');
                                if (!$jenisId) {
                                    return [];
                                }
                                return KategoriAsset::where('jenis_asset_id', $jenisId)
                                    ->pluck('nama_kategori', 'id');
                            })
                            ->relationship('kategori', 'nama_kategori')
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (blank($state)) {
                                    $set('kode_aset', null);
                                    return;
                                }

                                // 1. Ambil data kategori
                                $category = \App\Models\KategoriAsset::find($state);
                                if (!$category) return;

                                // 2. Ambil inisial dari nama kategori (contoh: Motor -> MTR)
                                // Kita ambil 3 huruf pertama dan jadikan uppercase
                                $jenis = $category->jenis;
                                $prefix = strtoupper($jenis->kode);

                                // 3. Hitung jumlah aset yang sudah menggunakan kategori ini
                                $count = \App\Models\AssetBergerak::where('kategori_asset_id', $state)->count();

                                // 4. Buat nomor urut (count + 1) dengan format 3 digit (001)
                                $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

                                // 5. Gabungkan menjadi MTR-001
                                $generatedCode = "{$prefix}-{$nextNumber}";

                                // 6. Set nilai ke input kode_aset
                                $set('kode_aset', $generatedCode);

                                // Reset nilai atribut dinamis
                                $set('value', []);
                            }),
                        Grid::make(2)
                            ->schema(function (Get $get) {
                                $categoryId = $get('kategori_asset_id');
                                if (!$categoryId) return [];

                                $category = KategoriAsset::find($categoryId);
                                if (!$category || !$category->fields) return [];

                                $inputs = [];
                                foreach ($category->fields as $field) {
                                    // Menggunakan "value." sesuai kolom JSON di migration Anda
                                    $inputs[] = TextInput::make("value.{$field['name']}")
                                        ->label($field['name'])
                                        ->required();
                                }
                                return $inputs;
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_aset')->searchable()->sortable(),
                TextColumn::make('jenis')->searchable(),

                // Perbaikan: Pakai relasi 'kategori' bukan 'category'
                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->sortable(),

                TextColumn::make('gedung.nama_gedung')
                    ->label('Gedung'),

                // Perbaikan: Pakai kolom 'value' sesuai migration Anda
                TextColumn::make('value')
                    ->label('Atribut')
                    ->getStateUsing(fn($record) => $record->value ? collect($record->value)->implode(', ') : '-')
                    ->color('gray')
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'dipindahkan',
                        'danger' => 'rusak',
                    ]),
            ])
            ->filters([
                // Perbaikan filter agar sesuai relasi 'kategori'
                Tables\Filters\SelectFilter::make('kategori_asset_id')
                    ->label('Filter Kategori')
                    ->relationship('kategori', 'nama_kategori'),
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis')
                    ->options([
                        'kendaraan' => 'Kendaraan',
                        'furnitur' => 'Furnitur',
                        'barang elektronik' => 'Barang elektronik',
                    ])
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssetBergeraks::route('/'),
            'create' => Pages\CreateAssetBergerak::route('/create'),
            'edit' => Pages\EditAssetBergerak::route('/{record}/edit'),
        ];
    }
}
