<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Illuminate\Support\Facades\App;
use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

class TranslationTest extends TestCase
{
    /** @test */
    public function it_saves_title_to_pricelist()
    {
        Pricelist::create();

        $pricelist = Pricelist::first();

        $pricelist->translateOrNew('en')->title = 'Hello World';

        $pricelist->save();

        $this->assertEquals('Hello World', Pricelist::first()->translate('en')->title);
    }

    /** @test */
    public function it_saves_title_and_units_to_pricelist_item()
    {
        PricelistItem::create([
            'shortcut' => 'some-title',
            'price' => 8,
            'active' => true,
        ]);

        $pricelist_item = PricelistItem::first();

        $pricelist_item->translateOrNew('en')->title = 'Hello World';
        $pricelist_item->translateOrNew('en')->units = 'kg';

        $pricelist_item->save();

        $this->assertEquals('Hello World', PricelistItem::first()->translate('en')->title);
        $this->assertEquals('kg', PricelistItem::first()->translate('en')->units);
    }
}
