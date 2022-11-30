<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeEntriesRequest extends FormRequest
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
            'project_id' => 'required',
            'start_date' => 'required|date_format:'.config('app.date_format').' H:i:s',
            'end_date' => 'required|date_format:'.config('app.date_format').' H:i:s',
        ];

    }
}
