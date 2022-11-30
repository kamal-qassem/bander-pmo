<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomesRequest extends FormRequest
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
        
        $rules = [
            
            'income_category_id' => 'required',
            'entry_date' => 'required|date_format:'.config('app.date_format'),
            'amount' => 'required',
            'payer_id' => 'required',
            'pay_method_id' => 'required',
        ];

        if( isPluginActive('account') )
        {
            $rules[ 'account_id' ] = 'required';
        }

        return $rules;
    }
}
