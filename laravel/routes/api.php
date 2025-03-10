<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json($request->user());
});

use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{id}/toggle-role', [UserController::class, 'toggleRole']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});




// Route::post('/register', [RegisteredUserController::class, 'store']);
// Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login');



// Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');




