<?php
use App\WebSocket\MyWebSocketHandler;

Broadcast::routes(['middleware' => ['auth:api']]);

Broadcast::channel('my-channel', function ($user) {
return $user->id === 1;
});

Broadcast::channel('my-private-channel', function ($user) {
return $user->id === 1;
}, ['guards' => ['api']]);

Route::webSocket('/upload-audio/2', [MyWebSocketHandler::class,'onMessage']);
