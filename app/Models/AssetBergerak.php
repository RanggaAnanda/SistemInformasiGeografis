<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetBergerak extends Model
{
    protected $table = 'asset_bergerak' ;

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'jenis',
        'gedung_id',
        'status',
        'kategori_asset_id',
        'value'
    ];

    protected $casts = ['value'=>'array'];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }

    public function mutations()
    {
        return $this->hasMany(MutasiAsset::class);
    }

    public function kategori(){
        return $this->belongsTo(KategoriAsset::class,'kategori_asset_id');
    }
}
