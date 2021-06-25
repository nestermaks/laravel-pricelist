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

        $item->attach_items($pricelists);

        $this->assertEquals(5, $item->related_items->count());
    }

    /** @test */
    public function it_can_detach_a_pricelist()
    {
        Pricelist::factory()->count(5)->create();
        PricelistItem::factory()->create();

        $pricelists = Pricelist::getActiveItems();
        $item = PricelistItem::firstOrFail();

        $item->attach_items($pricelists);
        $detached_single_item = $item->related_items()->firstOrFail();

        $item->detach_items($detached_single_item);

        $this->assertEquals(4, $item->related_items()->count());

        $detach_all = $item->related_items;
        $item->detach_items($detach_all);

        $this->assertEquals(0, $item->related_items()->count());
    }

    /** @test */
    public function it_can_set_item_order()
    {
        Pricelist::factory()->count(5)->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $item = PricelistItem::firstOrFail();

        $pricelist->attach_items($item);

        $item->setItemOrder($pricelist, 222);

        $this->assertTrue(! empty($item->related_items()->where('pricelist_pricelist_item.item_order', 222)->first()));
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

        $item->attach_items($pricelist);

        $this->assertTrue(! empty($item->related_items()->where('pricelist_pricelist_item.item_order', 1)->first()));

        $pricelist->attach_items($all_items);

        $pricelist->detach_items($all_items->whereIn('id', [2, 4]));

        $item->attach_items($all_pricelists);

        $this->assertEquals(4, $pricelist->related_items()->where('pricelist_pricelist_item.item_order', 4)->first()->id);

        PricelistItem::where('id', 1)->firstOrFail()->detach_items($pricelist);

        $this->assertEquals(4, $pricelist->related_items()->where('pricelist_pricelist_item.item_order', 3)->first()->id);
    }
}
