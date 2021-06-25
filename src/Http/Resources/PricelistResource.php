<?php

namespace Nestermaks\LaravelPricelist\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PricelistResource extends JsonResource
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
