<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectTasksRequest extends FormRequest
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
      //  config(['app.date_format' => env('DATE_FORMAT')]);
         config('app.date_format');
        return [
            'name' => 'required',
            'attachments.*' => 'required|mimes:' . FILE_TYPES_GENERAL,
            'priority' => 'max:2147483647|required|numeric',
            'startdate' => 'required|date_format:'.config('app.date_format'),
            'duedate' => 'nullable|date_format:'.config('app.date_format'),
            'datefinished' => 'nullable|date_format:'.config('app.date_format'),
            'status' => 'max:2147483647|nullable|numeric',
            'recurring_value' => 'max:2147483647|nullable|numeric',
            'cycles' => 'max:2147483647|nullable|numeric',
            'total_cycles' => 'max:2147483647|nullable|numeric',
            'last_recurring_date' => 'nullable|date_format:'.config('app.date_format'),
            'milestone' => 'max:2147483647|nullable|numeric',
        ];
    }
}
