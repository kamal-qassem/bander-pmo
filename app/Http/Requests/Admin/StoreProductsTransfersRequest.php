<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductsTransfersRequest extends FormRequest
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
            'ware_house_id_from' => 'required|exists:warehouses,id',
            'products.*' => 'required|productinwarehouse|exists:products,id',
            'ware_house_id_to' => 'required|exists:warehouses,id|different:ware_house_id_from',
        ];
    }
}
