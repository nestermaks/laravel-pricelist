<?php

namespace Nestermaks\LaravelPricelist\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePricelistRequest extends FormRequest
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
            'description' => ['max:1000'],
            'lang' => ['required', 'max:16'],
            'order' => ['numeric', 'min:0', 'max:65535'],
            'active' => ['boolean']
        ];
    }
}
