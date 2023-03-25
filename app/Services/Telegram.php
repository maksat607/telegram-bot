<?php

namespace App\Services;

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
    public function username(){
        $response =  Http::get( $this->api_url.'getMe');
        return $response['result']['username'];
    }
    public function sendMessage(string $chat_id, string $text)
    {
        $response = Http::post($this->api_url . 'sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
        ]);

        return $response->json();
    }

    public function sendFile(string $chat_id, string $file_path)
    {
        $response = Http::attach(
            'document', fopen($file_path, 'r'), basename($file_path)
        )->post($this->api_url . 'sendDocument', [
            'chat_id' => $chat_id
        ]);
        return $response->json();
    }

    public function sendVoice(string $chat_id, string $file_path)
    {
        $response = Http::attach(
            'voice', fopen($file_path, 'r'), basename($file_path)
        )->post($this->api_url . 'sendVoice', [
            'chat_id' => $chat_id
        ]);
        return $response->json();
    }
}
