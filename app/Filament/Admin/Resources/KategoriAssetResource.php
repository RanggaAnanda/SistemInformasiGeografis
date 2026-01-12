<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\KategoriAssetResource\Pages;
use App\Filament\Admin\Resources\KategoriAssetResource\RelationManagers;
use App\Models\KategoriAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriAssetResource extends Resource
{
    protected static ?string $model = KategoriAsset::class;
    protected static ?string $navigationLabel = 'Kategori Asset';
    protected static ?string $navigationGroup = 'Data Aset';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('nama_kategori')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Repeater::make('fields')
                        ->label('Daftar Atribut Tambahan')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Atribut (Contoh: Plat Nomor, Merk, RAM)')
                                ->required(),
                            Forms\Components\Select::make('type')
                                ->label('Tipe Input')
                                ->options([
                                    'text' => 'Teks',
                                    'number' => 'Angka',
                                    'date' => 'Tanggal',
                                ])->default('text')->required(),
                        ])
                        ->columns(2)
                        ->createItemButtonLabel('Tambah Atribut Baru')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->searchable()
                    ->sortable(),

                // Menampilkan daftar nama atribut dipisahkan koma
                Tables\Columns\TextColumn::make('fields')
                ->label('Atribut')
                ->getStateUsing(fn ($record) => collect($record->fields)->pluck('name')->implode(', '))
                ->separator(',') 
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
            'index' => Pages\ListKategoriAssets::route('/'),
            'create' => Pages\CreateKategoriAsset::route('/create'),
            'edit' => Pages\EditKategoriAsset::route('/{record}/edit'),
        ];
    }
}
