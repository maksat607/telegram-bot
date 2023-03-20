<?php

namespace App\Http\Controllers;

use App\Events\ApplicationChat;
use App\Models\Customer;
use App\Notifications\UserNotifications;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        Storage::disk('local')->append('example.txt', json_encode($request->all()));
        $message = $request->input('message.text');
        $chatId = $request->input('message.chat.id');
        $first_name = $request->input('message.chat.first_name');
        $last_name = $request->input('message.chat.last_name');
        $username = $request->input('message.chat.username');
        Storage::disk('local')->put('fullname.txt', $message.' '.$chatId.' '.$first_name.' '.$last_name.' '.$username);


        $customer = Customer::firstOrCreate([
            'telegram_id'=>$chatId.fake()->text(2),
            'fullname'=>$first_name.' '.$last_name,
            'telegram_id'=>$chatId,
            'username'=>$username,
        ]);


        $data = [
            'user_id'=> 0,
            'curomer_id'=>$customer->id,
            'message'=>$message,
            'self'=>1
        ];
        $customer->notify(new UserNotifications($data));
        $customer->load('notifications');
        event(new ApplicationChat($customer,$data));

//        $client = new Client(['base_uri' => 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/']);
//        $client->post('sendMessage', [
//            'json' => [
//                'chat_id' => '$chatId',
//                'text' => 'You said: ' . '$message',
//            ],
//        ]);

        return 'OK';
    }
    public function setwebhook(){
        Storage::disk('local')->put('example.txt', 'Contents');
//        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
//
//        $response = $telegram->setWebhook(['url' => 'https://example.com/{token}/webhook', 'https://cars.vinz.ru/telegram-bot']);

    }
    public function start(Request $request)
    {
        Storage::disk('local')->put('example.txt', json_encode($request->all()));
//        $message = $update->getMessage();
//        $chat_id = $message->getChat()->getId();
//        $text = 'Welcome to my bot!';
//        $this->sendMessage($chat_id, $text);
    }

    private function sendMessage($chat_id, $text)
    {
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];


        Telegram::sendMessage($data);
    }
}
