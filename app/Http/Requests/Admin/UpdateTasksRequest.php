<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTasksRequest extends FormRequest
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
        
        return [            
            'name' => 'required',
            'status_id' => 'required',
            'tag.*' => 'exists:task_tags,id',
            'start_date' => 'nullable|date_format:'.config('app.date_format'),
            'due_date' => 'nullable|date_format:'.config('app.date_format') . '|after_or_equal:start_date',
            'attachment' => 'nullable|mimes:png,jpg,jpeg,gif',
        ];
    }

     public function messages()
        {
            return [
               'attachment.mimes' => 'The thumbnail must be of a file type: png,jpg,jpeg,gif',
            ];
        }
}
