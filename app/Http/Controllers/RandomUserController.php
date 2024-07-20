<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Region;
use App\Models\TgUser;
use App\Models\UserChat;
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
                $user = TgUser::where('telegram_id', $chatId)->where('state', 'await_phone')->first();
                if ($user) {
                    $this->savePhone($chatId, $contact, $text, $messageId);
                }
            }
        }
    }
    public function handleMessage($chatId, $text, $messageId)
    {
        $user = TgUser::where('telegram_id', $chatId)->first();

        if ($user) {
            if($text == '/start'){
                if($user->state == 'await_region'){
                    $this->savePhone($chatId,false,null,$messageId);
                }
            }
            switch ($user->state) {
                case 'await_fio':
                    $this->phoneMessageSaveName($chatId, $text, $messageId);
                break;
                // case 'await_code':
                //     $this->saveCode($chatId, $text, $messageId);
                // break;
            }
        } else {
            if ($text == '/start') {
                $this->startMessage($chatId, false,$messageId);
            }
        }
    }
    public function handleCallbackQuery($chatId, $data, $messageId)
    {
        if (strpos($data, 'region_') === 0) {
            $regionId = str_replace('region_', '', $data);
            $this->saveRegion($chatId, $regionId,$messageId);
        } else {
            switch ($data) {
                case 'fio':
                    $this->nameAwait($chatId, $messageId);
                    break;
            }
        }
    }
    public function startMessage($chatId, $user,$messageId)
    {
        if (!$user) {
            TgUser::create([
                'telegram_id' => $chatId,
            ]);
        }
        $this->deleteMessage($chatId, $messageId);

        $message = Telegram::sendMessage([
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
        $this->storeMessageId($chatId, $message['message_id']);
    }

    public function nameAwait($chatId, $messageId)
    {
        $this->deleteMessage($chatId, $messageId);
        $user = TgUser::where('telegram_id', $chatId)->first();
        $user->update([
            'state' => 'await_fio',
        ]);
        $message = 'Ism va Familiyangizni kiriting!!';
        $this->sendMessage($chatId, $message, $messageId);
    }
    public function phoneMessageSaveName($chatId, $text, $messageId)
    {
        if ($text == '/start') {
            $this->startMessage($chatId, true,$messageId);
            return;
        }
        $this->deleteMessage($chatId, $messageId);
        $this->deleteMessage($chatId, $messageId-1);
        $user = TgUser::where('telegram_id', $chatId)->first();
        if ($text != '/start') {
            $user->update([
                'name' => $text,
                'state' => 'await_phone',
            ]);
        }
        $message = Telegram::sendMessage([
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
        $this->storeMessageId($chatId, $message['message_id']);

    }
    public function savePhone($chatId, $contact, $text, $messageId)
    {
        if ($text == '/start') {
            $this->phoneMessageSaveName($chatId, false, $messageId);
            return;
        }
        $this->deleteMessage($chatId, $messageId);
        $this->deleteMessage($chatId, $messageId-1);
        $user = TgUser::where('telegram_id', $chatId)->first();
        $phone = $contact['phone_number'];
        $user->update([
            'phone' => $phone,
            'state' => 'await_region',
        ]);
        $regions = Region::where('status', 1)->get();
        $inlineKeyboard = [];

        foreach ($regions as $region) {
            $inlineKeyboard[] = [
                [
                    'text' => $region->name,
                    'callback_data' => 'region_' . $region->id,
                ],
            ];
        }
        $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Telefon raqam muvaffaqiyatli saqlandi. Pastdagi royhatdan Viloyatingizni tanlang!',
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
        ]);
        $this->storeMessageId($chatId, $message['message_id']);
    }
    public function saveRegion($chatId, $regionId,$messageId)
    {
        $this->deleteMessage($chatId, $messageId);
        $this->deleteMessage($chatId, $messageId-1);
        $user = TgUser::where('telegram_id', $chatId)->first();
        $region = Region::find($regionId);

        if ($region) {
            $user->update([
                'region_id' => $region->id,
                'state' => 'await_code',
            ]);

            $text = "Viloyatingiz muvaffaqiyatli saqlandi. Qaysi maxsulotni sotib olganizni tanlang. Pastdagi tugmalar orqali!";
            $products = Product::where('status', 1)->get();
            $inlineKeyboard = [];
            foreach ($products as $product) {
                $inlineKeyboard[] = [
                    [
                        'text' => $product->name,
                        'callback_data' => 'product_' . $product->id,
                    ],
                ];
            }
        } else {
            $text = "Noma'lum viloyat.";
        }


        $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard]),
        ]);
        $this->storeMessageId($chatId, $message['message_id']);
    }

    public function sendMessage($chatId, $message, $messageId)
    {
        $this->deleteMessage($chatId, $messageId);
        $message = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
        $this->storeMessageId($chatId, $message['message_id']);
    }
    private function deleteMessage($chatId, $messageId)
    {
         UserChat::where('chat_id',$chatId)->delete();
    }

    private function storeMessageId($chatId, $messageId)
    {
        $user = TgUser::where('telegram_id', $chatId)->first();
        if($user){
            UserChat::create([
                'chat_id'=>$chatId,
                'message_id'=>$messageId,
            ]);
        }
    }
}

//
