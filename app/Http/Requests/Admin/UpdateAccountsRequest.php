<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountsRequest extends FormRequest
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
            'name' => 'required|unique:accounts,name,'.$this->route('account'),
            'initial_balance' => 'nullable|regex:/^\d+(\.\d{1,4})?$/',
            'phone' => 'nullable|phone_number',
        ];
    }
}
