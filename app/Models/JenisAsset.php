<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisAsset extends Model
{
    protected $table = 'jenis_asset';

    protected $fillable = [
        'kode',
        'nama_jenis',
        'keterangan',
    ];

    public function kategori(): HasMany
    {
        return $this->hasMany(KategoriAsset::class);
    }
}

