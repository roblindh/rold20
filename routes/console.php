<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('RoL d20 rules engine is loaded and active.');
})->purpose('Display an inspiring quote');
