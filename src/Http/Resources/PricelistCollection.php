<?php

namespace Nestermaks\LaravelPricelist\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PricelistCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
