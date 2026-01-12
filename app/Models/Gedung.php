<?php

namespace App\Models;

use App\Models\OperatingHour;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    protected $table = 'gedung';

    protected $fillable = [
        'district_id',
        'nama_gedung',
        'jenis',
        'alamat',
        'latitude',
        'longitude',
        'status',
        'is_public',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function movableAssets()
    {
        return $this->hasMany(AssetBergerak::class);
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }


    public function operatingHours()
    {
        return $this->hasMany(OperatingHour::class);
    }

    public function isOpenNow(): bool
    {
        if ($this->status !== 'aktif') {
            return false;
        }

        $now = Carbon::now();
        $today = $now->dayOfWeek;

        $hour = $this->operatingHours
            ->where('day_of_week', $today)
            ->first();

        if (!$hour || $hour->is_closed) {
            return false;
        }

        return $now->between(
            Carbon::parse($hour->open_time),
            Carbon::parse($hour->close_time)
        );
    }

    protected static function booted()
    {
        static::created(function (Gedung $gedung) {

            $defaultHours = [
                0 => ['closed' => true],  // Minggu
                1 => ['open' => '08:00', 'close' => '16:00'],
                2 => ['open' => '08:00', 'close' => '16:00'],
                3 => ['open' => '08:00', 'close' => '16:00'],
                4 => ['open' => '08:00', 'close' => '16:00'],
                5 => ['open' => '08:00', 'close' => '16:00'],
                6 => ['closed' => true],  // Sabtu
            ];

            foreach ($defaultHours as $day => $config) {
                $gedung->operatingHours()->create([
                    'day_of_week' => $day,
                    'open_time'   => $config['open'] ?? null,
                    'close_time'  => $config['close'] ?? null,
                    'is_closed'   => $config['closed'] ?? false,
                ]);
            }
        });
    }
}
