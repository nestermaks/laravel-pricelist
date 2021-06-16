<?php

namespace Nestermaks\LaravelPricelist\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\Tests\Models\TestModel;

class TestModelFactory extends Factory
{
    protected $model = TestModel::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word(),
        ];
    }
}