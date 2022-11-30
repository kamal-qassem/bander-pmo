<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpensesRequest extends FormRequest
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
        
        $rules =  [            
            'account_id' => 'required',
            'expense_category_id' => 'required',
            'entry_date' => 'required|date_format:'.config('app.date_format'),
            'amount' => 'required',
            'payment_method_id' => 'required',
        ];
        $id = $this->route('expense');
        $account_details = \App\Account::find( request()->account_id );
        $record = \App\Expense::find($id);
        if ( $account_details ) {
            $balance = $account_details->initial_balance;
            if ( $record ) {
                $balance = $balance - $record->amount;
            }
            $amount = request()->amount;
            if ( $record ) {
                if ( $record->amount > $amount && $balance < $amount ) {
                    $rules['amount'] = 'required|lte:' . $balance;
                }
            } else {
                if ( $balance < $amount ) {
                    $rules['amount'] = 'required|lte:' . $balance;
                }
            }
        }
        return $rules;
    }
}
