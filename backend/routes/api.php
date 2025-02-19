<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnterpriseController;

Route::post('/enterprise/register', [EnterpriseController::class, 'store']);