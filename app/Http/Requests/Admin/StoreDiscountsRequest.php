<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountsRequest extends FormRequest
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
        $rules = [
            'name' => 'required|unique:discounts,name,'.$this->route('discount'),
            'discount' => 'numeric|required',
        ];
        if ( 'percent' === request()->discount_type ) {
            $rules['discount'] = 'numeric|max:100';
        }
        return $rules;
    }
}
