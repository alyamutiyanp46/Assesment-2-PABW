<?php

return [
    App\Providers\AppServiceProvider::class,
    // Tiap provider mendaftarkan route, migration, dan view untuk modul HMVC-nya.
    Modules\Facility\Providers\FacilityServiceProvider::class,
    Modules\Complaint\Providers\ComplaintServiceProvider::class,
];
