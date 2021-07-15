<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

class PricelistItemTest extends TestCase
{
    /** @test */
    public function it_gets_all_price_items()
    {
        PricelistItem::create([
            'shortcut' => 'some-title',
            'price' => 8,
            'active' => true,
        ]);

        $pricelists = PricelistItem::getActiveItems();

        $this->assertEquals(1, $pricelists->count());
    }

    /** @test */
    public function it_can_attach_a_pricelist()
    {
        Pricelist::factory()->count(5)->create();
        PricelistItem::factory()->create();

        $pricelists = Pricelist::getActiveItems();
        $item = PricelistItem::firstOrFail();

        $item->attachItems($pricelists);

        $this->assertEquals(5, $item->relatedItems->count());
    }

    /** @test */
    public function it_can_detach_a_pricelist()
    {
        Pricelist::factory()->count(5)->create();
        PricelistItem::factory()->create();

        $pricelists = Pricelist::getActiveItems();
        $item = PricelistItem::firstOrFail();

        $item->attachItems($pricelists);
        $detached_single_item = $item->relatedItems->where('id', 1);

        $item->detachItems($detached_single_item);

        $this->assertEquals(4, $item->relatedItems()->count());

        $detach_all = $item->relatedItems;
        $item->detachItems($detach_all);

        $this->assertEquals(0, $item->relatedItems()->count());
    }

    /** @test */
    public function it_can_set_item_order()
    {
        Pricelist::factory()->count(5)->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $item = PricelistItem::firstOrFail();

        $pricelist->attachItems($item->get());

        $item->setItemOrder($pricelist, 222);

        $this->assertTrue(! empty($item->relatedItems()->where('pricelist_pricelist_item.item_order', 222)->first()));
    }

    /** @test */
    public function it_increments_order_number_after_attaching_pricelists_and_rearranges_after_detaching()
    {
        Pricelist::factory()->count(5)->create();
        PricelistItem::factory()->count(5)->create();

        $all_pricelists = Pricelist::getActiveItems();
        $all_items = Pricelist::getActiveItems();

        $pricelist = Pricelist::where('id', 3)->firstOrFail();
        $item = PricelistItem::where('id', 4)->firstOrFail();

        $item->attachItems(Pricelist::where('id', 3)->get());

        $this->assertTrue(! empty($item->relatedItems()->where('pricelist_pricelist_item.item_order', 1)->first()));

        $pricelist->attachItems($all_items);

        $pricelist->detachItems($all_items->whereIn('id', [2, 4]));

        $item->attachItems($all_pricelists);

        $this->assertEquals(4, $pricelist->relatedItems()->where('pricelist_pricelist_item.item_order', 4)->first()->id);

        PricelistItem::where('id', 1)->firstOrFail()->detachItems(Pricelist::where('id', 3)->get());

        $this->assertEquals(4, $pricelist->relatedItems()->where('pricelist_pricelist_item.item_order', 3)->first()->id);
    }
}
