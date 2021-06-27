<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;
use Nestermaks\LaravelPricelist\Models\PricelistItemTranslation;
use Nestermaks\LaravelPricelist\Models\PricelistTranslation;

class PricelistControllerTest extends TestCase
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

        $this->item->attach_items(Pricelist::whereIn('id', [1,2,3,4,5])->get());

        foreach ($this->pricelists as $i) {
            $i->rearrangeItems();
        }
    }

    /** @test */
    public function it_gets_ok()
    {
        $this->get(config('pricelist.api') . '/' . config('pricelist.pricelists'))->assertOk();
    }

    /** @test */
    public function it_gets_pricelists()
    {
        $response = $this->json('GET', config('pricelist.api') . '/' . config('pricelist.pricelists'));

        if (config('pricelist.pricelists-per-page') <= $this->pricelists->count()) {
            $response->assertJsonCount(config('pricelist.pricelists-per-page'), 'data');
        } else {
            $response->assertJsonCount($this->pricelists->count(), 'data');
        }
    }

    /** @test */
    public function it_stores_pricelist_to_db()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists'),
            [
                'title' => 'Hello world',
                'description' => 'Description works',
                'lang' => 'en',
                'active' => 1,
                'order' => 23,
            ]
        );

        $response->assertStatus(201);

        $hello_pricelist = Pricelist::whereTranslation('title', 'Hello world')->first();

        $this->assertEquals('Hello world', $hello_pricelist->title);
    }

    /** @test */
    public function it_validates_when_stores_pricelist()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists'),
            [
                'title' => '',
                'description' => 'Description works',
                'lang' => 'en',
                'active' => 1,
                'order' => 23,
            ]
        );

        $response->assertStatus(422);

    }

    /** @test */
    public function it_shows_a_pricelist()
    {
        $this->post(
            config('pricelist.api') . '/' . config('pricelist.pricelists'),
            [
                'title' => 'Hello world',
                'description' => 'Description works',
                'lang' => 'en',
                'active' => 1,
                'order' => 23,
            ]
        );

        $hello_pricelist = Pricelist::whereTranslation('title', 'Hello world')->first();

        $this
            ->get(config('pricelist.api') . '/' . config('pricelist.pricelists') . '/' . $hello_pricelist->id)
            ->assertOk()->assertJson([
                'data' => ['title' => 'Hello world'],
            ]);
    }

    /** @test */
    public function it_updates_pricelist()
    {
        $response = $this->patch(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/2',
            [
                'title' => 'Hello world',
                'description' => 'Description works',
                'lang' => 'en',
                'active' => 1,
                'order' => 23,
            ]
        );

        $response->assertStatus(200);

        $hello_pricelist = Pricelist::where('id', 2)->first();

        $this->assertEquals('Hello world', $hello_pricelist->title);
    }

    /** @test */
    public function it_validates_pricelist_updates()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->patch(
            config('pricelist.api') . '/' . config('pricelist.pricelists') . '/2',
            [
                'title' => 'Hello world',
                'description' => 'Description works',
                'lang' => 'en',
                'active' => 1123, //error here
                'order' => 23,
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_deletes_pricelist()
    {
        $response = $this->delete(config('pricelist.api') . '/' . config('pricelist.pricelists') . '/2');

        $response->assertStatus(200);

        $this->assertEmpty(Pricelist::where('id', 2)->first());
    }
}
