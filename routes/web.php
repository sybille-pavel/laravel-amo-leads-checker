<?php

use App\Http\Middleware\CheckAmoAuth;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;


Route::get('/', function () {
    return view('welcome');
})->middleware(CheckAmoAuth::class);

Route::get('/auth/redirect', [AuthController::class, 'redirect']);
Route::get('/auth/callback', [AuthController::class, 'callback']);
Route::get('/leads', [LeadController::class, 'index']);

Route::prefix('/api')->group(function () {
    Route::get('/statuses', [LeadController::class, 'statuses']);
    Route::get('/leads', [LeadController::class, 'api']);
});
