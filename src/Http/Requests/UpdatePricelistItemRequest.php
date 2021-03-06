<?php

namespace Nestermaks\LaravelPricelist\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePricelistItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return config('pricelist.update-pricelist-items');
    }
}
