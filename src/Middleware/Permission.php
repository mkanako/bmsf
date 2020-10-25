<?php

namespace Cc\Bmsf\Middleware;

use Cc\Bmsf\Facades\Auth;
use Illuminate\Http\Request;

class Permission
{
    public function handle(Request $request, \Closure $next)
    {
        $path = trim(str_replace(
            $request->route()->getPrefix(),
            '',
            strstr($request->route()->uri(), '{', true) ?: $request->route()->uri()
        ), '/');
        if (Auth::user()->isSuper() || Auth::user()->findPath($path)) {
            return $next($request);
        }
        return err('no permission');
    }
}
