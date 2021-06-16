<?php

namespace Nestermaks\LaravelPricelist;

use Illuminate\Support\Collection;

trait LaravelPricelist
{
    public static function getActiveItems(): Collection
    {
        return self::where('active', true)->get();
    }
}
