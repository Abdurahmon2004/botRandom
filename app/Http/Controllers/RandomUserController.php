<?php

namespace App\Http\Controllers;

use App\Models\TgUser;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class RandomUserController extends Controller
{
    public function webhook(){
        $update = Telegram::getWebhookUpdates();
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
                    $this->saveName($chatId,$text,$messageId,$user);
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
                'telegram_id'=>$chatId,
                'state'=>'await_name'
            ]);
        }
        $text = 'Assalomu alaykum bizning palonchi botimizga hush kelibsiz! Ismingizni va Familiyangizni kiriting!';
        $this->sendMessage($chatId,$text,$messageId);
    }

    public function saveName($chatId,$text,$messageId,$user){
        $user->update([
            'name'=>$text
        ]);
        $message = 'Ismingiz Muvaffaqiyatli saqlandi. Endi Pastda paydo bolgan "Raqam ulashish" tugmasini bosing!';
        $btn = ['text' => 'Telefon raqamingizni kiriting', 'request_contact' => true];
        $btnName = 'keyboard';
        $this->sendMessageBtn($chatId,$message,$btn,$btnName,$messageId);
    }

    public function sendMessage($chatId,$text,$messageId){
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>$text,
        ]);
    }
    public function sendMessageBtn($chatId, $text,$btn,$btnName,$messageId){
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>$text,
            'reply_markup' => json_encode([
                $btnName => [
                    [
                        $btn
                    ],
                ],
            ]),
        ]);
    }
}
