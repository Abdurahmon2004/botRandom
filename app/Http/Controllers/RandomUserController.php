<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Region;
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
            if($chatId && $data){
                $this->handleCallbackQuery($chatId,$data,$messageId);
            }
            if($chatId && $contact){
                $this->savePhone($chatId,$contact,$messageId);
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

    public function handleCallbackQuery($chatId,$data,$messageId){
        $user = TgUser::where('telegram_id',$chatId)->first();
        if (strpos($data, 'region_') === 0) {
            $regionId = str_replace('region_', '', $data);
            $this->saveRegion($chatId, $regionId,$user,$messageId);
        }
        // switch ($data) {
        //     case '':

        //         break;
        // }
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
            'name'=>$text,
            'state'=>'await_phone',
        ]);
        $message = 'Ismingiz Muvaffaqiyatli saqlandi. Endi Pastda paydo bolgan "Raqam ulashish" tugmasini bosing!';
        $btn = ['text' => 'Telefon raqamingizni kiriting', 'request_contact' => true];
        $btnName = 'keyboard';
        $this->sendMessageBtn($chatId,$message,$btn,$btnName,$messageId);
    }

    public function savePhone($chatId,$contact,$messageId){
        $user = TgUser::where('telegram_id',$chatId)->first();
        $user->update([
            'phone'=>$contact['phone_number'],
            'state'=>'await_region'
        ]);
        $regions = Region::where('status',1)->get();
        $inlineKeyboard = [];
        foreach ($regions as $region) {
            $inlineKeyboard[] = [
                [
                    'text' => $region->name,
                    'callback_data' => 'product_' . $region->id,
                ],
            ];
        }
        $text = 'Telefon raqam muvaffaqiyatli saqlandi. Pastdagi royhatdan Viloyatingizni tanlang!';
        $btnName = 'inline_keyboard';
        $this->sendMessageBtn($chatId,$text,$inlineKeyboard,$btnName,$messageId);
    }

    public function saveRegion($chatId, $regionId,$user,$messageId)
    {
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
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ]),
        ]);
    }
}
