<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnterpriseController;
use App\Http\Middleware\CheckModuleAuthority;

Route::post('/enterprise/register', [EnterpriseController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Routes d'authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Routes d'entreprise nécessitant des autorisations
    Route::middleware([CheckModuleAuthority::parameters('enterprise', 'read')])
        ->group(function () {
            Route::get('/enterprise/show', [EnterpriseController::class, 'show']);
        });
});