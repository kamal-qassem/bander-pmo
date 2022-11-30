<?php
namespace Modules\ModulesManagement\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModulesManagementsRequest extends FormRequest
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
            
            'name' => 'required|unique:modules_managements,name,'.$this->route('modules_management'),
            'slug' => 'required|unique:modules_managements,slug,'.$this->route('modules_management'),
            'type' => 'required',
            'enabled' => 'required',
        ];
    }
}
