<?php


namespace Nestermaks\LaravelPricelist\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistItemTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'units'];
}