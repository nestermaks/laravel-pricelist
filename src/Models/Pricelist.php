<?php

namespace Nestermaks\LaravelPricelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\LaravelPricelist;

class Pricelist extends Model
{
    use LaravelPricelist;
    use HasFactory;
    protected $guarded = [];

    protected function setOrderAfterAttaching($items)
    {
        $items_in_pricelist = $this->related_items()->count() - $items->count() + 1;
        $items->each(function ($item) use (&$items_in_pricelist) {
            $this->setItemOrder($item, $items_in_pricelist);
            $items_in_pricelist += 1;
        });
    }

    protected function setOrderAfterDetaching()
    {
        $this->rearrangeItems();
    }

    public function rearrangeItems()
    {
        $index = 1;
        $this
            ->related_items()
            ->orderBy('pivot_item_order')
            ->each(function ($item) use (&$index) {
                $this->setItemOrder($item, $index);
                $index += 1;
            });
    }
}
