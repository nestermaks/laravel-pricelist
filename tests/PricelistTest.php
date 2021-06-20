<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;
use Nestermaks\LaravelPricelist\Tests\Models\TestModel;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertNotContains;

class PricelistTest extends TestCase
{
    /** @test */
    public function it_gets_all_tables()
    {
        Pricelist::create([
//            'title' => 'Some title',
//            'description' => 'Lorem Ipsum dolor sit amet',
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

        $pricelist->attach_items($items);

        $this->assertEquals(5, $pricelist->related_items()->count());
    }

    /** @test */
    public function it_can_detach_an_item()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();

        $pricelist->attach_items($items);
        $detached_single_item = $pricelist->related_items()->firstOrFail();

        $pricelist->detach_items($detached_single_item);

        $this->assertEquals(4, $pricelist->related_items()->count());

        $detach_all = $pricelist->related_items;
        $pricelist->detach_items($detach_all);

        $this->assertEquals(0, $pricelist->related_items()->count());
    }

    /** @test */
    public function it_rearranges_items_after_detaching()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();

        $pricelist->attach_items($items);

        $pricelist->detach_items($items->whereIn('id', [2, 4]));

        assertNotContains(2, $pricelist->related_items->pluck('id'));
        assertNotContains(4, $pricelist->related_items->pluck('id'));
        assertContains(3, $pricelist->related_items->pluck('id'));
    }

    /** @test */
    public function it_can_be_attached_to_another_model()
    {
//        $test_model = TestModel::factory()->create();

        $test_model = TestModel::create([
            'title' => 'Hello'
        ]);

        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();
        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();
        $pricelist->attach_items($items);

        $test_model->addPricelist($pricelist);

        $this->assertEquals(1, $test_model->pricelists()->count());
    }

    /** @test */
    public function it_can_be_detached_from_another_model()
    {
//        $test_model = TestModel::factory()->create();

        $test_model = TestModel::create([
            'title' => 'Hello'
        ]);

        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();
        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();
        $pricelist->attach_items($items);

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

        $pricelist->attach_items($items);

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

        $pricelist->attach_items($items);

        $pricelist->changeItemOrder($changed_item, 2);
        $this->assertEquals([1, 4, 2, 3, 5], $pricelist->related_items()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());

        $pricelist->changeItemOrder($changed_item, 5);
        $this->assertEquals([1, 2, 3, 5, 4], $pricelist->related_items()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());

        $changed_item->changeItemOrder($pricelist, 1);
        $this->assertEquals([4, 1, 2, 3, 5], $pricelist->related_items()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());

        $changed_item->changeItemOrder($pricelist, 3);
        $this->assertEquals([1, 2, 4, 3, 5], $pricelist->related_items()->orderBy('pivot_item_order', 'asc')->get()->pluck('id')->toArray());
    }
}
