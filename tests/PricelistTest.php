<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;
use Nestermaks\LaravelPricelist\Tests\Models\TestModel;

class PricelistTest extends TestCase
{
    /** @test */
    public function it_gets_all_tables()
    {
        Pricelist::create([
            'title' => 'Some title',
            'description' => 'Lorem Ipsum dolor sit amet',
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

        $this->assertEquals(5, $pricelist->pricelist_items()->count());
    }

    /** @test */
    public function it_can_detach_an_item()
    {
        Pricelist::factory()->create();
        PricelistItem::factory()->count(5)->create();

        $pricelist = Pricelist::firstOrFail();
        $items = PricelistItem::getActiveItems();

        $pricelist->attach_items($items);
        $detached_single_item = $pricelist->pricelist_items()->firstOrFail();

        $pricelist->detach_items($detached_single_item);

        $this->assertEquals(4, $pricelist->pricelist_items()->count());

        $detach_all = $pricelist->pricelist_items;
        $pricelist->detach_items($detach_all);

        $this->assertEquals(0, $pricelist->pricelist_items()->count());
    }

    /** @test */
    public function it_can_be_attached_to_another_model()
    {
        $test_model = TestModel::factory()->create();

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
        $test_model = TestModel::factory()->create();

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
}
