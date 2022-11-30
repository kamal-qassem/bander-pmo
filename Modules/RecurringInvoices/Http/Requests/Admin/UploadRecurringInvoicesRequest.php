<?php
namespace Modules\RecurringInvoices\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadRecurringInvoicesRequest extends FormRequest
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
            'attachments' => 'nullable|mimes:png,jpg,jpeg,gif',
        ];
    }
}
