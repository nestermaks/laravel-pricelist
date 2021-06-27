<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;
use Nestermaks\LaravelPricelist\Models\PricelistItemTranslation;
use Nestermaks\LaravelPricelist\Models\PricelistTranslation;

class PricelistItemControllerTest extends TestCase
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
                'related_items'
            )
            ->count(9)
            ->create();

        $this->pricelist = Pricelist::where('id', 4)->first();
        $this->item = $this->pricelist->related_items()->first();
        $this->pricelists = Pricelist::getActiveItems();
        $this->items = PricelistItem::getActiveItems();

        $this->item->attach_items(Pricelist::whereIn('id', [1, 2, 3, 4, 5])->get());

        foreach ($this->pricelists as $i) {
            $i->rearrangeItems();
        }
    }

    /** @test */
    public function it_gets_ok()
    {
        $this->get(config('pricelist.api') . '/' . config('pricelist.items'))->assertOk();
    }

    /** @test */
    public function it_gets_pricelists()
    {
        $response = $this->json('GET', config('pricelist.api') . '/' . config('pricelist.items'));

        if (config('pricelist.pricelist-items-per-page') <= $this->items->count()) {
            $response->assertJsonCount(config('pricelist.pricelist-items-per-page'), 'data');
        } else {
            $response->assertJsonCount($this->items->count(), 'data');
        }
    }

    /** @test */
    public function it_shows_a_pricelist_item()
    {
        $this->post(
            config('pricelist.api') . '/' . config('pricelist.items'),
            [
                'title' => 'Hello world',
                'units' => 'kg',
                'shortcut' => 'hell',
                'price' => 345,
                'max_price' => 500,
                'price_from' => false,
                'lang' => 'en',
                'active' => 1,
            ]
        );

        $hello_pricelist_item = PricelistItem::whereTranslation('title', 'Hello world')->first();

        $this
            ->get(config('pricelist.api') . '/' . config('pricelist.items') . '/' . $hello_pricelist_item->id)
            ->assertOk()->assertJson([
                'data' => ['title' => 'Hello world'],
            ]);
    }

    /** @test */
    public function it_stores_pricelist_item_to_db()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->post(
            config('pricelist.api') . '/' . config('pricelist.items'),
            [
                'title' => 'Hello world',
                'units' => 'kg',
                'shortcut' => 'hell',
                'price' => 345,
                'max_price' => 500,
                'price_from' => false,
                'lang' => 'en',
                'active' => 1,
            ]
        );

        $response->assertStatus(201);

        $hello_pricelist_item = PricelistItem::whereTranslation('title', 'Hello world')->first();

        $this->assertEquals('Hello world', $hello_pricelist_item->title);
        $this->assertEquals(0, $hello_pricelist_item->price_from);
    }

    /** @test */
    public function it_validates_when_stores_a_pricelist_item()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->post(
            config('pricelist.api') . '/' . config('pricelist.items'),
            [
                'title' => 'Hello world',
                'units' => 'kg',
                'shortcut' => 'hell',
                'price' => 345,
                'max_price' => 500,
                'price_from' => false,
                'lang' => 'en',
                'active' => 11234, //error is here
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_updates_pricelist_item()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->patch(
            config('pricelist.api') . '/' . config('pricelist.items') . '/2',
            [
                'title' => 'Hello world',
                'units' => 'kg',
                'shortcut' => 'hell',
                'price' => 345,
                'max_price' => 500,
                'price_from' => 0,
                'lang' => 'en',
                'active' => 1,
            ]
        );

        $response->assertStatus(200);

        $hello_pricelist_item = PricelistItem::where('id', 2)->first();

        $this->assertEquals('Hello world', $hello_pricelist_item->title);
    }

    /** @test */
    public function it_validates_on_pricelist_updates()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->patch(
            config('pricelist.api') . '/' . config('pricelist.items') . '/2',
            [
                'title' => 'Hello world',
                'units' => 'kg',
                'shortcut' => 'hell',
                'price' => -234, //error here
                'max_price' => 500,
                'price_from' => 0,
                'lang' => 'en',
                'active' => 1,
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_fails_if_max_price_and_price_from_are_both_set()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->patch(
            config('pricelist.api') . '/' . config('pricelist.items') . '/2',
            [
                'title' => 'Hello world',
                'units' => 'kg',
                'shortcut' => 'hell',
                'price' => 564,
                'max_price' => 500, //error here
                'price_from' => 1, //error here
                'lang' => 'en',
                'active' => 1,
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_deletes_pricelist()
    {
        $response = $this->delete(config('pricelist.api') . '/' . config('pricelist.items') . '/2');

        $response->assertStatus(200);

        $this->assertEmpty(PricelistItem::where('id', 2)->first());
    }
}
