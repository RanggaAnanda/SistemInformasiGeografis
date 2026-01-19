<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriAsset extends Model
{
    protected $table = 'kategori_asset';

    protected $fillable = [
        'nama_kategori',
        'jenis_asset_id',
        'fields'
    ];
    protected $casts = [
        'fields' => 'array', 
    ];

    public function jenis()
    {
        return $this->belongsTo(JenisAsset::class, 'jenis_asset_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(AssetBergerak::class, 'asset_category_id');
    }
}
