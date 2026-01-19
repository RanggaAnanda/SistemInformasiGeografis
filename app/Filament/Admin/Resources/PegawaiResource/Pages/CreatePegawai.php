<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;

use App\Filament\Admin\Resources\PegawaiResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function handleRecordCreation(array $data): Pegawai
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name'     => $data['nama'],
                'email'    => Str::slug($data['nama']) . '@mail.com',
                'password' => Hash::make('password'),
                'role'     => $data['role'], // pegawai / admin_asset
            ]);

            // 2. Create PEGAWAI (MANUAL)
            return Pegawai::create([
                'user_id'    => $user->id,
                'nip'        => $data['nip'] ?? null,
                'nama'       => $data['nama'],

                'jabatan'    => $data['jabatan'],
                'gedung_id'  => $data['gedung_id'],
                'status'     => $data['status'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        });
    }
}
