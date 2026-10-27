<?php

use Illuminate\Support\Facades\Route;
use Modules\Pharmacy\App\Http\Controllers\PharmacyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacies', PharmacyController::class)->names('pharmacy');
});
