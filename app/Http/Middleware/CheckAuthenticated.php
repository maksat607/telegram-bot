<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('token')) {
            // Redirect to login if user is not authenticated
            return redirect()->route('login');
        }
        return $next($request);
    }
}
