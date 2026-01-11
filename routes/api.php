<?php

use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FuelLogController;
use App\Http\Controllers\Api\InsuranceController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\TechnicalInspectionController;
use App\Http\Controllers\Api\VehicleAssignmentController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('vehicles', VehicleController::class);
        Route::apiResource('maintenances', MaintenanceController::class);
        Route::apiResource('fuel-logs', FuelLogController::class)->only(['index', 'store', 'show']);
        Route::apiResource('insurances', InsuranceController::class);
        Route::apiResource('technical-inspections', TechnicalInspectionController::class);
        Route::apiResource('alerts', AlertController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('vehicles/{vehicle}/assignments', [VehicleAssignmentController::class, 'store']);
    });
});
