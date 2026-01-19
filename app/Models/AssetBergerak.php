<?php

namespace App\Models;

use App\Models\JenisAsset;
use Illuminate\Database\Eloquent\Model;

class AssetBergerak extends Model
{
    protected $table = 'asset_bergerak';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'jenis_asset_id',
        'kategori_asset_id',
        'gedung_id',
        'pegawai_id',
        'status',
        'value',
    ];

    protected $casts = ['value' => 'array'];

    public function jenis()
    {
        return $this->belongsTo(JenisAsset::class);
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }

    public function mutasi()
    {
        return $this->hasMany(MutasiAsset::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriAsset::class, 'kategori_asset_id');
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
