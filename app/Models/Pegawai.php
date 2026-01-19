<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'gedung_id',
        'nip',
        'nama',
        'gedung_id',
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * (Nanti) Pegawai bisa bertanggung jawab atas banyak aset
     */
    // public function assets()
    // {
    //     return $this->hasMany(AssetBergerak::class);
    // }
}
