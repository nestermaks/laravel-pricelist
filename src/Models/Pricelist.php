<?php

namespace Nestermaks\LaravelPricelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Nestermaks\LaravelPricelist\LaravelPricelist;

class Pricelist extends Model
{
    use LaravelPricelist, HasFactory;
    protected $guarded = [];

    public function pricelist_items(): BelongsToMany
    {
        return $this->belongsToMany(PricelistItem::class)->withPivot('item_order');
    }

    public function attach_items($items = []): void
    {
        $this->pricelist_items()->attach($items);
    }

    public function detach_items($items = []): void
    {
        $this->pricelist_items()->detach($items);
    }
}