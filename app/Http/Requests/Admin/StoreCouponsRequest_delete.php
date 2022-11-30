<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponsRequest extends FormRequest
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
            'valid_from' => 'nullable|date_format:'.config('app.date_format').' H:i:s',
            'valid_to' => 'nullable|date_format:'.config('app.date_format').' H:i:s',
            'discount_amount' => 'numeric',
            'discount_percent' => 'numeric',
            'redeem_time' => 'nullable|date_format:'.config('app.date_format').' H:i:s',
        ];
    }
}
