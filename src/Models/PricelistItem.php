<?php

namespace Nestermaks\LaravelPricelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\LaravelPricelist;

class PricelistItem extends Model
{
    use LaravelPricelist;
    use HasFactory;

    protected $guarded = [];

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

//    public function detach_pricelists($pricelists = []): void
//    {
//        $this->related_items()->detach($pricelists);
//    }
}
