<?php

namespace Cc\Labems\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Auth::guard(LABEMS_ENTRY);
    }
}
