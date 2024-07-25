<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RandomUserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WinnerController;
use App\Models\UserChat;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('admin')->group(function(){
    Route::get('/', [DashboardController::class,'index'])->name('admin');

    Route::get('/com', function () {
       Artisan::call('optimize');
       Artisan::call('migrate');
       dd('hello');
    });
    Route::resource('regions', RegionController::class);

    Route::resource('products', ProductController::class);

    Route::resource('codes', GroupController::class);

    Route::get('/allCodes/{id}', [GroupController::class, 'allCodes'])->name('allCodes');

    Route::post('/codes-group/{id}',[GroupController::class, 'codesAdd']);

    Route::get('/codes/export/{group}', [GroupController::class, 'export'])->name('codes.export');

    Route::get('/users', [UserController::class, 'index'])->name('users');

    Route::get('/UserCodes', [UserController::class, 'codeUser'])->name('usersCodes');

    Route::get('/codeDelete/{id}', [UserController::class, 'deleteCode']);

    Route::resource('/winner-groups', WinnerController::class);

    Route::get('/winnerUsers/{id}', [WinnerController::class, 'userIndex'])->name('winnerUsers.index');
    Route::post('/winner/store/{id}', [WinnerController::class, 'saveWinners'])->name('winner.store');


    Route::resource('channels', ChannelController::class)->except(['create', 'edit', 'show']);

});
require __DIR__.'/auth.php';
