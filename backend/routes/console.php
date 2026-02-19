<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Simplicity is the ultimate sophistication.');
})->purpose('Display an inspiring quote');
