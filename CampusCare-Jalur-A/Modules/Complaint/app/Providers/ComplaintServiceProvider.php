<?php

namespace Modules\Complaint\Providers;

use Illuminate\Support\ServiceProvider;

// Provider ini membuat route, migration, dan view modul Complaint tersedia.
class ComplaintServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'complaint');
    }
}
