<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('admin')->group(function(){
    Route::get('/', function () {
        return view('dashboard.dashboard');
    })->name('admin');

    Route::resource('regions', RegionController::class);

    Route::resource('products', ProductController::class);

    Route::resource('codes', GroupController::class);

    Route::post('/codes-group/{id}',[GroupController::class, 'codesAdd']);

    Route::get('/codes/export/{group}', [GroupController::class, 'export'])->name('codes.export');
});
require __DIR__.'/auth.php';
