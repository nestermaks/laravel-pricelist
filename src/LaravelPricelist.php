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
//            ->syncWithoutDetaching([$related_model->id => ['item_order' => $value]])
            ->updateExistingPivot($related_model->id, ['item_order' => $value])
        ;
    }

    public function getItemOrder($related_model)
    {
        return $this
            ->related_items
            ->where('id', $related_model->id)
            ->first()
            ->pivot
            ->item_order;
    }

    public function changeItemOrder($related_model, $new_order)
    {
        $pricelist = $related_model;
        $item = $this;

        if (self::class === Pricelist::class) {
            $pricelist = $this;
            $item = $related_model;
        }

        if ($pricelist->getItemOrder($item) === $new_order) {
            return;
        }

        if ($pricelist->getItemOrder($item) > $new_order) {
            $pricelist->moveItemsDown($new_order);
        }
        else {
            $pricelist->moveItemsUp($new_order);
        }

//        dd($pricelist);

        $pricelist->setItemOrder($item, $new_order);
        $pricelist->rearrangeItems();
    }
}
