<?php

return [
    App\Providers\AppServiceProvider::class,
    // Modul HMVC memuat route dan migration dari folder Modules/Pustaka.
    Modules\Pustaka\Providers\PustakaServiceProvider::class,
];
