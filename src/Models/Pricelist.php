<?php

namespace Nestermaks\LaravelPricelist\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
//use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Nestermaks\LaravelPricelist\Database\Factories\PricelistFactory;
use Nestermaks\LaravelPricelist\LaravelPricelist;

/**
 * Class PricelistItem
 * @package Nestermaks\LaravelPricelist\Models
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $locale
 * @property int $order
 * @property bool $active
 */
class Pricelist extends Model implements TranslatableContract
{
    use LaravelPricelist;
    use HasFactory;
    use Translatable;

    protected $guarded = [];
    public $translatedAttributes = ['title', 'description'];

    protected function setOrderAfterAttaching($items): void
    {
        $items_in_pricelist = $this->related_items()->count() - $items->count() + 1;
        $items->each(function ($item) use (&$items_in_pricelist) {
            $this->setItemOrder($item, $items_in_pricelist);
            $items_in_pricelist += 1;
        });
    }

    protected function setOrderAfterDetaching($items = null): void
    {
        $this->rearrangeItems();
    }

    public function moveItemsDown(int $new_item_order)
    {
        $index = $this->related_items()->count() + 1;
        $this
            ->related_items()
            ->orderBy('pivot_item_order', 'desc')
            ->wherePivot('item_order', '>=', $new_item_order)
            ->each(function ($item) use (&$index) {
                $this->setItemOrder($item, $index);
                $index -= 1;
            });
    }

    public function moveItemsUp(int $new_item_order): void
    {
        $index = 0;
        $this
            ->related_items()
            ->orderBy('pivot_item_order')
            ->wherePivot('item_order', '<=', $new_item_order)
            ->each(function ($item) use (&$index) {
                $this->setItemOrder($item, $index);
                $index += 1;
            });
    }

    public function rearrangeItems(): void
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
