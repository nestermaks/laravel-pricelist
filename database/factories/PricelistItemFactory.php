<?php

namespace Nestermaks\LaravelPricelist\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

class PricelistItemFactory extends Factory
{
    protected $model = PricelistItem::class;

    public function definition()
    {
        return [
//            'title' => $this->faker->word(),
            'shortcut' => $this->faker->word(),
//            'units' => $this->faker->word(),
            'price' => $this->faker->numberBetween(10, 900),
//            'active' => $this->faker->boolean(),
            'active' => 1,
        ];
    }
}
