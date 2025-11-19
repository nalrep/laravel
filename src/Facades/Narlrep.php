<?php

namespace Narlrep\Facades;

use Illuminate\Support\Facades\Facade;

class Narlrep extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'narlrep';
    }
}
