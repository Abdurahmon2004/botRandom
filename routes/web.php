<?php

use App\Models\TgUser;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $users = TgUser::all();
    return view('welcome',compact('users'));
});
Route::get('/com', function () {
    Artisan::call('optimize');
    Artisan::call('migrate');
    dd('hello');
});
