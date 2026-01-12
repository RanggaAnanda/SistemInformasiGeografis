<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'kode_kecamatan',
        'nama_kecamatan',
        'nama_wilayah',
    ];
}
