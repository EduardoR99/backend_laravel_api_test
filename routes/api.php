<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\RedirectLogController;
use App\Services\RedirectService;


Route::prefix('/redirects')->group(function () {
    Route::get('/', [RedirectController::class, 'index']);
    Route::post('/', [RedirectController::class, 'store']);
    Route::put('/{id}', [RedirectController::class, 'update']);
    Route::put('/desativar/{id}', [RedirectController::class, 'destroy']);
    
    Route::get('{redirect_id_code}/logs', [RedirectLogController::class, 'index']);
    Route::get('{redirect_id_code}/stats', [RedirectLogController::class, 'stats']);
});

Route::get('/r/{redirectTo}', [RedirectService::class, 'redirect']);
