<?php

namespace App\Http\Middleware;


use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AuthenticateAccessTokenRemote
{
    public function handle(Request $request, Closure $next)
    {

        $accessToken = $request->header('Authorization');

        if (!$accessToken)
        {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        if (strpos($accessToken, 'Bearer ') !== 0)
        {
            $accessToken = 'Bearer ' . $accessToken;
        }

        $accessToken = trim(str_replace("Bearer ", "", $accessToken));

        if ($cachedUser = Cache::get($accessToken))
        {
            Auth::login($cachedUser);
            return $next($request);
        }

        $client = new Client();
        if (!request()->get('server') == 'prod' || !request()->get('server')=='production' ){

        }

        try
        {

            $response = $client->request('GET', config('kuleshov-auth.url') . '/api/user',
                ['headers' => ['Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json', 'Content' => 'application/json',
                    'Project-Security-Key' => config('kuleshov-auth.security_key')
                ]]);

            if ($response->getStatusCode() === 200)
            {
                $responseData = json_decode($response->getBody() , true);

                $user = User::where('phone', $responseData['phone'])->first();


                if (!$user)
                {
                    return response()->json(['error' => 'User is not found on the server'], 401);
                }

                Cache::put($accessToken, $user, now()->addWeek());

                Auth::login($user);

                return $next($request);
            }
            else
            {
                return response()->json(['error' => 'invalid_access_token'], 401);
            }
        }
        catch(RequestException $e)
        {
            if ($e->hasResponse())
            {
                return response()
                    ->json(['error' => 'invalid_access_token', 'e' => $e->getMessage() ], 401);
            }
            else
            {
                return response()
                    ->json(['error' => 'server_error', 'message' => $e->getMessage() ], 500);
            }
        }
    }
}

