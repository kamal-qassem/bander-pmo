<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotesRequest extends FormRequest
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
            'title' => 'required',
            'quote_date' => 'nullable|date_format:'.config('app.date_format'),
            'quote_expiry_date' => 'nullable|date_format:'.config('app.date_format'),
            'currency_id' => 'required',
        ];
    }
}
