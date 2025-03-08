<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    Route::post('api/register', [RegisteredUserController::class, 'store']);
    Route::post('api/login', [AuthenticatedSessionController::class, 'store'])->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('api/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


Route::middleware('api')->prefix('api')->group(function () {
    Route::apiResource('seasons', SeasonController::class);
    Route::apiResource('restaurants', RestaurantController::class);
});