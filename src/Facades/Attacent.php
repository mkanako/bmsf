<?php

namespace Cc\Bmsf\Facades;

use Illuminate\Support\Facades\Facade;

class Attacent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Cc\Bmsf\Attacent::class;
    }
}
