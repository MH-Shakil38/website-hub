<?php

use Illuminate\Support\Facades\Route;
use Modules\HajjUmrah\App\Http\Controllers\HajjUmrahController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('hajjumrahs', HajjUmrahController::class)->names('hajjumrah');
});
