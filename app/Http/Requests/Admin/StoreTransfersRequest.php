<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransfersRequest extends FormRequest
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

        $from_amount = \App\Account::find( request()->from_id )->initial_balance;

        $rules = [
            'from_id' => 'required',
            'to_id' => 'required|different:from_id',
            'date' => 'required|date_format:'.config('app.date_format'),
            'amount' => 'numeric|required|min:1|max:' . $from_amount,
            'payment_method_id' => 'required',
        ];
        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'to_id.different' => trans('custom.transfers.account-shouldnot-same'),
        ];
    }
}
