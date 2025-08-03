<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    OrganizationController,
    BuildingController,
    ActivityController
};

Route::middleware('auth.apikey')->group(function () {
    Route::get('/buildings', [BuildingController::class, 'index']);
    Route::get('/buildings/{id}/organizations', [OrganizationController::class, 'byBuilding']);
    Route::get('/activities/{id}/organizations', [OrganizationController::class, 'byActivity']);
    Route::get('/organizations/search', [OrganizationController::class, 'search']);
    Route::get('/organizations/nearby', [OrganizationController::class, 'nearby']);
    Route::get('/organizations/{id}', [OrganizationController::class, 'show']);
});

