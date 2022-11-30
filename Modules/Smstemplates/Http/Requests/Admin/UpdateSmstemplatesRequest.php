<?php
namespace Modules\Smstemplates\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmstemplatesRequest extends FormRequest
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
            
            'title' => 'required|unique:smstemplates,title,'.$this->route('smstemplate'),
            'key' => 'required|unique:smstemplates,key,'.$this->route('smstemplate'),
            'content' => 'required',
        ];
    }
}
