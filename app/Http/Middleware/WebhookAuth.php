<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Api;

class WebhookAuth
{
    protected $telegram;
    protected $telegramChatId;

    public function __construct()
    {
        $this->telegram = new Api(config('app.telegram_bot_token'));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('message')) {
            $this->telegramChatId = $request->input('message.from.id');
        } elseif ($request->has('callback_query')) {
            $this->telegramChatId = $request->input('callback_query.from.id');
        } else {
            $this->telegramChatId = null;
        }
        if ($request->route()&&$request->route()->uri()=='api/telegram-bot'){
          Log::info(json_encode($request->all()));
        }

        if ($request->has('telegram_chat_id')){
            $this->telegramChatId = $request->input('telegram_chat_id');
        }


        if (!$this->telegramChatId) {
            return $next($request);
        }


        // Check if the user exists, if not, create a new user
        $user = User::where('id', 3)->first();

        if (!$user) {
            $user = User::create([
                'id' => 3,
                'telegram_id' => 111,
                'name'=> 'Telegram User',
                'email'=> 'Telegram User'.rand(1,1000).'@gmail.com',
                'password'=> Hash::make('password'),
                'fullname' => 'Telegram User',
            ]);


        }


        Auth::login($user); // Log in the user

        if (Auth::check()) {
            Log::info('User is successfully logged in.'.$user->id); // Confirm login worked
        } else {
            Log::warning('User login failed.');
        }
        return $next($request);
    }


}
