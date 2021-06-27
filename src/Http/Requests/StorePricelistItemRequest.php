<?php

namespace Nestermaks\LaravelPricelist\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePricelistItemRequest extends FormRequest
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
        return [
            'title' => ['required', 'max:256'],
            'units' => ['required', 'max:256'],
            'shortcut' => ['required', 'max:256'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_price' => ['prohibited_if:price_from,true'],
            'active' => ['boolean']
        ];
    }
}
