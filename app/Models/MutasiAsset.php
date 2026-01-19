<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiAsset extends Model
{
    protected $table = 'mutasi_asset';

    protected $fillable = [
        'asset_bergerak_id',
        'from_gedung_id',
        'to_gedung_id',
        'from_pegawai_id',
        'to_pegawai_id',
        'jenis_mutasi',
        'status',
        'catatan',
        'requested_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(AssetBergerak::class, 'asset_bergerak_id');
    }
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function fromGedung()
    {
        return $this->belongsTo(Gedung::class, 'from_gedung_id');
    }

    public function toGedung()
    {
        return $this->belongsTo(Gedung::class, 'to_gedung_id');
    }

    public function fromPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'from_pegawai_id');
    }

    public function toPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'to_pegawai_id');
    }
}
