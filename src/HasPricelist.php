<?php


namespace Nestermaks\LaravelPricelist;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Nestermaks\LaravelPricelist\Models\Pricelist;

trait HasPricelist
{
    public function pricelists(): MorphToMany
    {
        return $this->morphToMany(Pricelist::class, 'pricelistable');
    }

    public function addPricelist(Pricelist $pricelist): void
    {
        $this->pricelists()->attach($pricelist);
    }

    public function removePricelist(Pricelist $pricelist): void
    {
        $this->pricelists()->detach($pricelist);
    }
}
