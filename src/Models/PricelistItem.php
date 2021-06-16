<?php

namespace Nestermaks\LaravelPricelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\HasPricelist;

class PricelistItem extends Model
{
    use HasPricelist, HasFactory;

    protected $guarded = [];

    public function pricelist()
    {
        $this->belongsToMany(Pricelist::class)->withPivot('order');
    }
}