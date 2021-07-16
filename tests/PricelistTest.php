<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;
use Nestermaks\LaravelPricelist\Tests\Extra\TestModel;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertNotContains;

class PricelistTest extends TestCase
{
    /** @test */
    public function it_gets_all_tables()
    {
        Pricelist::create([
            'active' => true,
        ]);

        $pricelists = Pricelist::getActiveItems();

        $this->assertEquals(1, $pricelists->count());
    }

    /** @test */
    public function it_can_attach_an_item()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();

        $pricelist->attachItems($items);

        $this->assertEquals(5, $pricelist->relatedItems()->count());
    }

    /** @test */
    public function it_can_detach_an_item()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();

        $pricelist->attachItems($items);
        $detached_single_item = $pricelist->relatedItems()->firstOrFail();

        $pricelist->detachItems($detached_single_item->where('id', 1)->get());

        $this->assertEquals(4, $pricelist->relatedItems()->count());

        $detach_all = $pricelist->relatedItems;
        $pricelist->detachItems($detach_all);

        $this->assertEquals(0, $pricelist->relatedItems()->count());
    }

    /** @test */
    public function it_rearranges_items_after_detaching()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();

        $pricelist->attachItems($items);

        $pricelist->detachItems($items->whereIn('id', [2, 4]));

        assertNotContains(2, $pricelist->relatedItems->pluck('id'));
        assertNotContains(4, $pricelist->relatedItems->pluck('id'));
        assertContains(3, $pricelist->relatedItems->pluck('id'));
    }

    /** @test */
    public function it_can_be_attached_to_another_model()
    {
        $test_model = TestModel::create([
            'title' => 'Hello',
        ]);

        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();
        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();
        $pricelist->attachItems($items);

        $test_model->addPricelist($pricelist);

        $this->assertEquals(1, $test_model->pricelists()->count());
    }

    /** @test */
    public function it_can_be_detached_from_another_model()
    {
        $test_model = TestModel::create([
            'title' => 'Hello',
        ]);

        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();
        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();
        $pricelist->attachItems($items);

        $test_model->addPricelist($pricelist);

        $this->assertEquals(1, $test_model->pricelists()->count());

        $test_model->removePricelist($pricelist);

        $this->assertEquals(0, $test_model->pricelists()->count());
    }

    /** @test */
    public function it_gets_appropriate_item_order_number()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();
        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();
        $changed_item = $items->where('id', 4)->first();

        $pricelist->attachItems($items);

        $pricelist->setItemOrder($changed_item, 5);

        $this->assertEquals(5, $changed_item->getItemOrder($pricelist));
        $this->assertEquals(5, $pricelist->getItemOrder($changed_item));
    }

    /** @test */
    public function it_sets_new_order_after_changing_elements_order()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();
        $pricelist = Pricelist::firstOrFail();
        $changed_item = PricelistItem::where('id', 4)->first();
        $items = PricelistItem::getActiveItems();

        $pricelist->attachItems($items);

        $pricelist->changeItemOrder($changed_item, 2);
        $this->assertEquals([1, 4, 2, 3, 5], $pricelist->relatedItems()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());

        $pricelist->changeItemOrder($changed_item, 5);
        $this->assertEquals([1, 2, 3, 5, 4], $pricelist->relatedItems()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());

        $changed_item->changeItemOrder($pricelist, 1);
        $this->assertEquals([4, 1, 2, 3, 5], $pricelist->relatedItems()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());

        $changed_item->changeItemOrder($pricelist, 3);
        $this->assertEquals([1, 2, 4, 3, 5], $pricelist->relatedItems()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());
    }
}
