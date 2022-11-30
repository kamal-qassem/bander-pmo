<?php
namespace Modules\Contracts\Http\Requests\Admin;

use Modules\Contracts\Entities\ContractType;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractTypesRequest extends FormRequest
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
            'name'  => 'bail|required|unique:contract_types',
            
        ];
    }
}
