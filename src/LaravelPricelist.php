<?php

namespace Nestermaks\LaravelPricelist;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
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

    public function attach_items(Collection $related_items): void
    {
        $this->related_items()->attach($related_items);
        $this->setOrderAfterAttaching($related_items);
    }

    public function detach_items(Collection $related_items): void
    {
        $this->related_items()->detach($related_items);
        $this->setOrderAfterDetaching($related_items);
    }

    public function setItemOrder(Model $related_model, int $value): void
    {
        $this
            ->related_items()
            ->syncWithoutDetaching(
                [$related_model->id => ['item_order' => $value]]
            );
    }

    public function getItemOrder(Model $related_model): int
    {
        return $this
            ->related_items()
            ->wherePivot(substr($related_model->getTable(), 0, -1) . '_id', $related_model->id)
            ->first()
            ->pivot->item_order;
    }

    public function changeItemOrder(Model $related_model, int $new_order): void
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
        } else {
            $pricelist->moveItemsUp($new_order);
        }

        $pricelist->setItemOrder($item, $new_order);
        $pricelist->rearrangeItems();
    }
}
