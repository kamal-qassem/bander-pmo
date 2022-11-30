<?php
namespace App\Http\Requests\Admin;

use App\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductCategoriesRequest extends FormRequest
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
            'name' => 'required|unique:product_categories,name,'.$this->route('product_category'),
            'photo' => 'nullable|mimes:png,jpg,jpeg,gif',
        ];
    }
}
