<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsRequest extends FormRequest
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
            'name' => 'required|unique:products,name,'.$this->route('product'),
            'actual_price' => 'required|regex:/^\d+(\.\d{1,4})?$/',
            'sale_price' => 'nullable|regex:/^\d+(\.\d{1,4})?$/',
            'category.*' => 'exists:product_categories,id',
            'tag.*' => 'exists:product_tags,id',
            'ware_house_id' => 'required',
            'stock_quantity' => 'max:2147483647|nullable|numeric',
            'alert_quantity' => 'max:2147483647|nullable|numeric|lte:stock_quantity',
            'thumbnail' => 'nullable|mimes:png,jpg,jpeg,gif',
        ];
    }
}
