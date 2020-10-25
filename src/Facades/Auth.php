<?php

namespace Cc\Bmsf\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Auth::guard(BMSF_ENTRY);
    }
}
