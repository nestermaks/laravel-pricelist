<?php

namespace Nestermaks\LaravelPricelist\Tests\Extra;

use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\HasPricelist;

class TestModel extends Model
{
    use HasPricelist;

    protected $guarded = [];
}
