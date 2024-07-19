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
    $user = TgUser::where('telegram_id',6054214655)->first();
    $user->update([
        'state'=>'await_phone'
    ]);
    dd('hello');
});
