<?php

namespace App\Services;

use GuzzleHttp\Client;

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
}
