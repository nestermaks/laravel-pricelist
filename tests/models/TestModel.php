<?php

namespace Nestermaks\LaravelPricelist\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\HasPricelist;
use Nestermaks\LaravelPricelist\Tests\Database\Factories\TestModelFactory;

class TestModel extends Model
{
    use HasPricelist;
    use HasFactory;
    protected $guarded = [];

    protected static function newFactory(): TestModelFactory
    {
        return TestModelFactory::new();
    }
}
