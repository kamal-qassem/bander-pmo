<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactsRequest extends FormRequest
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

        $controller = getController('controller');
       
        $route = 'contact';
        if ( 'CustomersController' === $controller ) {
            $route = 'customer';
        } elseif ( 'LeadsController' === $controller ) {
            $route = 'lead';
        } elseif ( 'SuppliersController' === $controller ) {
            $route = 'supplier';
        }

        $rules = [
            
            'email' => 'required|email|unique:contacts,email,'.$this->route( 'contact' ),
            'contact_type' => 'required',
            'first_name' => 'required',
            'contact_type.*' => 'exists:roles,id',
            'language.*' => 'exists:languages,id',
            'phone1' => 'nullable|phone_number',
            'phone2' => 'nullable|phone_number',
            'thumbnail' => 'nullable|image',
        ];
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
