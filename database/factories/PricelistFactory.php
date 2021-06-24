<?php

namespace Nestermaks\LaravelPricelist\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\Models\Pricelist;

class PricelistFactory extends Factory
{
    protected $model = Pricelist::class;

    public function definition()
    {
        return [
            'active' => true,
            'order' => $this->faker->numberBetween(1, 50),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
