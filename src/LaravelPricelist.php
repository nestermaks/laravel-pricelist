<?php

namespace Nestermaks\LaravelPricelist;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function relatedItems(): BelongsToMany
    {
        return $this->belongsToMany($this->getRelatedClass())->withPivot('item_order');
    }

    public static function getActiveItems(): Collection
    {
        return self::where('active', true)->get();
    }

    public function attachItems(Collection $related_items): void
    {
        $this->relatedItems()->attach($related_items);
        $this->setOrderAfterAttaching($related_items);
    }

    public function detachItems(Collection $related_items): void
    {
        $this->relatedItems()->detach($related_items);
        $this->setOrderAfterDetaching($related_items);
    }

    public function setItemOrder(Model $related_model, int $value): void
    {
        $this
            ->relatedItems()
            ->syncWithoutDetaching(
                [$related_model->id => ['item_order' => $value]]
            );
    }

    public function getItemOrder(Model $related_model): int
    {
        return $this
            ->relatedItems()
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
