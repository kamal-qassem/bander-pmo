<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMileStonesRequest extends FormRequest
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
            'due_date' => 'nullable|date_format:'.config('app.date_format'),
            'project_id' => 'required',
            'milestone_order' => 'max:2147483647|nullable|numeric',
        ];
    }
}
