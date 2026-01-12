<?php

namespace App\Filament\Admin\Resources;
use App\Filament\Admin\Resources\GedungResource\RelationManagers;
use Dom\Text;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Admin\Resources\GedungResource\Pages;
use App\Models\Gedung;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GedungResource extends Resource
{
    protected static ?string $model = Gedung::class;
    protected static ?string $navigationLabel = 'Gedung';
    protected static ?string $navigationGroup = 'Data Aset';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('district_id')
                ->relationship('district', 'nama_kecamatan')
                ->required(),

            TextInput::make('nama_gedung')->required(),
            TextInput::make('jenis')->required(),
            Forms\Components\Textarea::make('alamat'),

            TextInput::make('latitude')->numeric()->required(),
            TextInput::make('longitude')->numeric()->required(),
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
