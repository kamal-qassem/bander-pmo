<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetsRequest extends FormRequest
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
            
            'category_id' => 'required',
            'title' => 'required',
            'photo1' => 'nullable|mimes:png,jpg,jpeg,gif',
            'status_id' => 'required',
            'location_id' => 'required',
        ];

        return $rules;
    }

      public function messages()
    {
        $messages =  [

            'photo1.mimes' => 'Thumbnail must be a file of type png,jpg,jpeg,gif',

         ];

        return $messages;
    }
}
