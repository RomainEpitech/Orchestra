<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\PersonnelModuleController;
use App\Http\Middleware\CheckModuleAuthority;

Route::post('/enterprise/register', [EnterpriseController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/update', [AuthController::class, 'updateProfile']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword']);
    
    Route::middleware([CheckModuleAuthority::parameters('enterprise', 'read')])
        ->group(function () {
            Route::get('/enterprise/show', [EnterpriseController::class, 'show']);
        });
    
    Route::middleware([CheckModuleAuthority::parameters('personnel', 'read')])
        ->group(function () {
            Route::get('/personnel/licence', [PersonnelModuleController::class, 'getAllLicenses']);
            Route::get('/personnel/licence/{userUuid}', [PersonnelModuleController::class, 'getUserLicense']);
        });

    Route::middleware([CheckModuleAuthority::parameters('personnel', 'create')])
        ->post('/personnel/licence', [PersonnelModuleController::class, 'createLicense']);
    Route::middleware([CheckModuleAuthority::parameters('personnel', 'delete')])
        ->delete('/personnel/licence/{userUuid}', [PersonnelModuleController::class, 'deleteLicense']);
});