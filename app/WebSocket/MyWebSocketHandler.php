<?php

namespace App\WebSocket;

use Ratchet\ConnectionInterface;
use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;

class MyWebSocketHandler extends WebSocketHandler
{
    public function onOpen(ConnectionInterface $connection)
    {
        // Handle incoming WebSocket connections
    }

    public function onMessage(ConnectionInterface $connection, $message)
    {
        // Handle incoming WebSocket messages

        // Save the audio data to a file
        file_put_contents(public_path('public').'/file.wav', $message);

        // Broadcast a message to all WebSocket clients
        $this->broadcast()->toOthers()->emit('audio_received');
    }

    public function onClose(ConnectionInterface $connection)
    {
        // Handle closed WebSocket connections
    }
}
