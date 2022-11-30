<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientProjectsRequest extends FormRequest
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
            'title' => 'required',
            'client_id' => 'required',
            'priority' => 'required',
            
            'billing_type_id' => 'required',
            'assigned_to.*' => 'exists:contacts,id',
            'start_date' => 'nullable|date_format:'.config('app.date_format'),
            'due_date' => 'nullable|date_format:'.config('app.date_format'),
            'status_id' => 'required',
        ];

        if ( request()->billing_type_id == PROJECT_BILLING_TYPE_FIXED_PRICE ) {
            $rules['budget'] = 'required|numeric|regex:/^\d+(\.\d{1,4})?$/';
        } elseif ( request()->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS ) {
            $rules['project_rate_per_hour'] = 'required|numeric|regex:/^\d+(\.\d{1,4})?$/';
        } elseif ( request()->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS ) {
            $rules['hourly_rate'] = 'required|numeric|regex:/^\d+(\.\d{1,4})?$/';
        }
        return $rules;
    }
}
