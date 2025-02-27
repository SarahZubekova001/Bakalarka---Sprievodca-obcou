<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\HikingController;
use App\Http\Controllers\GalleryController;
use App\Models\Category;
use App\Models\Season;
use Illuminate\Support\Facades\Route;


// RESOURCE ROUTES
Route::resource('galleries', GalleryController::class);
Route::resource('hiking', HikingController::class);
Route::resource('restaurants', RestaurantController::class);
Route::resource('posts', PostController::class);
Route::resource('categories', CategoryController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('seasons', SeasonController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);


//  ADMIN PANEL ROUTE
Route::get('/admin', function () {
    return view('admin_panel', [
        'categories' => Category::all(),
        'seasons' => Season::all()
    ]);
});

// IMAGE UPLOAD & DELETE ROUTES
// Uloženie nových obrázkov (pridávanie obrázkov do galérie)
Route::post('/images', [ImageController::class, 'store'])->name('images.store');

// Odstránenie obrázkov (opravená route na správny controller)
Route::delete('/galleries/images/{image}', [GalleryController::class, 'deleteImage'])->name('galleries.deleteImage');

    
Route::get('/addresses', [AddressController::class, 'create'])->name('addresses.create');
Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
