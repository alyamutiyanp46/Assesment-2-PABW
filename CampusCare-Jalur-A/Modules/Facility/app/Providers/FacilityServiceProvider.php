<?php

namespace Modules\Facility\Providers;

use Illuminate\Support\ServiceProvider;

// Provider ini mendaftarkan route dan migration modul Facility ke Laravel.
class FacilityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'facility');
    }
}
