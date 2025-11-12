<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function handleTelegramCallback(Request $request)
    {
        // Получение данных от Telegram
        $id = $request->input('id');
        $username = $request->input('username');
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');

        if (!$id) {
            return redirect('/login')->with('error', 'Ошибка аутентификации');
        }

        // Проверка пользователя через API Kuleshov
        try {
            $response = Http::post('https://tg.kuleshov.studio/api/check-tuser', [
                'id' => $id,
                'companycode' => 'co81cf402b5d6f5'
            ]);

            if ($response->successful()) {
                // Поиск или создание пользователя
                $user = User::firstOrCreate(
                    ['telegram_id' => $id],
                    [
                        'name' => $username ?? "tg_{$id}",
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'password' => Hash::make(Str::random(16))
                    ]
                );

                // Авторизация пользователя
                Auth::login($user);

                return redirect('/');
            }
        } catch (\Exception $e) {
            \Log::error('Ошибка аутентификации Telegram: ' . $e->getMessage());
        }

        return redirect('/login')->with('error', 'Ошибка аутентификации');
    }
}
