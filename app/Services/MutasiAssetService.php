<?php

namespace App\Services;

use App\Models\AssetBergerak;
use App\Models\MutasiAsset;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MutasiAssetService
{
    /**
     * Approve mutasi asset
     */
    public function approve(MutasiAsset $mutasi, int $approverId): void
    {
        if ($mutasi->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Mutasi sudah diproses.'
            ]);
        }

        DB::transaction(function () use ($mutasi, $approverId) {

            $asset = AssetBergerak::lockForUpdate()->findOrFail(
                $mutasi->asset_bergerak_id
            );

            // VALIDASI STATUS ASSET
            if ($asset->status === 'rusak') {
                throw ValidationException::withMessages([
                    'asset' => 'Asset rusak tidak dapat dimutasi.'
                ]);
            }

            // APPLY MUTATION
            $this->applyMutation($asset, $mutasi);

            // UPDATE MUTASI
            $mutasi->update([
                'status' => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Reject mutasi asset
     */
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

    /**
     * Apply perubahan ke asset (PRIVATE – inti bisnis)
     */
    private function applyMutation(AssetBergerak $asset, MutasiAsset $mutasi): void
    {
        match ($mutasi->jenis_mutasi) {

            // Gedung → Pegawai
            'klaim' => $asset->update([
                'pegawai_id' => $mutasi->to_pegawai_id,
                'status' => 'aktif',
            ]),

            // Pegawai → Gedung
            'pengembalian' => $asset->update([
                'pegawai_id' => null,
                'status' => 'aktif',
            ]),

            // Pegawai A → Pegawai B
            'internal' => $asset->update([
                'pegawai_id' => $mutasi->to_pegawai_id,
            ]),

            // Antar Gedung
            'antar_gedung' => $asset->update([
                'gedung_id' => $mutasi->to_gedung_id,
                'pegawai_id' => $mutasi->to_pegawai_id,
                'status' => 'aktif',
            ]),

            default => throw ValidationException::withMessages([
                'jenis_mutasi' => 'Jenis mutasi tidak valid.'
            ]),
        };
    }
}
