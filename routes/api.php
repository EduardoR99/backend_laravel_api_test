<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;
use App\Models\Redirect;
use Vinkla\Hashids\Facades\Hashids;

Route::get('/users', [RedirectController::class, 'index']);
Route::get('/r/{redirectTo}', [RedirectController::class, 'redirect']);


Route::get('/r', function(){

    return 'teste';
});