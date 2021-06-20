<?php

namespace Nestermaks\LaravelPricelist\Models;

use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\Database\Factories\PricelistItemFactory;
use Nestermaks\LaravelPricelist\LaravelPricelist;

class PricelistItem extends Model implements TranslatableContract
{
    use LaravelPricelist, HasFactory, Translatable;

    protected $guarded = [];
    public $translatedAttributes = ['title', 'units'];

    protected function setOrderAfterAttaching($items)
    {
        $items->each(function ($pricelist) {
            $items_in_pricelist = $pricelist->related_items()->count();
            $this->setItemOrder($pricelist, $items_in_pricelist);
        });
    }

    protected function setOrderAfterDetaching($items)
    {
        $items->each(function ($pricelist) {
            $pricelist->rearrangeItems();
        });
    }
//
    protected static function newFactory(): PricelistItemFactory
    {
        return PricelistItemFactory::new();
    }
}
