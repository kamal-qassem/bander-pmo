<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AccountBalance;

class StoreExpensesRequest extends FormRequest
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
            'account_id' => 'required',
            'expense_category_id' => 'required',
            'entry_date' => 'required|date_format:'.config('app.date_format'),
            'amount' => 'required',
            'payment_method_id' => 'required',
        ];

        $account_details = \App\Account::find( request()->account_id );
        if ( $account_details ) {
            $balance = $account_details->initial_balance;
            $amount = request()->amount;
            if ( $balance < $amount ) {
                $rules['amount'] = 'required|lte:' . $balance;
            }
        }
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
            'amount.lte' => trans('global.expense.no-suffient-funds'),
        ];
    }
}
