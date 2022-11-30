<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsReturnsRequest extends FormRequest
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
        config(['app.date_format' => env('DATE_FORMAT')]);
        
        return [
            
            'customer_id' => 'required',
            'currency_id' => 'required',
            'order_date' => 'nullable|date_format:'.config('app.date_format'),
            'order_due_date' => 'nullable|date_format:'.config('app.date_format'),
            'ware_house_id' => 'required',
        ];
    }
}
