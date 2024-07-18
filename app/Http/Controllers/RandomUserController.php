<?php
namespace App\Http\Controllers;

use App\Models\TgUser;
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

            if (isset($update['callback_query'])) {
                $callbackQuery = $update['callback_query'];
                $chatId = $callbackQuery['message']['chat']['id'];
                $data = $callbackQuery['data'];

                if ($data === 'enter_code') {
                    $this->enterCode($chatId);
                }
            }
        }
    }

    public function startBot($chatId)
    {
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

    public function enterCode($chatId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Kod kiritishga tayyor kodni kiriting!'
        ]);
    }
}
