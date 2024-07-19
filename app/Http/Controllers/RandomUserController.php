<?php
namespace App\Http\Controllers;

use App\Models\code;
use App\Models\TgUser;
use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class RandomUserController extends Controller
{
    public function webhook(Request $request)
    {
        $update = Telegram::getWebhookUpdates();
        if ($update) {
            if (isset($update['message'])) {
                $chatId = $update['message']['chat']['id'];
                $text = $update['message']['text'];
                switch ($text) {
                    case '/start':
                        $this->startBot($chatId);
                        break;
                }
            }
            if($update['message']){
                $chatId = $update['message']['chat']['id'];
                $text = $update['message']['text'];
                $user = TgUser::where('telegram_id',$chatId)->where('state','await_code')->first();
                if($user){
                    $code = code::where('code',$text)->first();
                    if($code){
                        $this->enterPhone($chatId);
                    }else{
                        $this->errorCode($chatId);
                    }
                }else{
                    $this->startBot($chatId);
                }
            }
            if($update['message']){
                $chatId = $update['message']['chat']['id'];
                $text = $update['message']['text'];
                $user = TgUser::where('telegram_id',$chatId)->where('state','await_phone')->first();
                if($user){
                    $this->createPhone($user,$text);
                }else{
                    $this->startBot($chatId);
                }
            }
            if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $chatId = $callbackQuery['message']['chat']['id'];
                $data = $callbackQuery['data'];

                if ($data === 'enter_code') {
                    $this->enterCode($chatId);
                }
            }
            if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $chatId = $callbackQuery['message']['chat']['id'];
                $data = $callbackQuery['data'];

                if ($data === 'enter_phone') {
                    $this->firstPhone($chatId);
                }
            }
        }
    }

    public function startBot($chatId)
    {
        // TgUser::create([
        //     'telegram_id'=>$chatId,
        // ]);
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Assalomu alaykum, botimizga hush kelibsiz! Kodni kiritish uchun pastdagi tugmani bosing.",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Kod kiritish', 'callback_data' => 'enter_code']],
                ]
            ])
        ]);
    }
    public function errorCode($chatId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Kod xato. Iltimos, kodni qayta kiritish uchun pastdagi tugmani bosing",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Kod kiritish', 'callback_data' => 'enter_code']],
                ]
            ])
        ]);
    }

    public function enterPhone($chatId)
    {
        $user = TgUser::where('telegram_id',$chatId)->first();
        $user->update([
            'state'=>'await_phone'
        ]);
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "To\'g\'ri kod kiritdingiz. Tabriklaymiz! Telefon raqamingizni kiritish uchun pastdagi tugmani bosing",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Telefon raqam kiritish', 'callback_data' => 'enter_phone']],
                ]
            ])
        ]);
    }

    public function enterCode($chatId)
    {
        $user = TgUser::where('telegram_id',$chatId)->first();
        $user->update([
            'state'=>'await_code'
        ]);
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Kod kiritishga tayyor kodni kiriting!',
        ]);
    }
    public function firstPhone($chatId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Telefon raqam kiritishga tayyor. Namuna 934257087 raqamni shunday ko\'rinishda kiriting!',
        ]);
    }
    public function createPhone($user,$text)
    {
        $user->update([
            'phone'=>$text
        ]);
        Telegram::sendMessage([
            'chat_id' => $user->id,
            'text' => 'Telefon raqam qabul qilindi. oxirgi qadam ismingizni kiritish uchun pastdagi tugmani bosing',
        ]);
    }
}
