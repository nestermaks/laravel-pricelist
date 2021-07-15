<?php

namespace Nestermaks\LaravelPricelist\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
//use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Nestermaks\LaravelPricelist\Database\Factories\PricelistItemFactory;
use Nestermaks\LaravelPricelist\LaravelPricelist;

/**
 * Class PricelistItem
 * @property int $id
 * @property string $title
 * @property string $units
 * @property string $shortcut
 * @property int $price
 * @property int $max_price
 * @property bool $price_from
 * @property bool $active
 *
 */
class PricelistItem extends Model implements TranslatableContract
{
    use LaravelPricelist;
    use HasFactory;
    use Translatable;

    protected $guarded = [];
    public $translatedAttributes = ['title', 'units'];

    protected function setOrderAfterAttaching(Collection $items): void
    {
        $items->each(function ($pricelist) {
            $items_in_pricelist = $pricelist->relatedItems()->count();
            $this->setItemOrder($pricelist, $items_in_pricelist);
        });
    }

    protected function setOrderAfterDetaching(Collection $items): void
    {
        $items->each(function ($pricelist) {
            $pricelist->rearrangeItems();
        });
    }
}
