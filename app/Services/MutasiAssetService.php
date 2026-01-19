<?php

namespace App\Services;

use App\Models\AssetBergerak;
use App\Models\MutasiAsset;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MutasiAssetService
{
    public function approve(MutasiAsset $mutasi, int $approverId): void
    {
        if ($mutasi->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Mutasi sudah diproses.'
            ]);
        }

        DB::transaction(function () use ($mutasi, $approverId) {

            $asset = AssetBergerak::lockForUpdate()
                ->findOrFail($mutasi->asset_bergerak_id);

            if ($asset->status === 'rusak') {
                throw ValidationException::withMessages([
                    'asset' => 'Asset rusak tidak dapat dimutasi.'
                ]);
            }

            $this->applyMutation($asset, $mutasi);

            $mutasi->update([
                'status' => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);
        });
    }

    public function reject(MutasiAsset $mutasi, int $approverId, ?string $reason = null): void
    {
        if ($mutasi->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Mutasi sudah diproses.'
            ]);
        }

        $mutasi->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'catatan' => $reason,
        ]);
    }

    private function applyMutation(AssetBergerak $asset, MutasiAsset $mutasi): void
    {
        match ($mutasi->jenis_mutasi) {

            'klaim' => $asset->update([
                'pegawai_id' => $mutasi->to_pegawai_id,
                'status' => 'digunakan',
            ]),

            'pengembalian' => $asset->update([
                'pegawai_id' => null,
                'status' => 'tersedia',
            ]),

            'antar_gedung' => $asset->update([
                'gedung_id' => $mutasi->to_gedung_id,
            ]),

            default => throw ValidationException::withMessages([
                'jenis_mutasi' => 'Mutasi tidak valid.'
            ]),
        };
    }
}
