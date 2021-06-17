<?php

namespace Nestermaks\LaravelPricelist;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

trait LaravelPricelist
{
    protected function getRelatedClass($name = Pricelist::class): string
    {
        if (self::class === $name) {
            return PricelistItem::class;
        }

        return $name;
    }

    public function related_items(): BelongsToMany
    {
        return $this->belongsToMany($this->getRelatedClass())->withPivot('item_order');
    }

    public static function getActiveItems(): Collection
    {
        return self::where('active', true)->get();
    }

    public function attach_items($related_items = []): void
    {
        $this->related_items()->attach($related_items);
        $this->setOrderAfterAttaching($related_items);
    }

    public function detach_items($related_items = []): void
    {
        $this->related_items()->detach($related_items);
        $this->setOrderAfterDetaching($related_items);
    }

    public function setItemOrder($related_model, $value): void
    {
        $this
            ->related_items()
            ->syncWithoutDetaching([$related_model->id => ['item_order' => $value]])
        ;
    }
}
