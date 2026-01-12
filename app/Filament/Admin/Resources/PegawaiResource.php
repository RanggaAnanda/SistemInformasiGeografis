<?php

namespace App\Filament\Admin\Resources;

use App\Models\Pegawai;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Admin\Resources\PegawaiResource\Pages;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationLabel = 'Pegawai';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Data Pegawai')
                ->schema([
                    TextInput::make('nip')
                        ->label('NIP')
                        ->maxLength(30),

                    TextInput::make('nama')
                        ->required()
                        ->maxLength(150),

                    TextInput::make('jabatan')
                        ->required()
                        ->maxLength(100),

                    Select::make('gedung_id')
                        ->label('Gedung / Unit Kerja')
                        ->relationship('gedung', 'nama_gedung')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('status')
                        ->options([
                            'aktif'     => 'Aktif',
                            'mutasi'    => 'Mutasi',
                            'nonaktif'  => 'Nonaktif',
                        ])
                        ->default('aktif')
                        ->required(),

                    Textarea::make('keterangan')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jabatan')
                    ->sortable(),

                TextColumn::make('gedung.nama_gedung')
                    ->label('Gedung')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'mutasi',
                        'gray'    => 'nonaktif',
                    ]),
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
            'index'  => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit'   => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
