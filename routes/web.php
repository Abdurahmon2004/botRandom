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
    TgUser::create([
        'telegram_id'=>123456,
    ]);
    dd('hello');
});
