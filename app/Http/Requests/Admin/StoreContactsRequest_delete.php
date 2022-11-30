<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactsRequest extends FormRequest
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
            'group_id' => 'required',
            'email' => 'required|email|unique:contacts,email',
            'contact_type' => 'required',
            'first_name' => 'required',
            'contact_type.*' => 'exists:contact_types,id',
            'language.*' => 'exists:languages,id',
        ];
        if ( in_array( request('create_user'), ['yesactivate', 'yesinactivate'] ) ) {
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users') ->where( function( $query ) {
                    return $query->where('email', request('email'));
                }),
                Rule::unique('contacts') ->where( function( $query ) {
                    return $query->where('email', request('email'));
                }),
            ];
        }
        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => trans('custom.email-required-user'),
            'email.unique' => trans('custom.user-exists'),
        ];
    }
}
