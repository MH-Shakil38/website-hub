<?php

use Illuminate\Support\Facades\Route;
use Modules\Clinic\App\Http\Controllers\Frontend\HomeController;

// Self-contained static theme assets: Modules/Clinic/Public/assets/{css|js}/{file}
Route::get('clinic-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('clinic.asset');
