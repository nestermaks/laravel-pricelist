<?php


namespace Nestermaks\LaravelPricelist\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\Models\PricelistTranslation;


class PricelistTranslationFactory extends Factory
{
    protected $model = PricelistTranslation::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'locale' => 'en',
            'description' => $this->faker->paragraph(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}