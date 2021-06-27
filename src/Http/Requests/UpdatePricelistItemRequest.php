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
        return [
            'title' => ['max:256'],
            'units' => ['max:256'],
            'shortcut' => ['max:256'],
            'price' => ['numeric', 'min:0'],
            'max_price' => ['prohibited_if:price_from,true'],
            'price_from' => ['boolean'],
            'active' => ['boolean']
        ];
    }
}
