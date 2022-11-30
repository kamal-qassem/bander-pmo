<?php
namespace Modules\Contracts\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractsRemindersRequest extends FormRequest
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
            
            'description' => 'required',
            'date' => 'required|date_format:'.config('app.date_format'),
          
            'reminder_to_id' => 'required',
        ];
    }
}
