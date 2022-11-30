<?php
namespace Modules\Contracts\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractsRequest extends FormRequest
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
            'invoice_date' => 'nullable|date_format:'.config('app.date_format'),
            'invoice_due_date' => 'nullable|date_format:'.config('app.date_format'),

            ];
     
        $rules['invoice_no'] = 'required|numeric|unique:contracts,invoice_no';
        
        return $rules;
    }

    public function messages()
    {
        $messages = [
            'invoice_no.required' => trans('custom.contracts.invoice-no-required'),
            'invoice_no.numeric' => trans('custom.contracts.invoice-no-numeric'),
            'invoice_no.unique' => trans('custom.contracts.invoice-no-unique'),
        ];

    
       return $messages;
     }
}
