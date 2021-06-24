<?php


namespace Nestermaks\LaravelPricelist\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\Models\PricelistItemTranslation;


class PricelistItemTranslationFactory extends Factory
{
    protected $model = PricelistItemTranslation::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'units' => $this->faker->word(),
            'locale' => 'en',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}