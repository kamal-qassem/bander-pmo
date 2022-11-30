<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductsReturnsRequest extends FormRequest
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
            'customer_id' => 'required',
            'address' => 'required',
            'currency_id' => 'required',
            'order_date' => 'required|date_format:'.config('app.date_format'),
            'order_due_date' => 'required|date_format:'.config('app.date_format'),
            'ware_house_id' => 'required',
            'product_ids' => 'required',
        ];
    }
}
