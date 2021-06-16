<?php

namespace Nestermaks\LaravelPricelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\HasPricelist;

class Pricelist extends Model
{
    use HasPricelist, HasFactory;
    protected $guarded = [];

    public function pricelist_items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PricelistItem::class)->withPivot('order');
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