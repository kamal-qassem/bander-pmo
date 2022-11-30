<?php
namespace Modules\Templates\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplatesRequest extends FormRequest
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
            'title' => 'required',
            'key' => 'required|unique:templates,key,' . $this->route('template'),
            'type' => 'required',
            'subject' => 'required',
            
            'content' => 'required',
        ];
    }
}
