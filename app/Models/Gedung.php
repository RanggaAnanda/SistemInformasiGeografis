<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    protected $table = 'gedung' ;
    
    protected $fillable = [
        'district_id',
        'nama_gedung',
        'jenis',
        'alamat',
        'latitude',
        'longitude'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function movableAssets()
    {
        return $this->hasMany(AssetBergerak::class);
    }
}
