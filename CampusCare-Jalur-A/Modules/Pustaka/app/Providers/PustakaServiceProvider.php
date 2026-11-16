<?php

namespace Modules\Pustaka\Providers;

use Illuminate\Support\ServiceProvider;

// Provider ini menjadi jembatan Laravel ke route dan migration milik modul.
class PustakaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
