<?php

use Illuminate\Support\Facades\Route;
use Modules\Interior\App\Http\Controllers\InteriorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('interiors', InteriorController::class)->names('interior');
});
