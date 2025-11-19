<?php

namespace Nalrep\Facades;

use Illuminate\Support\Facades\Facade;

class Nalrep extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'narlrep';
    }
}
