<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceProjectsSaveRequest extends FormRequest
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
        config(['app.date_format' => env('DATE_FORMAT')]);
        $rules = [
            'status' => 'required',
            'invoice_date' => 'required|date_format:'.config('app.date_format'),
            'invoice_due_date' => 'nullable|date_format:'.config('app.date_format'),
            'allowed_paymodes.*' => 'exists:payment_gateways,id',

            
            'product_ids.*' => 'required|exists:products,id',
            'product_qty.*' => 'required|gt:0|regex:/^\d+?$/',
            'product_price.*' => 'required|regex:/^\d+(\.\d{1,2})?$/',            
            'product_tax.*' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'product_discount.*' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
        ];

        // To avoid "The values under comparison must be of the same type", we need to apply "lte" rule products those contain price for teh product.
        $product_prices = request('product_price');
        $discount_types = request('discount_type');
        if ( ! empty( $product_prices ) ) {
            foreach ($product_prices as $index => $value) {
                if ( ! empty( $value ) ) {
                    $discount_type = ! empty( $discount_types[ $index ] ) ? $discount_types[ $index ] : '';
                    if ( ! empty( $discount_type ) && 'value' === $discount_type ) {
                        $rules[ 'product_discount.' . $index ] = 'nullable|lte:product_price.*|regex:/^\d+(\.\d{1,2})?$/';
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
        
        // Let us check if this ID is present in 'Products' OR 'Tasks' OR 'Expenses'
        $product_names = request('product_name');


        if ( ! empty( $product_names ) ) {
            if ( in_array( $products_selection, array( 'select', 'select2' ) ) ) {
                foreach ($product_names as $index => $id) {
                    //$product = \App\Product::find( $id );
                    $type = 'product';
                    if ( ! is_numeric( $id ) ) {
                        list($id, $type ) = explode( '_', $id );
                    }
                    if( 'product' === $type ) {
                        $rules['product_name.' . $index] = 'required|exists:products,id';
                        $rules['product_ids.' . $index] = 'required|exists:products,id';
                    } elseif ( 'task' === $type ) {
                        $rules['product_name.' . $index] = 'required|exists:project_tasks,id';
                        $rules['product_ids.' . $index] = 'required|exists:project_tasks,id';
                    } elseif( 'expense' === $type ) {
                        $rules['product_name.' . $index] = 'required|exists:expenses,id';
                        $rules['product_ids.' . $index] = 'required|exists:expenses,id';
                    }
                }
            } else {
                foreach ($product_names as $index => $name) {
                    $product = \App\Product::where( 'name', $name )->first();
                    $projecttask = $expense = '';
                    if ( ! is_numeric( $name ) ) {
                        list($name, $type ) = explode( '_', $name );
                        if ( 'task' == $type ) {
                            $projecttask = \App\ProjectTask::where( 'name', $name )->first();
                        }
                        if ( 'expense' == $type ) {
                            $expense = \App\Expense::where( 'name', $name )->first();
                        }
                    }
                    if( ! empty( $product ) ) {
                        $rules['product_name.' . $index] = 'required|exists:products,name';
                        $rules['product_ids.' . $index] = 'required|exists:products,id';
                    } elseif ( ! empty(  $projecttask ) ) {
                        $rules['product_name.' . $index] = 'required|exists:project_tasks,name';
                        $rules['product_ids.' . $index] = 'required|exists:project_tasks,name';
                    } elseif( ! empty( $expense ) ) {
                        $rules['product_name.' . $index] = 'required|exists:expenses,name';
                        $rules['product_ids.' . $index] = 'required|exists:expenses,name';
                    }
                }
            }
        } else {
            if ( in_array( $products_selection, array( 'select', 'select2' ) ) ) {
                $rules['product_name.*'] = 'required|exists:products,id';
                $rules['product_ids.*'] = 'required|exists:products,id';
            } else {
                $rules['product_name.*'] = 'required|exists:products,name';
                $rules['product_ids.*'] = 'required|exists:products,name';
            }
        }

        $product_qtys = request('product_qty');
        $products = request('product_name');

        // Let us not allow those who dont have enough quantity.
        if ( ! empty( $product_qtys ) ) {
            foreach ($product_qtys as $index => $quantity) {
                $product = ! empty( $products[ $index ] ) ? $products[ $index ] : '';
                $type = 'product';
                if ( ! is_numeric( $product ) ) {
                   list($product, $type ) = explode( '_', $product ); 
                }
                if ( ! empty( $quantity ) ) {
                    if ( 'product' === $type ) {
                        if ( in_array( $products_selection, array( 'select', 'select2' ) ) ) {
                            $details = \App\Product::find( $product );
                        } else {
                            $details = \App\Product::where('name', $product)->first();
                        }
                        if ( ! empty( $details ) ) {
                            $stock_quantity = $details->stock_quantity + $quantity; // Because while editing quantity already treat it as duducted from the stock, so lets add the previous quantity the stock quantity to avoid the low stock notice.
                            $rules[ 'product_qty.' . $index ] = 'required|gt:0|lte:'.$stock_quantity.'|regex:/^\d+?$/';
                        }
                    } elseif( 'task' === $type ) {
                        $rules[ 'product_qty.' . $index ] = 'required|gt:0|regex:/^\d+(\.\d{1,2})?$/';
                    } elseif( 'expense' === $type ) {
                        $rules[ 'product_qty.' . $index ] = 'required|gt:0|regex:/^\d+?$/';
                    }
                } else {
                    if ( 'product' === $type ) {
                        $rules[ 'product_qty.' . $index ] = 'required|gt:0|regex:/^\d+?$/';
                    } elseif( 'task' === $type ) {
                        $rules[ 'product_qty.' . $index ] = 'required|gt:0|regex:/^\d+(\.\d{1,2})?$/';
                    } elseif( 'expense' === $type ) {
                        $rules[ 'product_qty.' . $index ] = 'required|gt:0|regex:/^\d+?$/';
                    }
                }
            }
        }
        $rules['invoice_no'] = 'required|numeric|unique:invoices,invoice_no';
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
            $messages[ 'product_qty.'.$i.'.gt' ] = trans('custom.messages.product_qty-gt', ['row' => $i+1]);
            $messages[ 'product_qty.'.$i.'.regex' ] = trans('custom.messages.product_qty-regex', ['row' => $i+1]);
            $messages[ 'product_price.'.$i.'.required' ] = trans('custom.messages.product_price-required', ['row' => $i+1]);
            $messages[ 'product_price.'.$i.'.regex' ] = trans('custom.messages.product_price-regex', ['row' => $i+1]);
            $messages[ 'product_discount.'.$i.'.lte' ] = trans('custom.messages.product_discount-lte', ['row' => $i+1]);
            $messages[ 'product_discount.'.$i.'.regex' ] = trans('custom.messages.product_discount-regex', ['row' => $i+1]);
        }
        return $messages;
    }
}
