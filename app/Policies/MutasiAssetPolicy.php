<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MutasiAsset;

class MutasiAssetPolicy
{
    /**
     * SUPER ADMIN BYPASS
     * Super admin boleh melakukan semua aksi
     */
    public function before(User $user, string $ability)
    {
        if ($user->role === 'super_admin') {
            return true;
        }
    }

    /**
     * Lihat daftar mutasi
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'pegawai',
            'admin_asset',
        ]);
    }

    /**
     * Lihat detail mutasi
     */
    public function view(User $user, MutasiAsset $mutasi): bool
    {
        // Pegawai hanya boleh lihat mutasi miliknya sendiri
        if ($user->role === 'pegawai') {
            return $mutasi->requested_by === $user->id;
        }

        return true;
    }

    /**
     * Mengajukan mutasi
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            'pegawai',
            'admin_asset',
        ]);
    }

    /**
     * Approve mutasi
     */
    public function approve(User $user, MutasiAsset $mutasi): bool
    {
        // Admin asset tidak boleh approve mutasi sendiri
        if ($mutasi->requested_by === $user->id) {
            return false;
        }

        return $user->role === 'admin_asset';
    }

    /**
     * Reject mutasi
     */
    public function reject(User $user, MutasiAsset $mutasi): bool
    {
        return $this->approve($user, $mutasi);
    }
}
