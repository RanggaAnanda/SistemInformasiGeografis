<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'gedung_id',
        'nip',
        'nama',
        'jabatan',
        'status',
        'keterangan',
    ];

    /**
     * Pegawai bekerja di satu Gedung (home base)
     */
    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }

    public function assetBergerak()
    {
        return $this->hasMany(AssetBergerak::class);
    }

    /**
     * (Nanti) Pegawai bisa bertanggung jawab atas banyak aset
     */
    // public function assets()
    // {
    //     return $this->hasMany(AssetBergerak::class);
    // }
}
