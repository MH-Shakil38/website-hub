<?php

use Illuminate\Support\Facades\Route;
use Modules\IvfCenter\App\Http\Controllers\IvfCenterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ivfcenters', IvfCenterController::class)->names('ivfcenter');
});
