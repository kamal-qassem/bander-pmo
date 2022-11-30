<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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
        
        $rules = [
            'first_name' => 'required',
            'language.*' => 'exists:languages,id',
            'phone1' => 'nullable|phone_number',
            'phone2' => 'nullable|phone_number',
            'thumbnail' => 'nullable|mimes:png,jpg,jpeg,gif'
        ];
        if ( \Request::has('email') ) {
            $user_id = \Auth::id();
            $rules['email'] = 'required|email|unique:contacts,email,' . $user_id;
        }      
        return $rules;
    }
}
