<?php

use Illuminate\Support\Facades\Route;
use Modules\TechCompany\App\Http\Controllers\Frontend\HomeController;

// Self-contained static theme assets: Modules/TechCompany/Public/assets/{css|js}/{file}
Route::get('tech-company-assets/{type}/{file}', [HomeController::class, 'asset'])
    ->where(['type' => 'css|js', 'file' => '[A-Za-z0-9_\-\.]+'])
    ->name('techcompany.asset');
