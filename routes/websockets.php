<?php
use App\WebSocket\MyWebSocketHandler;

Broadcast::routes();

Broadcast::channel('my-channel', function ($user) {
return true;
});

Broadcast::channel('my-private-channel', function ($user) {
return true;
});

Route::webSocket('/upload-audio/2', [MyWebSocketHandler::class,'onMessage']);
