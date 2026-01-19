<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Gedung;
use Filament\Notifications\Notification;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profile Saya';
    protected static ?string $title = 'Edit Profile';
    protected static ?string $navigationGroup = 'Akun';

    protected static string $view = 'filament.admin.pages.profile';

    public array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $this->form->fill([
            'name'      => $user->name,
            'email'     => $user->email,
            'nama'      => $pegawai?->nama,
            'jabatan'   => $pegawai?->jabatan,
            'gedung_id' => $pegawai?->gedung_id,
            'keterangan' => $pegawai?->keterangan,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Akun Login')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama User')
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required(),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->label('Password Baru')
                        ->nullable()
                        ->helperText('Kosongkan jika tidak ingin mengganti password'),
                ]),

            Forms\Components\Section::make('Data Pegawai')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->required(),

                    Forms\Components\TextInput::make('jabatan')
                        ->required(),

                    Forms\Components\Select::make('gedung_id')
                        ->label('Gedung / Unit Kerja')
                        ->options(
                            Gedung::query()
                                ->pluck('nama_gedung', 'id')
                                ->toArray()
                        )
                        ->disabled()
                        ->dehydrated(false), // ⬅️ PENTING


                    Forms\Components\Textarea::make('keterangan')
                        ->rows(3),
                ]),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function save(): void
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        // Update USER
        $user->update([
            'name'  => $this->data['name'],
            'email' => $this->data['email'],
            ...(!empty($this->data['password'])
                ? ['password' => Hash::make($this->data['password'])]
                : []),
        ]);

        // Update PEGAWAI
        if ($pegawai) {
            $pegawai->update([
                'nama'       => $this->data['nama'],
                'jabatan'    => $this->data['jabatan'],
                'keterangan' => $this->data['keterangan'],
            ]);
        }

        Notification::make()
            ->title('Profile berhasil diperbarui')
            ->success()
            ->send();
    }
}
