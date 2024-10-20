<?php
use App\WebSocket\MyWebSocketHandler;

Broadcast::routes(['middleware' => [\App\Http\Middleware\CheckAuthenticated::class]]);

Broadcast::channel('my-channel', function ($user) {
return true;
});

Broadcast::channel('my-private-channel', function ($user) {
return $user->id === 1;
}, ['guards' => ['api']]);

Route::webSocket('/upload-audio/2', [MyWebSocketHandler::class,'onMessage']);
