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

    public function handleMessage($chatId, $text, $messageId){
        $user = TgUser::where('telegram_id',$chatId)->first();
        if($user){
            switch ($user->state) {
                case 'await_code':
                    $this->processCode($chatId, $text, $messageId);
                    break;
                case 'await_phone':
                    $this->createPhone($user, $text, $messageId);
                    break;
                case 'await_name':
                    $this->finish($chatId, $messageId);
                    break;
            }
        }else if($text == '/start'){
            $this->startBot($chatId);
        }
    }
    private function handleCallbackQuery($chatId, $data, $messageId)
    {
        switch ($data) {
            case 'enter_code':
                $this->enterCode($chatId, $messageId);
                break;
            case 'enter_phone':
                $this->firstPhone($chatId, $messageId);
                break;
            case 'enter_name':
                $this->enterName($chatId, $messageId);
                break;
        }
    }

    public function processCode($chatId, $text, $messageId){

        $code = code::where('code', $text)->first();

        if ($code) {
            $this->enterPhone($chatId, $messageId);
        }else{
            $this->errorCode($chatId, $messageId);
        }
    }
    public function startBot($chatId)
    {
      $user = TgUser::where('telegram_id',$chatId)->first();
       if(!$user){
        TgUser::create([
            'telegram_id'=>$chatId
        ]);
       }
       $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Assalomu alaykum, botimizga hush kelibsiz! Kodni kiritish uchun pastdagi tugmani bosing.",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Kod kiritish', 'callback_data' => 'enter_code']],
                ]
            ])
        ]);
        $this->storeMessageId($chatId, $message['message_id']);
    }
    public function errorCode($chatId, $messageId)
    {
        $this->deleteMessage($chatId, $messageId);
       $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "Kod xato. Iltimos, kodni qayta kiritish uchun pastdagi tugmani bosing",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Kod kiritish', 'callback_data' => 'enter_code']],
                ]
            ])
        ]);
        $this->storeMessageId($chatId, $message['message_id']);
    }

    public function enterPhone($chatId,$messageId)
    {
        $this->deleteMessage($chatId, $messageId);
       $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "To\'g\'ri kod kiritdingiz. Tabriklaymiz! Telefon raqamingizni kiritish uchun pastdagi tugmani bosing",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Telefon raqam kiritish', 'callback_data' => 'enter_phone']],
                ]
            ])
        ]);
        $this->storeMessageId($chatId, $message['message_id']);

    }

    public function enterCode($chatId, $messageId)
    {
        $user = TgUser::where('telegram_id',$chatId)->first();
        $user->update([
            'state'=>'await_code'
        ]);
        $this->deleteMessage($chatId, $messageId);
        $this->deleteMessage($chatId, $messageId-1);
       $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Kod kiritishga tayyor kodni kiriting!',
        ]);
        $this->storeMessageId($chatId, $message['message_id']);

    }
    public function firstPhone($chatId, $messageId)
    {
        $this->deleteMessage($chatId, $messageId);
        $user = TgUser::where('telegram_id',$chatId)->first();
        $user->update([
            'state'=>'await_phone'
        ]);
       $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Telefon raqam kiritishga tayyor. Namuna 934257087 raqamni shunday ko\'rinishda kiriting!',
        ]);
        $this->storeMessageId($chatId, $message['message_id']);

    }
    public function enterName($chatId, $messageId)
    {
        $this->deleteMessage($chatId, $messageId);
        $user = TgUser::where('telegram_id',$chatId)->first();
        $user->update([
            'state'=>'await_name'
        ]);
       $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Ismingizni kiriting',
        ]);
        $this->storeMessageId($chatId, $message['message_id']);

    }
    public function createPhone($user,$text, $messageId)
    {
        $user->update([
            'phone'=>$text
        ]);
        $this->deleteMessage($user->telegram_id, $messageId);
       $message = Telegram::sendMessage([
            'chat_id' => $user->telegram_id,
            'text' => 'Telefon raqam qabul qilindi. oxirgi qadam ismingizni kiritish uchun pastdagi tugmani bosing',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Ismni kiritish', 'callback_data' => 'enter_name']],
                ]
            ])
        ]);
        $this->storeMessageId($user->telegram_id, $message['message_id']);

    }
    public function finish($chatId, $messageId){
        $this->deleteMessage($chatId, $messageId);
       $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Hammasi muvaffaqiyatli boldi. Kodingiz omadli bolsa oyin bolib otganidan song sovrin yutib olasiz. sizga adminlarimiz aloqaga chiqishadi',
        ]);
        $this->storeMessageId($chatId, $message['message_id']);

    }
    private function storeMessageId($chatId, $messageId)
    {
        $user = TgUser::where('telegram_id', $chatId)->first();
        $user->update(['last_message_id' => $messageId]);
    }

    private function deleteMessage($chatId, $messageId)
    {
        Telegram::deleteMessage([
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }
}

//
