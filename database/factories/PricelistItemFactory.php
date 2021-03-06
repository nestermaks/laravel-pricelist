<?php

namespace Nestermaks\LaravelPricelist\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

class PricelistItemFactory extends Factory
{
    protected $model = PricelistItem::class;

    public function definition()
    {
        return [
            'shortcut' => $this->faker->word(),
            'price' => $this->faker->numberBetween(10, 900),
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
