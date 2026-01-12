<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiAsset extends Model
{
    protected $table = 'mutasi_asset' ;

    protected $fillable = [
        'movable_asset_id',
        'from_building_id',
        'to_building_id',
        'requested_by',
        'approved_by',
        'status',
        'tanggal_pengajuan',
        'tanggal_disetujui'
    ];
}
