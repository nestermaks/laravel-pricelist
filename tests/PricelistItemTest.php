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
            'title' => 'Some title',
            'shortcut' => 'some-title',
            'price' => 8,
            'units' => 'kg',
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

        $item->attach_pricelists($pricelists);

        $this->assertEquals(5, $item->pricelists()->count());
    }

    /** @test */
    public function it_can_detach_a_pricelist()
    {
        Pricelist::factory()->count(5)->create();
        PricelistItem::factory()->create();

        $pricelists = Pricelist::getActiveItems();
        $item = PricelistItem::firstOrFail();

        $item->attach_pricelists($pricelists);
        $detached_single_item = $item->pricelists()->firstOrFail();

        $item->detach_pricelists($detached_single_item);

        $this->assertEquals(4, $item->pricelists()->count());

        $detach_all = $item->pricelists;
        $item->detach_pricelists($detach_all);

        $this->assertEquals(0, $item->pricelists()->count());
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

        $this->assertTrue(! empty($item->pricelists()->where('pricelist_pricelist_item.item_order', 222)->first()));
    }

    /** @test */
    public function it_increments_order_number_after_attaching_to_pricelist()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->create();

        $pricelist = Pricelist::firstOrFail();
        $item = PricelistItem::firstOrFail();

        $item->attach_pricelists($pricelist);

        $this->assertTrue(! empty($item->pricelists()->where('pricelist_pricelist_item.item_order', 1)->first()));
    }
}
