<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('izobo:about', function () {
    $this->info('IZOBO campaign application');
})->purpose('Show application information');
