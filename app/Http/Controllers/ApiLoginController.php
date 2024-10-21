<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
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
        // Validate the request
        $request->validate([
            'phone' => 'required',
            'password' => 'required|min:6',
        ]);

        Log::info('User is trying to log in: ' ,$request->all());
        $response = Http::withHeaders(
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Project-Security-Key' => '803b8a72-ca68-11ee-a2ca-52540011aef0'
            ]
        )->post('https://cars.vinz.ru/api/auth/login', [
            'phone' => $request->phone,
            'password' => $request->password,
            'server' => 'prod',
        ]);

        $data = $response->json();
        Log::info('Response: ' ,$data);

        if ($response->successful() && isset($data['token'])) {
            Log::info('User logged in: ' . $data['token']);

            Session::put('token', $data['token']);

            $user = $data['user'] ?? null;

            if ($user) {
                // Create a temporary user in the database or session (Example: using database)
                $tempUser = \App\Models\User::firstOrCreate(
                    ['phone' => $user['phone']], // Ensure it's unique by phone or any other unique field
                    [
                        'email' => $user['phone'] ?? null, // if provided
                        'password' => bcrypt(str_random(10)), // Set a random password
                    ]
                );

                // Log the user in
                Auth::login($tempUser);

                // Store the token in the session or cache
                Session::put('token', $data['token']);

                Log::info('Session data after login:', Session::all());

                return redirect()->intended('/'); // Redirect to the intended page
            }


            return redirect()->intended('/'); // Redirect to the intended page
        }

        return redirect()->back()->withErrors([
            'phone' => 'The provided credentials are incorrect.',
        ]);
    }

    // Handle logout
    public function logout(Request $request)
    {
        // Clear the session or logout the user
        Session::flush(); // Clear all session data
        Auth::logout();   // Optionally logout if using local authentication

        return redirect('/login');
    }
}
