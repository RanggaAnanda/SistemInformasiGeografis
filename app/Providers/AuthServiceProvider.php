<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\MutasiAsset;
use App\Policies\MutasiAssetPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        MutasiAsset::class => MutasiAssetPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
