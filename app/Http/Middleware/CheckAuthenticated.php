<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization') ?? $this->getTokenFromCookies($request);


        if (!$token || !auth()->check()) {
            return redirect()->route('login.get');
        }


        if (!Cache::has($token) && !auth()->check()) {
            return redirect()->route('login.get');
        }

        return $next($request);
    }
    private function getTokenFromCookies(Request $request)
    {
        $cookieHeader = $request->headers->get('cookie');


        if (!$cookieHeader) {
            return null; // No cookies present
        }

        // Parse cookies and search for 'token'
        $cookies = explode('; ', $cookieHeader); // Split cookies into individual key-value pairs
        foreach ($cookies as $cookie) {
            [$key, $value] = explode('=', $cookie, 2); // Split each cookie into key and value
            if ($key === 'token') {
                return $value; // Return the token value
            }
        }

        return null; // Token not found in cookies
    }
}
