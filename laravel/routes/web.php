<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SeasonController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('api')->prefix('api')->group(function () {
    Route::apiResource('seasons', SeasonController::class);
});


Route::middleware('guest')->group(function () {
    Route::post('api/register', [RegisteredUserController::class, 'store']);
    Route::post('api/login', [AuthenticatedSessionController::class, 'store'])->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('api/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Toto je API pre sezóny s prefixom /api
Route::middleware('api')->prefix('api')->group(function () {
    Route::apiResource('seasons', SeasonController::class);
});