<?php
namespace App\Http\Controllers;

use App\Models\code;
use App\Models\TgUser;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class RandomUserController extends Controller
{
    public function webhook(Request $request)
    {
        $update = Telegram::getWebhookUpdates();
        if ($update) {
            $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'] ?? null;
            $text = $update['message']['text'] ?? null;
            $data = $update['callback_query']['data'] ?? null;
            $messageId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'] ?? null;

            if ($chatId && $text) {
                $this->handleMessage($chatId, $text, $messageId);
            }

            if ($chatId && $data) {
                $this->handleCallbackQuery($chatId, $data, $messageId);
            }
        }
    }
    public function handleMessage($chatId,$text, $messageId){
        $user = TgUser::where('telegram_id',$chatId)->first();
        if($user){
            switch ($user->state) {
                case 'await_fio':
                    $this->saveName($chatId,$text,$messageId);
                break;
            }
        }else{
            $this->startMessage($chatId);
        }
    }
    public function handleCallbackQuery($chatId,$data, $messageId){
        switch ($data) {
            case 'fio':
                $this->nameAwait($chatId,$messageId);
                break;

            default:
                # code...
                break;
        }
    }
    public function startMessage($chatId){
        TgUser::create([
            'telegram_id'=>$chatId
        ]);
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>'Assalomu alaykum bizning palonchi botimizga hush kelibsiz! Ismingizni va Familiyangizni kiritish uchun pastdagi tugmani bosing!',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Ism va Familiya kiritish', 'callback_data' => 'fio']],
                ]
            ])
        ]);
    }

    public function nameAwait($chatId,$messageId){
        $user = TgUser::where('telegram_id',$chatId)->first();
        $user->update([
            'state'=>'await_fio'
        ]);
        $message = 'Ism va Familiyangizni kiriting!!';
        $this->sendMessage($chatId,$message,$messageId);
    }
    public function saveName($chatId,$text,$messageId){

    }
    public function phoneMessage($chatId,$messageId){
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>'Assalomu alaykum bizning palonchi botimizga hush kelibsiz! Ismingizni va Familiyangizni kiritish uchun pastdagi tugmani bosing!',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    ['text' => 'Ism va Familiya kiritish', 'callback_data' => 'phone'],
                ]
            ])
        ]);
    }
    public function sendMessage($chatId, $message,$messageId){
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>$message,
        ]);
    }
    private function deleteMessage($chatId, $messageId)
{
    Telegram::deleteMessage([
        'chat_id' => $chatId,
        'message_id' => $messageId,
    ]);
}

private function storeMessageId($chatId, $messageId)
{
    $user = TgUser::where('telegram_id', $chatId)->first();
    $user->update(['last_message_id' => $messageId]);
}
}

//
