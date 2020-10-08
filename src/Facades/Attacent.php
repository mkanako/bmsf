<?php

namespace Cc\Labems\Facades;

use Illuminate\Support\Facades\Facade;

class Attacent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Cc\Labems\Attacent::class;
    }
}
