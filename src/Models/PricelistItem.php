<?php

namespace Nestermaks\LaravelPricelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Nestermaks\LaravelPricelist\LaravelPricelist;

class PricelistItem extends Model
{
    use LaravelPricelist, HasFactory;

    protected $guarded = [];

    public function pricelists(): BelongsToMany
    {
        return $this->belongsToMany(Pricelist::class)->withPivot('item_order');
    }

    public function attach_pricelists($pricelists = []): void
    {
        $this->pricelists()->attach($pricelists);

        $pricelists->each(function ($pricelist) {
            $items_in_pricelist = $pricelist->pricelist_items()->count();
            $this->setItemOrder($pricelist, $items_in_pricelist);
        });
    }

    public function detach_pricelists($pricelists = []): void
    {
        $this->pricelists()->detach($pricelists);
    }

    public function setItemOrder(Pricelist $pricelist, $value): void
    {
        $this
            ->pricelists()
            ->syncWithoutDetaching([$pricelist->id => ['item_order' => $value]])
        ;
    }
}