<?php

namespace Nestermaks\LaravelPricelist;

use Illuminate\Support\Collection;
use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

trait LaravelPricelist
{

    public static function getActiveItems(): Collection
    {
        return self::where('active', true)->get();
    }
}
