<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnterpriseController;

Route::post('/enterprise/register', [EnterpriseController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);