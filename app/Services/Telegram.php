<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class Telegram
{

    private $api_key;
    private $api_url;

    public function __construct()
    {
        $this->api_key = env('TELEGRAM_BOT_TOKEN');
        $this->api_url = "https://api.telegram.org/bot{$this->api_key}/";
    }

    public function sendMessage($chat_id, $text)
    {
        $client = new Client();
        $endpoint = $this->api_url . 'sendMessage';

        $response = $client->post($endpoint, [
            'form_params' => [
                'chat_id' => $chat_id,
                'text' => $text,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
    public function sendFile($chat_id,$file_path,){
        $response = Http::attach(
            'document', fopen($file_path, 'r'), basename($file_path)
        )->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendDocument", [
            'chat_id' => $chat_id
        ]);
        return json_decode($response->getBody(), true);
    }
    public function sendVoice($chat_id,$file_path,){
        $response = Http::attach(
            'voice', fopen($file_path, 'r'), basename($file_path)
        )->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendVoice", [
            'chat_id' => $chat_id
        ]);
        return json_decode($response->getBody(), true);
    }
}
