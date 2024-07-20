<?php

namespace App\Http\Controllers;

use App\Models\TgUser;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class RandomUserController extends Controller
{
    public function webhook(){
        $update = Telegram::getUpdates();
        if($update){
            $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'] ?? null;
            $text = $update['message']['text'] ?? null;
            $data = $update['callback_query']['data'] ?? null;
            $messageId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'] ?? null;
            $contact = $update['message']['contact'] ?? null;
            if($chatId && $text){
                $this->handleMessage($chatId, $text, $messageId);
            }
        }
    }
    public function handleMessage($chatId, $text, $messageId){
        $user = TgUser::where('telegram_id',$chatId)->first();
        if($user){
            switch ($user->state) {
                case 'await_name':

                break;
            }
        }else{
            switch ($text) {
                case '/start':
                    $this->start($chatId,$messageId,$user);
                    break;
            }
        }
    }


    public function start($chatId,$messageId,$user){
        if(!$user){
            TgUser::create([
                'telegram_id'=>$chatId
            ]);
        }
        $text = 'Assalomu alaykum bizning palonchi botimizga hush kelibsiz! Ismingizni va Familiyangizni kiritish uchun pastdagi tugmani bosing!';
        $btn = ['text' => 'Ism va Familiya kiritish!', 'callback_data' => 'fio'];
        $this->sendMessage($chatId,$text,$btn,$messageId);
    }
    public function sendMessage($chatId,$text,$btn,$messageId){
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>$text,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        $btn
                    ],
                ],
            ]),
        ]);
    }
}
