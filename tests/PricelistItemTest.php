<?php

namespace Nestermaks\LaravelPricelist\Tests;


use Nestermaks\LaravelPricelist\Models\PricelistItem;

class PricelistItemTest extends TestCase
{
    /** @test */
    public function hello()
    {
        $this->assertEquals('hello', 'hello');
    }

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
}
