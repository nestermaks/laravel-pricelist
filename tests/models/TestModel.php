<?php

namespace Nestermaks\LaravelPricelist\Tests\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Nestermaks\LaravelPricelist\Tests\Database\Factories\TestModelFactory;
use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\HasPricelist;

class TestModel extends Model
{
    use HasPricelist;

    protected $guarded = [];

//    protected static function newFactory(): TestModelFactory
//    {
//        return TestModelFactory::new();
//    }
}
