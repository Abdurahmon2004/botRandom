<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\CodeUser;
use App\Models\Product;
use App\Models\ProductUser;
use App\Models\Region;
use App\Models\TgUser;
use App\Models\UserChat;
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
            // botga qayta start bosib yuborsa
            if($text == '/start'){
                switch ($user->state) {
                    case 'await_name':
                        $this->start($chatId,$messageId,$user);
                    break;
                    case 'await_phone':
                        $this->saveName($chatId,false,$messageId,$user);
                    break;
                    case 'await_region':
                        $this->savePhone($chatId,false,$messageId);
                    break;
                    case 'await_product':
                        $this->saveRegion($chatId, $user->region_id,false,$messageId);
                    break;
                    case 'await_code':
                        $this->Code($chatId, $text,$user,$messageId);
                    break;
                }
            }

           if($text != '/start'){
            switch ($user->state) {
                case 'await_name':
                    $this->saveName($chatId,$text,$messageId,$user);
                break;
                case 'await_code':
                    $this->codeSave($chatId,$text,$messageId,$user);
                break;
            }
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
        if (strpos($data, 'product_') === 0) {
            $productId = str_replace('product_', '', $data);
            $this->saveProduct($chatId, $productId,$user,$messageId);
        }
        if($data == 'code'){
            $this->Code($chatId, $data,$user,$messageId);
        }
    }


    public function start($chatId,$messageId,$user)
    {

        $text = 'Assalomu alaykum bizning palonchi botimizga hush kelibsiz! Ismingizni va Familiyangizni kiriting!';
        $this->sendMessage($chatId,$text,$messageId);
    }

    public function saveName($chatId,$text,$messageId,$user)
    {
       if($text){
        $user->update([
            'name'=>$text,
            'state'=>'await_phone',
        ]);
       }
        $message = 'Ismingiz Muvaffaqiyatli saqlandi. Endi Pastda paydo bolgan "Raqam ulashish" tugmasini bosing!';
        $btn = [[['text' => 'Telefon raqamingizni kiriting', 'request_contact' => true]]];
        $btnName = 'keyboard';
        $this->sendMessageBtn($chatId,$message,$btn,$btnName,$messageId);
    }

    public function savePhone($chatId,$contact,$messageId)
    {
        $user = TgUser::where('telegram_id',$chatId)->first();
        if($contact){
            $user->update([
                'phone'=>$contact['phone_number'],
                'state'=>'await_region'
            ]);
        }
        $regions = Region::where('status',1)->get();
        $btn = [];
        foreach ($regions as $region) {
            $btn[] = [
                [
                    'text' => $region->name,
                    'callback_data' => 'region_' . $region->id,
                ],
            ];
        }
        $message = 'Telefon raqam muvaffaqiyatli saqlandi. Pastdagi royhatdan Viloyatingizni tanlang!';
        $btnName = 'inline_keyboard';
        $this->sendMessageBtn($chatId,$message,$btn,$btnName,$messageId);
    }

    public function saveRegion($chatId, $regionId,$user,$messageId)
    {
        $region = Region::find($regionId);
        if ($region) {
           if($user){
            $user->update([
                'region_id' => $region->id,
                'state' => 'await_product',
            ]);
           }

            $message = "Viloyatingiz muvaffaqiyatli saqlandi. Pastdagi tugmalar orqali! Qaysi maxsulotni sotib olganizni tanlang. ";
            $products = Product::where('status', 1)->get();
            $btn = [];
            foreach ($products as $product) {
                $btn[] = [
                    [
                        'text' => $product->name,
                        'callback_data' => 'product_' . $product->id,
                    ],
                ];
            }
        } else {
            $text = "Noma'lum viloyat.";
            $this->savePhone($chatId,false,$messageId);
        }
        $btnName = 'inline_keyboard';
        $this->sendMessageBtn($chatId,$message,$btn,$btnName,$messageId);
    }
    public function saveProduct($chatId, $productId,$user,$messageId)
    {
        $product = Product::find($productId);
        if ($product) {
           if($user){
                ProductUser::create([
                    'user_id'=>$user->id,
                    'product_id'=>$product->id,
                ]);
                $user->update([
                    'state'=>'await_code'
                ]);
           }
            $message = "Hammasi yaxshi o'tdi endi.Himoya qatlami ostidagi kodni kiriting";
            $this->sendMessage($chatId,$message,$messageId);
        }else{
            $message = 'Bunday maxsulot topilmadi!';
            $user = TgUser::where('telegram_id',$chatId)->first();
            $this->saveRegion($chatId, $user->region_id,false,$messageId);
        }

    }
    public function Code($chatId, $text,$user,$messageId)
    {
        $message = "Himoya qatlami ostidagi kodni kiriting";
        $user->update([
            'state'=>'await_code'
        ]);
        $this->sendMessage($chatId,$message,$messageId);
    }
    public function codeSave($chatId,$text,$messageId,$user){
        $code = Code::where('code',$text)->first();
        if($code){
            if($code->status == 1){
                CodeUser::create([
                    'user_id'=>$user->id,
                    'code_id'=>$code->id,
                    'region_id'=>$user->region_id,
                ]);
                $code->update([
                    'status'=>0
                ]);
                $user->update([
                    'state'=>'finish'
                ]);
                $count = CodeUser::where('user_id',$user->id)->get()->count();
                $btnName = 'inline_keyboard';
                $btn = [
                    [['text'=>'Kanalni korish', 'url'=>'https://t.me/abdurohman_karimjonov']],
                    [['text'=>'Yana kod kiritish!', 'callback_data'=>'code']],
                ];
                $message = 'Malumotlar muvaffaqiyatli saqlandi.Yutuqlar har oyning 30-sanasida aniqlanadi. Tanlovni kuzatib borish uchun ushbu kanalni kuzatib boring. Siz kiritgan kodlar soni: '.$count;
                $this->sendMessageBtn($chatId, $message,$btn,$btnName,$messageId);
            }else if($code->status == 0){
                $message = 'Bu kod oldin foydanalingan. Boshqa kod bolsa kiriting';
                $this->sendMessage($chatId,$message,$messageId);
            }
        }else{
            $message = 'Bunday kod mavjud emas. Boshqa kod bolsa kiriting';
            $this->sendMessage($chatId,$message,$messageId);
        }
    }
    public function sendMessage($chatId,$text,$messageId){
        $user = TgUser::where('telegram_id',$chatId)->first();
        if(!$user){
            TgUser::create([
                'telegram_id'=>$chatId,
                'state'=>'await_name'
            ]);
        }
        $response = Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>$text,
        ]);
        \Log::info('Telegram response: '.json_encode($response));
        $this->storeMessageUser($chatId,$messageId);
    }
    public function sendMessageBtn($chatId, $text,$btn,$btnName,$messageId){
        Telegram::sendMessage([
            'chat_id'=>$chatId,
            'text'=>$text,
            'reply_markup' => json_encode([
                $btnName => $btn,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ]),
        ]);
    }
    public function storeMessageUser($chatId,$messageId){
        UserChat::create([
            'chat_id'=>$chatId,
            'message_id'=>$messageId,
           ]);
    }
}
