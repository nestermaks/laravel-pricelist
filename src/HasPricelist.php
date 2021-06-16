<?php


namespace Nestermaks\LaravelPricelist;


use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasPricelist
{
    public function addPricelist(LaravelPricelist $pricelist)
    {
        $this->pricelists()->attach($pricelist);
    }

    public function pricelists(): MorphToMany
    {
        return $this->morphToMany(LaravelPricelist::class, 'pricelistable');
    }

    public static function getActiveItems(): Collection
    {
        return self::where('active', true)->orderBy('order')->get();
    }
}