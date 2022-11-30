<?php
namespace Modules\Orders\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdersRequest extends FormRequest
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
            'customer_id' => 'required|exists:contacts,id',
            'status' => 'required',
            'grand_total' => 'min:1',
            'product_name.*'  => 'required|exists:products,name',
            'product_ids.*' => 'required|exists:products,id',
            'product_qty.*' => 'required|max:2147483647|regex:/^\d+?$/',
            'product_price.*' => 'required|regex:/^\d+(\.\d{1,2})?$/',            
            'product_tax.*' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'product_discount.*' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
        ];

        // To avoid "The values under comparison must be of the same type", we need to apply "lte" rule products those contain price for the product.
        $product_prices = request('product_price');
        $discount_types = request('discount_type');
        if ( ! empty( $product_prices ) ) {
            foreach ($product_prices as $index => $value) {
                if ( ! empty( $value ) ) {
                    $discount_type = ! empty( $discount_types[ $index ] ) ? $discount_types[ $index ] : '';
                    if ( ! empty( $discount_type ) && 'value' === $discount_type ) {
                        $rules[ 'product_discount.' . $index ] = 'nullable|lte:product_price.'.$index.'|regex:/^\d+(\.\d{1,2})?$/';
                    } else {
                        $rules[ 'product_discount.' . $index ] = 'nullable|lte:100|regex:/^\d+(\.\d{1,2})?$/';
                    }
                } else {
                    $discount_type = ! empty( $discount_types[ $index ] ) ? $discount_types[ $index ] : '';
                    if ( ! empty( $discount_type ) && 'percent' === $discount_type ) {
                        $rules[ 'product_discount.' . $index ] = 'nullable|lte:100|regex:/^\d+(\.\d{1,2})?$/';
                    }
                }
            }
        }

        $products_selection = getSetting( 'products_selection', 'site_settings' );
        if ( in_array( $products_selection, array( 'select', 'select2' ) ) ) {
            $rules['product_name.*'] = 'required|exists:products,id';
        }

        $product_qtys = request('product_qty');
        $products = request('product_name');
        // Let us not allow those who dont have enough quantity.
        if ( ! empty( $product_qtys ) ) {
            foreach ($product_qtys as $index => $quantity) {
                if ( ! empty( $quantity ) ) {                    
                    $product = ! empty( $products[ $index ] ) ? $products[ $index ] : '';
                    if ( ! empty( $product ) ) {
                        if ( in_array( $products_selection, array( 'select', 'select2' ) ) ) {
                            $details = \App\Product::find( $product );
                        } else {
                            $details = \App\Product::where('name', $product)->first();
                        }
                        if ( ! empty( $details ) ) {
                            $rules[ 'product_qty.' . $index ] = 'required|lte:'.$details->stock_quantity.'|regex:/^\d+?$/';
                        }
                    }                    
                }
            }
        }
        
        return $rules;
    }

    public function messages()
    {
        $messages = [];

        $products = request('product_name');
        for( $i = 0; $i < count( $products ); $i++ ) {
            $messages[ 'product_name.'.$i.'.required' ] = trans('custom.messages.product_name-required', ['row' => $i+1]);
            $messages[ 'product_name.'.$i.'.exists' ] = trans('custom.messages.product_name-exists', ['row' => $i+1]);
            $messages[ 'product_ids.'.$i.'.required' ] = trans('custom.messages.product_ids-required', ['row' => $i+1]);
            $messages[ 'product_ids.'.$i.'.exists' ] = trans('custom.messages.product_ids-exists', ['row' => $i+1]);
            $messages[ 'product_qty.'.$i.'.required' ] = trans('custom.messages.product_qty-required', ['row' => $i+1]);
            $messages[ 'product_qty.'.$i.'.lte' ] = trans('custom.messages.product_qty-lte', ['row' => $i+1]);
            $messages[ 'product_price.'.$i.'.required' ] = trans('custom.messages.product_price-required', ['row' => $i+1]);
            $messages[ 'product_discount.'.$i.'.lte' ] = trans('custom.messages.product_discount-lte', ['row' => $i+1]);
        }
        return $messages;
    }
}
