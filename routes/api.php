<?php

use App\Http\Controllers\RandomUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::any('/webhook',[RandomUserController::class,'webhook']);
