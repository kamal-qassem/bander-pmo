<?php
namespace Modules\DynamicOptions\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDynamicOptionsRequest extends FormRequest
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
            'title' => [ 
                'required',
                Rule::unique('dynamic_options')
                ->where( function( $query ) {
                    return $query->where('title', request('title'));
                })
                ->where(function ($query) {
                    return $query->where('module', request('module'));
                })
                ->where(function ($query) {
                    return $query->where('type', request('type'));
                })
                ->ignore($this->route('dynamic_option')),
             ],
            'module' => 'required',
            'type' => 'required',
        ];
    }
}
