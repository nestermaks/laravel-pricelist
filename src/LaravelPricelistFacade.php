<?php

namespace Nestermaks\LaravelPricelist;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nestermaks\LaravelPricelist\LaravelPricelist
 */
class LaravelPricelistFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-pricelist';
    }
}
