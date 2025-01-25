<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ApiLoginController extends Controller
{
    // Show custom login form
    public function showLoginForm()
    {

        return view('auth.login'); // Point to your custom login view
    }

    // Handle login form submission
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required|min:6',
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Project-Security-Key' => '803b8a72-ca68-11ee-a2ca-52540011aef0'
        ])->post('https://cars.vinz.ru/api/auth/login', [
            'phone' => $request->phone,
            'password' => $request->password,
            'server' => 'prod',
        ]);

        $data = $response->json();

        if ($response->successful() && isset($data['token'])) {
            $user = $data['user'] ?? null;

            if ($user) {
                $tempUser = User::firstOrCreate(
                    ['email' => $user['phone']],
                    [
                        'name' => $user['phone'],
                        'password' => bcrypt(fake()->password),
                    ]
                );
                Auth::login($tempUser);

                // Save the token in the cache
                $token = $data['token'];
                Cache::put($token, $tempUser->id, now()->addHours(122)); // Save the token for 1 hour


                return response()->json([
                    'token' => $data['token'],
                    'user' => $tempUser
                ]);
            }
        }

        Log::error($response->body());
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));

        if ($token) {
            Cache::forget($token);
        }

        return response()->json([
            'message' => 'Logged out successfully',
            'status' => 'success'
        ], 200);
    }
}
