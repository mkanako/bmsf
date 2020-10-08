<?php

namespace Cc\Labems\Middleware;

use Cc\Labems\Facades\Auth;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, \Closure $next)
    {
        try {
            $exp = Auth::parseToken()->checkOrFail()->get('exp');
            Auth::userOrFail();
            $ttl = config('jwt.ttl', 0) * 60;
            if ($ttl > 0 && $exp - time() < $ttl * 0.1) {
                $response = $next($request);
                if ($response->hasMacro('setJWTHeader')) {
                    $response->setJWTHeader(Auth::newToken());
                }
                return $response;
            }
        } catch (\Exception $e) {
            return err('unauthorized:' . $e->getMessage(), -1);
        }
        return $next($request);
    }
}
