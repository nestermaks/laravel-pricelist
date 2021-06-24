<?php


namespace Nestermaks\LaravelPricelist\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];
}