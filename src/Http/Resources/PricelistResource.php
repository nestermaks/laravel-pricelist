<?php

namespace Nestermaks\LaravelPricelist\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PricelistResource extends JsonResource
{
//    /**
//     * Transform the resource collection into an array.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @return array
//     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
