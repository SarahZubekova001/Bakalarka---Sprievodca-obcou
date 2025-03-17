<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MainInfoController;
use App\Http\Controllers\AdditionalInfoController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\EventController;
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
    Route::apiResource('images', ImageController::class);
    Route::apiResource('seasons', SeasonController::class);
    Route::apiResource('restaurants', RestaurantController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::get('/maininfo', [MainInfoController::class, 'showSingle']);
    Route::post('/maininfo', [MainInfoController::class, 'storeOrUpdate']);
    Route::apiResource('additional-info', AdditionalInfoController::class);
    Route::apiResource('events', EventController::class);
});