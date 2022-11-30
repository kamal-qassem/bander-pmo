<?php
namespace Modules\RecurringPeriods\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecurringPeriodsRequest extends FormRequest
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
            'title' => 'required|unique:recurring_periods,title,'.$this->route('recurring_period'),
            'value' => 'required',
        ];
    }
}
