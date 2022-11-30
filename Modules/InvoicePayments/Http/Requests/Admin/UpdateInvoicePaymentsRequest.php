<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoicePaymentsRequest extends FormRequest
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
            
            'invoice_id' => 'required',
            'invoice_id.*' => 'exists:invoices,id',
            'date' => 'required|date_format:'.config('app.date_format'),
            'account_id' => 'required',
            'amount' => 'required',
        ];
    }
}
