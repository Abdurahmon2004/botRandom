<?php
namespace App\Http\Controllers;

use App\Models\Region;
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
            $contact = $update['message']['contact'] ?? null;
            if ($chatId && $text) {
                $this->handleMessage($chatId, $text, $messageId);
            }

            if ($chatId && $data) {
                $this->handleCallbackQuery($chatId, $data, $messageId);
            }
            if ($chatId && $contact) {
                $user = TgUser::where('telegram_id',$chatId)->where('state','await_phone')->first();
                if($user){
                    if($text == '/start'){
                        $this->phoneMessageSaveName($chatId,null,$messageId);
                    }
                    $this->savePhone($chatId, $contact);
                }
            }
        }
    }
    public function handleMessage($chatId, $text, $messageId)
    {
        $user = TgUser::where('telegram_id', $chatId)->first();
        if ($user) {

            switch ($user->state) {
                case 'await_fio':
                    if($text == '/start'){
                        $this->startMessage($chatId);
                    }
                    $this->phoneMessageSaveName($chatId, $text, $messageId);
                break;
            }
        } else {
            if ($text == '/start') {
                $this->startMessage($chatId);
            }
        }
    }
    public function handleCallbackQuery($chatId, $data, $messageId)
    {

        switch ($data) {
            case 'fio':
                $this->nameAwait($chatId, $messageId);
            break;
        }
    }
    public function startMessage($chatId)
    {
        TgUser::create([
            'telegram_id' => $chatId,
        ]);
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Assalomu alaykum bizning palonchi botimizga hush kelibsiz! Ismingizni va Familiyangizni kiritish uchun pastdagi tugmani bosing!',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Ism va Familiya kiritish!', 'callback_data' => 'fio'],
                    ],
                ],
            ]),
        ]);
    }

    public function nameAwait($chatId, $messageId)
    {
        $user = TgUser::where('telegram_id', $chatId)->first();
        $user->update([
            'state' => 'await_fio',
        ]);
        $message = 'Ism va Familiyangizni kiriting!!';
        $this->sendMessage($chatId, $message, $messageId);
    }
    public function phoneMessageSaveName($chatId, $text, $messageId)
    {
        $user = TgUser::where('telegram_id', $chatId)->first();
       if($text != null){
        $user->update([
            'name' => $text,
            'state' => 'await_phone',
        ]);
       }
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Ismingiz Muvaffaqiyatli saqlandi. Endi Pastda paydo bolgan "Raqam ulashish tugmasini bosing!"',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => 'Telefon raqamingizni kiriting', 'request_contact' => true],
                    ],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ]),
        ]);
    }
    public function savePhone($chatId, $contact)
    {
        $user = TgUser::where('telegram_id', $chatId)->first();
        $phone = $contact['phone_number'];
        $user->update([
            'phone' => $phone,
        ]);
        $regions = Region::all();
        $inlineKeyboard = [];

        foreach ($regions as $region) {
            $inlineKeyboard[] = [
                [
                    'text' => $region->name,
                    'callback_data' => 'region_' . $region->id,
                ]
            ];
        }
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Telefon raqam muvaffaqiyatli saqlandi. Pastdagi royhatdan Viloyatingizni tanlang!',
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
        ]);

    }
    public function sendMessage($chatId, $message, $messageId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
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
