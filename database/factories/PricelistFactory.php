<?php

namespace Nestermaks\LaravelPricelist\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\Models\Pricelist;

class PricelistFactory extends Factory
{
    protected $model = Pricelist::class;

    public function definition()
    {
        return [
            'title' => $this->faker->title(),
            'description' => $this->faker->paragraph(),
            'active' => $this->faker->boolean(),
            'order' => $this->faker->randomNumber(2)
        ];
    }
}
