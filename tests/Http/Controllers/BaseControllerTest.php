<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;
use Nestermaks\LaravelPricelist\Models\PricelistItemTranslation;
use Nestermaks\LaravelPricelist\Models\PricelistTranslation;
use Nestermaks\LaravelPricelist\Tests\Extra\TestModel;

class BaseControllerTest extends TestCase
{
    private $pricelist;
    private $pricelists;
    private $item;
    private $items;

    public function setUp(): void
    {
        parent::setUp();
        Pricelist::factory()
            ->has(PricelistTranslation::factory(), 'translations')
            ->has(
                PricelistItem::factory()
                    ->has(PricelistItemTranslation::factory(), 'translations')
                    ->count(5),
                'relatedItems'
            )
            ->count(9)
            ->create();

        $this->pricelist = Pricelist::where('id', 4)->first();
        $this->item = $this->pricelist->relatedItems()->first();
        $this->pricelists = Pricelist::getActiveItems();
        $this->items = PricelistItem::getActiveItems();

        $this->item->attach_items(Pricelist::whereIn('id', [1,2,3,4,5])->get());

        foreach ($this->pricelists as $i) {
            $i->rearrangeItems();
        }
    }

    /** @test */
    public function it_changes_order_of_item_through_api()
    {
        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/change-order',
            [
                'pricelist_id' => 3,
                'pricelist_item_id' => 12,
                'item_order' => 5,
            ]
        );


        $response->assertStatus(200);

        $order = Pricelist::where('id', 3)->first()->getItemOrder(PricelistItem::where('id', 12)->first());

        $this->assertEquals(5, $order);
    }

    /** @test */
    public function it_attaches_pricelists_to_item_through_api()
    {
        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/relation/attach_items',
            [
                'pricelist_id' => [3, 4],
                'pricelist_item_id' => 2,
            ]
        );

        $response->assertStatus(200);

        $this->assertContains(2, Pricelist::where('id', 3)->first()->relatedItems->pluck('id'));
        $this->assertContains(2, Pricelist::where('id', 4)->first()->relatedItems->pluck('id'));

        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/relation/detach_items',
            [
                'pricelist_id' => [3, 4],
                'pricelist_item_id' => 2,
            ]
        );

        $response->assertStatus(200);

        $this->assertNotContains(2, Pricelist::where('id', 3)->first()->relatedItems->pluck('id'));
        $this->assertNotContains(2, Pricelist::where('id', 4)->first()->relatedItems->pluck('id'));
    }

    /** @test */
    public function it_attaches_items_to_pricelist_through_api()
    {
        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/relation/attach_items',
            [
                'pricelist_id' => 3,
                'pricelist_item_id' => [26, 29],
                'action' => 'attach_items',
            ]
        );

        $response->assertStatus(200);

        $this->assertContains(26, Pricelist::where('id', 3)->first()->relatedItems->pluck('id'));
        $this->assertContains(29, Pricelist::where('id', 3)->first()->relatedItems->pluck('id'));
    }

    /** @test */
    public function it_detaches_pricelists_from_the_item_through_api()
    {
        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/relation/attach_items',
            [
                'pricelist_id' => [3, 4],
                'pricelist_item_id' => 2,
            ]
        );

        $response->assertStatus(200);

        $this->assertContains(2, Pricelist::where('id', 3)->first()->relatedItems->pluck('id'));
        $this->assertContains(2, Pricelist::where('id', 4)->first()->relatedItems->pluck('id'));

        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/relation/detach_items',
            [
                'pricelist_id' => [3, 4],
                'pricelist_item_id' => 2,
            ]
        );

        $response->assertStatus(200);

        $this->assertNotContains(2, Pricelist::where('id', 3)->first()->relatedItems->pluck('id'));
        $this->assertNotContains(2, Pricelist::where('id', 4)->first()->relatedItems->pluck('id'));
    }

    /** @test */
    public function it_detaches_items_from_the_pricelist_through_api()
    {
        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/relation/attach_items',
            [
                'pricelist_id' => 3,
                'pricelist_item_id' => [4,5],
            ]
        );

        $response->assertStatus(200);

        $this->assertContains(3, PricelistItem::where('id', 4)->first()->relatedItems->pluck('id'));
        $this->assertContains(3, PricelistItem::where('id', 5)->first()->relatedItems->pluck('id'));

        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/relation/detach_items',
            [
                'pricelist_id' => 3,
                'pricelist_item_id' => [4],
            ]
        );

        $response->assertStatus(200);

        $this->assertNotContains(2, Pricelist::where('id', 3)->first()->relatedItems->pluck('id'));
        $this->assertNotContains(2, Pricelist::where('id', 4)->first()->relatedItems->pluck('id'));
    }

    /** @test */
    public function it_attaches_and_detaches_pricelist_to_model()
    {
        TestModel::create([
            'title' => 'Hello',
        ]);

        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/model/addPricelist',
            [
                'model_name' => 'Nestermaks\LaravelPricelist\Tests\Extra\TestModel',
                'pricelist_id' => 1,
                'model_id' => 1,
            ]
        );

        $response->assertStatus(200);

        $this->assertEquals(1, TestModel::first()->pricelists->where('id', 1)->first()->id);

        $response = $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/model/removePricelist',
            [
                'model_name' => 'Nestermaks\LaravelPricelist\Tests\Extra\TestModel',
                'pricelist_id' => 1,
                'model_id' => 1,
            ]
        );

        $response->assertStatus(200);

        $this->assertNotContains(1, TestModel::first()->pricelists->pluck('id'));
    }

    /** @test */
    public function it_gets_all_pricelists_of_a_model()
    {
        $test_model = TestModel::create([
            'title' => 'Hello',
        ]);

        $pricelist_1 = Pricelist::where('id', 1)->first();
        $pricelist_2 = Pricelist::where('id', 2)->first();

        $test_model->addPricelist($pricelist_1);
        $test_model->addPricelist($pricelist_2);
        $this->get(config('pricelist.api') . '/' . config('pricelist.pricelists') . '/model');


        $this->get(
            config('pricelist.api')
            . '/'
            . config('pricelist.pricelists')
            . '/get'
            . '/Nestermaks\LaravelPricelist\Tests\Extra\TestModel' // $model_name
            . '/1' // $model_id
        )->assertJsonCount(2, 'data');
    }
}
