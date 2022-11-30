<?php
$customer_id = old('customer_id');

if ( empty( $currency_id ) ) {
	$currency_id = ! empty( $products_return->currency_id ) ? $products_return->currency_id : getDefaultCurrency('id');
}

$currency_code = getCurrency($currency_id, 'code');
$currency_symbol = getCurrency($currency_id, 'symbol');

$controller = getController('controller');
$action = getController('action');
?>
<div id="products-row">
<div class="panel panel-default">     
<table class="table-responsive order_products">
    <thead>
    <tr class="item_header bg-gradient-directional-pink white text-view">
        <th style="width:40%; padding:10px;" class="text-center">{{ trans('custom.products.item_name') }}</th>
        <th style=" width: 7%; padding:10px;" class="text-center">{{ trans('custom.products.quantity') }}</th>
        <th style=" width: 7%; padding:10px;" class="text-center">{{ trans('custom.products.rate') }}</th>
        <th style=" width: 8%; padding:10px;" class="text-center">{{ trans('custom.products.tax_percent') }}</th>
        <th style=" width: 10%; padding:10px;" class="text-center">{{ trans('custom.products.tax') }}</th>
        <th style=" width: 7%; padding:10px;" class="text-center">{{ trans('custom.products.discount_percent') }}</th>
        <th style=" width: 10%; padding:10px;" class="text-center">{{ trans('custom.products.discount') }}</th>
        <th style=" width: 10%; padding:10px;" class="text-center">{{ trans('custom.products.amount') }}</th>

        @if('ClientProjectsController' == $controller && 'invoiceProjectPreview' == $action )
        <th style="display:none;" class="text-center">{{ trans('custom.products.action') }}</th>
        @else
        <th style=" width: 10%; padding:10px;" class="text-center">{{ trans('custom.products.action') }}</th>
        @endif
    </tr>
    </thead>
    <tbody>
    <?php
    $products = ! empty( $products_return->products ) ? json_decode( $products_return->products ) : array();
    $total_tax = ! empty( $products->total_tax ) ? $products->total_tax : 0;
    $total_discount = ! empty( $products->total_discount ) ? $products->total_discount : 0;
    $products_amount = ! empty( $products->products_amount ) ? $products->products_amount : 0;
    $sub_total = ! empty( $products->sub_total ) ? $products->sub_total : 0;
    $grand_total = ! empty( $products->grand_total ) ? $products->grand_total : 0;

    $discount_format = ! empty( $products->discount_format ) ? $products->discount_format : 'after_tax';
    $tax_format = ! empty( $products->tax_format ) ? $products->tax_format : 'after_tax';
    $cart_tax = ! empty( $products->cart_tax ) ? $products->cart_tax : 0;
    $cart_discount = ! empty( $products->cart_discount ) ? $products->cart_discount : 0;
    $amount_payable = ! empty( $products->amount_payable ) ? $products->amount_payable : 0;
    
    // Let us not take it from JSON format, cause it is not useful for reports later, so lets take it from separate table.
    // C: Aug 26, 2019        
    $record_id = ! empty( $products_return->id ) ? $products_return->id : 0;
    $products_attached = ! empty( $products_return->id ) ? $products_return->attached_products( $record_id ) : [];
    if ( ! empty( $products_return->id ) && ! empty( $products_return->project_id ) ) {
        $products_attached_tasks = $products_return->attached_tasks( $record_id );
        $products_attached_expenses = $products_return->attached_expenses( $record_id );
    }

    if ( ! empty( $products_attached ) && $products_attached->count() > 0
        || ! empty( $products_attached_tasks) && $products_attached_tasks->count() > 0
        || ! empty( $products_attached_expenses) && $products_attached_expenses->count() > 0
    ) {
        $products = [];
        $total_tax = 0;
        $total_discount = 0;
        $total_products_amount = 0;
        $sub_total = 0;
        $grand_total = 0;
    }
    if ( ! empty( $products_attached ) && $products_attached->count() > 0 ) {
        $products = array();
        $products['total_tax'] = $total_tax;
        $products['total_discount'] = $total_discount;
        $products['sub_total'] = $sub_total;
        $products['grand_total'] = $grand_total;
        $products['discount_format'] = $discount_format;
        $products['tax_format'] = $tax_format;
        $products['cart_tax'] = $cart_tax;
        $products['cart_discount'] = $cart_discount;
        $products['amount_payable'] = $amount_payable;

        foreach ($products_attached as $order ) {
            $product_qty = $order->product_qty;
            $product_price = $order->product_price;

            $products['product_name'][] = $order->product_id;
            $products['product_id'][] = $order->product_id;
            $products['product_qty'][] = $product_qty;            
            $products['product_price'][] = $product_price;

            $product_amount = $product_qty * $product_price;
            $products['products_amount'][] = $product_amount;
            $product_tax = $order->product_tax;
            $tax_type = $order->tax_type;
            $products['product_tax'][] = $product_tax; // Rate
            $products['tax_type'][] = $tax_type;
            if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                $tax_value = ( $product_amount * $product_tax) / 100;
            } else {
                $tax_value = $product_tax;
            }
            $products['tax_value'][] = $tax_value; // Calculated Tax
            $total_tax += $tax_value;

            $product_discount = $order->product_discount;
            $discount_type = $order->discount_type;
            $products['product_discount'][] = $product_discount; // Rate
            $products['discount_type'][] = $discount_type;
            if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                $discount_value = ( $product_amount * $product_discount) / 100;
            } else {
                $discount_value = $product_discount;
            }
            $products['discount_value'][] = $discount_value;
            $total_discount += $discount_value;

            $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
            $grand_total += $amount;
            $sub_total +=  $amount + $discount_value;
            $products['product_subtotal'][] = $order->product_subtotal;
            $products['product_amount'][] = $order->product_amount;
            
            
            $products['pid'][] = $order->pid;
            $products['unit'][] = $order->unit;
            $products['hsn'][] = $order->hsn;
            $products['alert'][] = $order->alert;
            $products['stock_quantity'][] = $order->stock_quantity;
            $products['product_ids'][] = $order->product_id;
            $products['product_description'][] = $order->product_description;
        }
        $products['total_tax'] = $total_tax;
        $products['total_discount'] = $total_discount;
        $products['sub_total'] = $sub_total;
        $products['grand_total'] = $grand_total;
        
    }

    if ( ! empty( $products_attached_tasks) && $products_attached_tasks->count() > 0 ) {                  
        foreach ($products_attached_tasks as $order ) {
            $product_qty = $order->product_qty;
            $product_price = $order->product_price;

            $products['product_name'][] = $order->task_id;
            $products['product_id'][] = $order->task_id;
            $products['product_qty'][] = $product_qty;            
            $products['product_price'][] = $product_price;

            $product_amount = $product_qty * $product_price;
            $products['products_amount'][] = $product_amount;

            $product_tax = $order->product_tax;
            $tax_type = $order->tax_type;
            $products['product_tax'][] = $product_tax; // Rate
            $products['tax_type'][] = $tax_type;
            if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                $tax_value = ( $product_amount * $product_tax) / 100;
            } else {
                $tax_value = $product_tax;
            }
            $products['tax_value'][] = $tax_value; // Calculated Tax
            $total_tax += $tax_value;

            $product_discount = $order->product_discount;
            $discount_type = $order->discount_type;
            $products['product_discount'][] = $product_discount; // Rate
            $products['discount_type'][] = $discount_type;
            if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                $discount_value = ( $product_amount * $product_discount) / 100;
            } else {
                $discount_value = $product_discount;
            }
            $products['discount_value'][] = $discount_value;
            $total_discount += $discount_value;

            $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
            $grand_total += $amount;
            $sub_total +=  $amount + $discount_value;
            $products['product_subtotal'][] = $order->product_subtotal;
            $products['product_amount'][] = $order->product_amount;


            $products['pid'][] = $order->pid;
            $products['unit'][] = $order->unit;
            $products['hsn'][] = $order->hsn;
            $products['alert'][] = $order->alert;
            $products['stock_quantity'][] = $order->stock_quantity;
            $products['product_ids'][] = $order->task_id;
            $products['product_description'][] = $order->product_description;
            $products['product_type'][] = 'task';
        }
        $products['total_tax'] = $total_tax;
        $products['total_discount'] = $total_discount;
        $products['sub_total'] = $sub_total;
        $products['grand_total'] = $grand_total;
    }

    if ( ! empty( $products_attached_expenses ) && $products_attached_expenses->count() > 0 ) {                    
        foreach ( $products_attached_expenses as $order ) {
            $product_qty = $order->product_qty;
            $product_price = $order->product_price;

            $products['product_name'][] = $order->expense_id;
            $products['product_id'][] = $order->expense_id;
            $products['product_qty'][] = $product_qty;            
            $products['product_price'][] = $product_price;

            $product_amount = $product_qty * $product_price;
            $products['products_amount'][] = $product_amount;

            $product_tax = $order->product_tax;
            $tax_type = $order->tax_type;
            $products['product_tax'][] = $product_tax; // Rate
            $products['tax_type'][] = $tax_type;
            if ( 'percent' === $tax_type && $product_tax > 0 && $product_amount > 0 ) {
                $tax_value = ( $product_amount * $product_tax) / 100;
            } else {
                $tax_value = $product_tax;
            }
            $products['tax_value'][] = $tax_value; // Calculated Tax
            $total_tax += $tax_value;

            $product_discount = $order->product_discount;
            $discount_type = $order->discount_type;
            $products['product_discount'][] = $product_discount; // Rate
            $products['discount_type'][] = $discount_type;
            if ( 'percent' === $discount_type && $product_discount > 0 && $product_amount > 0 ) {
                $discount_value = ( $product_amount * $product_discount) / 100;
            } else {
                $discount_value = $product_discount;
            }
            $products['discount_value'][] = $discount_value;
            $total_discount += $discount_value;

            $amount = ($product_qty * $product_price) + $tax_value - $discount_value;
            $grand_total += $amount;
            $sub_total +=  $amount + $discount_value;
            $products['product_subtotal'][] = $order->product_subtotal;
            $products['product_amount'][] = $order->product_amount;


            $products['pid'][] = $order->pid;
            $products['unit'][] = $order->unit;
            $products['hsn'][] = $order->hsn;
            $products['alert'][] = $order->alert;
            $products['stock_quantity'][] = $order->stock_quantity;
            $products['product_ids'][] = $order->expense_id;
            $products['product_description'][] = $order->product_description;
            $products['product_type'][] = 'expense';
        }
        $products['total_tax'] = $total_tax;
        $products['total_discount'] = $total_discount;
        $products['sub_total'] = $sub_total;
        $products['grand_total'] = $grand_total;
        
    }
    if ( is_array( $products ) && ! empty( $products['product_name'] ) ) {
        $products = (Object) $products;
    }
    
    if ( ! empty(old('product_name') ) ) {
        $products = (Object) [ 
            'product_name' => old('product_name'),
            
            'total_tax' => old('total_tax'),
            'total_discount' => old('total_discount'),
            'sub_total' => old('sub_total'),
            'grand_total' => old('grand_total'),
            'additional_tax' => old('additional_tax'),
            'products_amount' => old('products_amount'),

            'product_qty' => old('product_qty'),
            'product_price' => old('product_price'),

            'product_tax' => old('product_tax'),
            'tax_type' => old('tax_type'),
            'tax_value' => old('tax_value'),

            'product_discount' => old('product_discount'),
            'discount_type' => old('discount_type'),
            'discount_value' => old('discount_value'),

            'product_subtotal' => old('product_subtotal'),
            'pid' => old('pid'),
            'unit' => old('unit'),
            'hsn' => old('hsn'),
            'alert' => old('alert'),
            'stock_quantity' => old('stock_quantity'),
            'product_ids' => old('product_ids'),
            'product_description' => old('product_description'),
            'product_type' => old('product_type'),
        ];
    }

	$total_tax = 0;
	$total_discount = 0;
	$products_amount = 0;
	$sub_total = 0;
	$grand_total = 0;
    $total_rows = 0;

    $products_selection = getSetting( 'products_selection', 'site_settings' );
    
    if ( ! empty( $products ) ) {

    	$product_names = ! empty( $products->product_name ) ? $products->product_name : [];
        $total_tax = $products->total_tax;
        $total_discount = $products->total_discount;
        
        $sub_total = $products->sub_total;
        $grand_total = $products->grand_total;
        
        $product_qtys = $products->product_qty;
        
        $product_prices = $products->product_price;

        $product_taxs = $products->product_tax;
        $tax_types = $products->tax_type;
        $tax_values = $products->tax_value;

        $product_discounts = $products->product_discount;
        $discount_types = $products->discount_type;
        $discount_values = $products->discount_value;

        $product_subtotals = $products->product_subtotal;
        $pids = $products->pid;
        $units = $products->unit;
        $hsns = $products->hsn;
        $alerts = $products->alert;
        $stock_quantitys = $products->stock_quantity;
        $product_ids = $products->product_ids;
        $product_descriptions = ! empty( $products->product_description ) ? $products->product_description : '';

        for( $i = 0; $i < count( $product_names ); $i++ ) {
            $product_name = ! empty( $product_names[ $i ] ) ? $product_names[ $i ] : '';
            $product_qty = ! empty( $product_qtys[ $i ] ) ? $product_qtys[ $i ] : '0';
            if ( fmod( $product_qty, 1 ) === 0.00 ) { // If the quantity is '9.00' let us convert it to '9'
              $product_qty = (int)$product_qty;  
            }
            $product_price = ! empty( $product_prices[ $i ] ) ? $product_prices[ $i ] : '0';
            $product_amount = $product_qty * $product_price;
			
			$products_amount += $product_amount;

            $product_tax = ! empty( $product_taxs[ $i ] ) ? $product_taxs[ $i ] : '0'; // Rate.
            $tax_type = ! empty( $tax_types[ $i ] ) ? $tax_types[ $i ] : 'percent';
            
            if ( 'percent' === $tax_type ) {
            	$tax_value = ( $product_amount * $product_tax) / 100;
            } else {
            	$tax_value = $product_tax;
            }


            $product_discount = ! empty( $product_discounts[ $i ] ) ? $product_discounts[ $i ] : '0';
            $discount_type = ! empty( $discount_types[ $i ] ) ? $discount_types[ $i ] : 'percent';
            
            if ( 'percent' === $discount_type ) {
                $discount_value = ( $product_amount * $product_discount) / 100;
            } else {
            	$discount_value = $product_discount;
            }

            $amount = $product_amount + $tax_value - $discount_value;
            
            $product_subtotal = ! empty( $product_subtotals[ $i ] ) ? $product_subtotals[ $i ] : '0';
            $pid = ! empty( $pids[ $i ] ) ? $pids[ $i ] : '';
            $unit = ! empty( $units[ $i ] ) ? $units[ $i ] : '';
            $hsn = ! empty( $hsns[ $i ] ) ? $hsns[ $i ] : '';
            $alert = ! empty( $alerts[ $i ] ) ? $alerts[ $i ] : '';
            $stock_quantity = ! empty( $stock_quantitys[ $i ] ) ? $stock_quantitys[ $i ] : '';
            $product_id = ! empty( $product_ids[ $i ] ) ? $product_ids[ $i ] : '';
            $product_description = ! empty( $product_descriptions[ $i ] ) ? $product_descriptions[ $i ] : '';
    ?>
    <tr height="90px" class="product_row" data-rowid="{{$i}}" data-product_id="{{$pid}}" id="product_row_{{$i}}">
        <td valign="top" valign="top">
            
            @if ( in_array( $products_selection, array( 'select', 'select2' ) ) )
            <div class="form-group">
            <select class="form-control product_name_select @if( 'select2' === $products_selection ) select2 @endif" required="required" name="product_name[]" placeholder="{{trans('custom.products.please_select')}}" id="productselectname-{{$i}}" onchange="getProductDetails(this.value, '{{$i}}')" @if('ClientProjectsController' == $controller && 'invoiceProjectPreview' == $action ) onclick="return false;" onmousedown="(function(e){ e.preventDefault(); })(event, this)" onkeypress="(function(e){ e.preventDefault(); })(event, this)" @endif>
                <option value="">{{trans('custom.products.please_select')}}</option>
                @if ( ! empty( $products_return ) && ! empty( $products_return->project_id ) )
                    <optgroup label="@lang('global.products.title')">
                @endif
            <?php
            $query = \App\Product::query();
            $query->select([
                'products.id',
                'products.name',
                'products.product_code',
                'products.actual_price',
                'products.sale_price',
                'products.stock_quantity',
                'products.hsn_sac_code',
                'products.alert_quantity',
                'products.description',
                'products.tax_id',
                'products.discount_id',
                'products.measurement_unit',
				'products.prices',
            ]);
            $query->where( 'product_status', '=', 'Active' );
            
            foreach ($query->get() as $record ) {
                ?>
                <option value="{{$record->id}}" @if( $product_id == $record->id ) selected @endif>{{$record->name . ' ('.$record->stock_quantity.')'}}</option>
                <?php
            }
            ?>
            @if ( ! empty( $products_return ) && ! empty( $products_return->project_id ) )
                </optgroup>
            @endif

            @if ( ! empty( $products_return ) && ! empty( $products_return->project_id ) )
                <?php
                $project_id = $products_return->project_id;
                
                $query = \App\ProjectTask::where( 'billable', 'yes' )->where('project_id', $products_return->project_id);
                $query->select([
                    'project_tasks.id',
                    'project_tasks.name',
                    'project_tasks.description',
                    'project_tasks.startdate',
                    'project_tasks.duedate',
                    'project_tasks.datefinished',
                    'project_tasks.billable',
                    'project_tasks.billed',
                    'project_tasks.hourly_rate',
                ]);
                
                
                if ( $query->count() > 0 ) { ?>
                    <optgroup label="@lang('global.project-tasks.title')">
                    <?php
                    
                    foreach ($query->get() as $record ) {
                        ?>
                        <option value="{{$record->id}}_task" @if( $product_id == $record->id ) selected @endif>{{$record->name}}@if( $record->billed == 'yes' ) {{trans('global.client-projects.billed')}} @endif</option>
                        <?php
                    } ?>
                    </optgroup>
                    <?php
                }
                ?>

                <?php
                // Expenses.
                $query = \App\Expense::where( 'billable', 'yes' )->where('project_id', $products_return->project_id);
                $query->select([
                    'expenses.id',
                    'expenses.name',
                    'expenses.entry_date',
                    'expenses.amount',
                    'expenses.description',
                    'expenses.ref_no',
                    'expenses.project_id',
                    'expenses.billable',
                    'expenses.billed',
                ]);
                
                
                if ( $query->count() > 0 ) { ?>
                    <optgroup label="@lang('global.expense.title')">
                    <?php
                    
                    foreach ($query->get() as $record ) {
                        
                        $prices = ! empty( $record->prices ) ? json_decode( $record->prices, true ) : array();

                        $actual_price = ! empty( $prices['actual'][ $currency_code ] ) ? $prices['actual'][ $currency_code ] : '0';
                        $sale_price = ! empty( $prices['sale'][ $currency_code ] ) ? $prices['sale'][ $currency_code ] : '0';
            
                        $tax = $record->tax;
                        if ( $tax ) {
                            $record->tax_rate = $tax->rate;
                            $record->tax_value = $tax->rate;
                            $record->rate_type = $tax->rate_type;
                            if ( $tax->rate > 0 && 'percent' === $tax->rate_type ) {
                                $record->tax_value = ($sale_price * $tax->rate) / 100;
                            }
                        } else {
                            $record->tax_rate = 0;
                            $record->tax_value = 0;
                            $record->rate_type = 'percent';
                        }

                        $discount = $record->discount;
                        if ( $discount ) {
                            $record->discount_rate = $discount->discount;
                            $record->discount_value = $record->discount;
                            $record->discount_type = $discount->discount_type;
                            if ( $discount->discount > 0 && 'percent' === $discount->discount_type ) {
                                $record->discount_value = ($sale_price * $discount->discount) / 100;
                            }
                        } else {
                            $record->discount_rate = 0;
                            $record->discount_value = 0;
                            $record->discount_type = 'percent';
                        }
                        ?>
                        <option value="{{$record->id}}_expense" @if( $product_id == $record->id ) selected @endif>{{$record->name . '('.digiCurrency( $record->amount, $products_return->currency_id ).')'}}@if( $record->billed == 'yes' ) {{trans('global.client-projects.billed')}} @endif</option>
                        <?php
                    } ?>
                    </optgroup>
                    <?php
                }
                ?>
            @endif
            </select>
        </div>
            @else
            <?php
            if ( ctype_digit( $product_name ) ) {
                $p = \App\Product::find( $product_name );
                if ( $p ) {
                    $product_name = $p->name;
                }
            }
            ?>
            <div class="form-group">
            <input type="text" class="form-control ui-autocomplete-input product_name_select" name="product_name[]" placeholder="@lang('custom.products.item_name')" id="productname-{{$i}}" autocomplete="off" value="{{$product_name}}">
            @endif
            @if ( Gate::allows('product_create') && 'ClientProjectsController' != $controller && 'invoiceProjectPreview' != $action )
                @if( 'button' === $addnew_type )
                <button type="button" class="btn btn-danger modalForm" data-toggle="modal"  data-id="{{$i}}" data-post="data-php" data-action="createproduct" data-redirect="{{route('admin.purchase_orders.create')}}" onclick="modalForm('createproduct', '{{$i}}')">{{ trans('global.app_add_new') }}</button>
                @else        
                &nbsp;<a class="modalForm" data-toggle="modal"  data-action="createproduct" data-id="{{$i}}" onclick="modalForm('createproduct', '{{$i}}')"><i class="fa fa-plus-square"></i></a>
                @endif
            @endif
        </div>
        </td>
        <td valign="top">
        <div class="form-group">
            <input type="number" class="form-control req amnt" name="product_qty[]" id="quantity-{{$i}}" onscroll="rowTotal('{{$i}}')" onkeyup="rowTotal('{{$i}}')" autocomplete="off" value="{{$product_qty}}" onmouseover="quantityfield_previous(this.value)" step="0.01" min="0.01">
            @if ( ! empty( $products_return ) && empty( $products_return->project_id ) )
            <p>
                <i class="fa fa-minus-circle fa-lg quantity-decrease" aria-hidden="true" onclick="quantity_decrease('{{$i}}')"></i>&nbsp;<i class="fa fa-plus-circle fa-lg quantity-increase" aria-hidden="true" onclick="quantity_increase('{{$i}}')"></i>
            </p>
            @endif
        </div>
        </td>
        <td valign="top">
            <div class="form-group">
            <input type="text" class="form-control req prc" name="product_price[]" id="price-{{$i}}" onkeypress="return isDecimalNumber(event, this)" onkeyup="rowTotal('{{$i}}')" autocomplete="off" value="{{$product_price}}" step="0.01">
        <input type="hidden" class="product_amount" name="product_amount[]" id="product_amount-{{$i}}" value="{{$product_amount}}">
    </div>
        </td>
        <td valign="top">
            <div class="form-group">
            <input type="text" class="form-control vat " name="product_tax[]" id="tax_rate-{{$i}}" onkeypress="return isDecimalNumber(event, this)" onkeyup="rowTotal('{{$i}}')" autocomplete="off" value="{{$product_tax}}" step="0.01">
        </div>
            <div class="form-group" style="margin-top: 5px;">
            <select name="tax_type[]" id="tax_type-{{$i}}" onchange="rowTotal('{{$i}}')" style="padding: 6px 12px;">
                <
                <option value="percent" <?php if( 'percent' === $tax_type ) echo ' selected="selected"'; ?>>{{trans('custom.common.percent')}}</option>
                <option value="value" <?php if( 'value' === $tax_type ) echo ' selected="selected"'; ?>>{{trans('custom.common.value')}}</option>
            </select>
            <input type="hidden" name="tax_value[]" id="tax_value-{{$i}}" value="{{$tax_value}}">
        </div>

        </td>
        <td class="text-center" id="tax_value_display-{{$i}}" valign="top">{{digiCurrency($tax_value, $currency_id)}}</td>
        <td valign="top">
            <div class="form-group" style="margin-top: 3px;">
            <input type="text" class="form-control discount" name="product_discount[]" onkeypress="return isDecimalNumber(event, this)" id="discount_rate-{{$i}}" onkeyup="rowTotal('{{$i}}')" autocomplete="off" value="{{$product_discount}}" step="0.01">
            <select name="discount_type[]" id="discount_type-{{$i}}" onchange="rowTotal('{{$i}}')" style="padding: 6px 12px;">
                <option value="percent" <?php if( 'percent' === $discount_type ) echo ' selected="selected"'; ?>>{{trans('custom.common.percent')}}</option>
                <option value="value" <?php if( 'value' === $discount_type ) echo ' selected="selected"'; ?>>{{trans('custom.common.value')}}</option>
            </select>
            <input type="hidden" name="discount_value[]" id="discount_value-{{$i}}" value="{{$discount_value}}">
        </div>

        </td>
        <td class="text-center" id="discount_value_display-{{$i}}" valign="top">{{digiCurrency($discount_value, $currency_id)}}</td>

        <td class="text-center" valign="top"><strong><span class="ttlText" id="result-{{$i}}">{{digiCurrency($amount, $currency_id)}}</span></strong>
        </td>
        <td class="text-center" valign="top">
            @if ( ! in_array( $controller, ['ClientProjectsController'] ) && ! in_array( $action, ['invoiceProjectPreview']) )
            <button type="button" data-rowid="{{$i}}" class="btn btn-danger removeProd" title="Remove" onclick="removeProd('{{$i}}')"> <i class="fa fa-trash-o"></i> </button>
            @endif
        </td>
        <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-{{$i}}" value="{{$product_subtotal}}">
        <input type="hidden" class="pdIn" name="pid[]" id="pid-{{$i}}" value="{{$pid}}">
        <input type="hidden" name="unit[]" id="unit-{{$i}}" value="{{$unit}}">
        <input type="hidden" name="hsn[]" id="hsn-{{$i}}" value="{{$hsn}}">

        <input type="hidden" name="alert[]" id="alert-{{$i}}" value="{{$alert}}">
        <input type="hidden" name="stock_quantity[]" id="stock_quantity-{{$i}}" value="{{$stock_quantity}}">
        <input type="hidden" name="product_ids[]" id="product_ids-{{$i}}" value="{{$product_id}}" class="product_ids">
    </tr>
    <tr id="product_desc_row_{{$i}}">
        <td colspan="8"><textarea id="dpid-{{$i}}" class="form-control" name="product_description[]" placeholder="Enter product description" autocomplete="off">{{$product_description}}</textarea><br></td>
    </tr>
    <script type="text/javascript">
        var product = {};
        product["product_id"] = '{{$product_id}}';
        product["rowid"] = '{{$i}}';
        js_global['cartproducts'].push(product);
    </script>
    <?php 
    $total_rows++;
    }
    } else { ?>

    <?php
    $products_selected = old('product_name', [
        'product_name' => 0,
    ]);
    for( $i = 0; $i < count( $products_selected ); $i++ ) {
    ?>
    <tr height="90px" class="product_row" data-rowid="{{$i}}" data-product_id="{{$i}}" id="product_row_{{$i}}">
        <td valign="top" valign="top">
            @if ( in_array( $products_selection, array( 'select', 'select2' ) ) )
            <div class="form-group">
            <select class="form-control product_name_select @if( 'select2' === $products_selection ) select2 @endif" required="required" name="product_name[]" placeholder="{{trans('custom.products.please_select')}}" id="productselectname-{{$i}}" onchange="getProductDetails(this.value, '{{$i}}')" >
                <option value="">{{trans('custom.products.please_select')}}</option>
            <?php
            $query = \App\Product::query();
            $query->select([
                'products.id',
                'products.name',
                'products.product_code',
                'products.actual_price',
                'products.sale_price',
                'products.stock_quantity',
                'products.hsn_sac_code',
                'products.alert_quantity',
                'products.description',
                'products.tax_id',
                'products.discount_id',
                'products.measurement_unit',
				'products.prices',
            ]);

            $query->where( 'product_status', '=', 'Active' );
            
            foreach ($query->get() as $record ) {
                
                $prices = ! empty( $record->prices ) ? json_decode( $record->prices, true ) : array();

				$actual_price = ! empty( $prices['actual'][ $currency_code ] ) ? $prices['actual'][ $currency_code ] : '0';
				$sale_price = ! empty( $prices['sale'][ $currency_code ] ) ? $prices['sale'][ $currency_code ] : '0';
				
				$tax = $record->tax;
                if ( $tax ) {
                    $record->tax_rate = $tax->rate;
                    $record->tax_value = $tax->rate;
                    $record->rate_type = $tax->rate_type;
                    if ( $tax->rate > 0 && 'percent' === $tax->rate_type ) {
                        $record->tax_value = ($sale_price * $tax->rate) / 100;
                    }
                } else {
                    $record->tax_rate = 0;
                    $record->tax_value = 0;
                    $record->rate_type = 'percent';
                }

                $discount = $record->discount;
                if ( $discount ) {
                    $record->discount_rate = $discount->discount;
                    $record->discount_value = $record->discount;
                    $record->discount_type = $discount->discount_type;
                    if ( $discount->discount > 0 && 'percent' === $discount->discount_type ) {
                        $record->discount_value = ($record->sale_price * $discount->discount) / 100;
                    }
                } else {
                    $record->discount_rate = 0;
                    $record->discount_value = 0;
                    $record->discount_type = 'percent';
                }
                ?>
                <option value="{{$record->id}}">{{$record->name . ' ('.$record->stock_quantity.')'}}</option>
                <?php
            }
            ?>
            </select>
        </div>
            @else
            <div class="form-group">            
            {!! Form::text('product_name[]', old('product_name[]'), ['class' => 'form-control ui-autocomplete-input product_name_select', 'placeholder' => trans('custom.products.item_name'), 'id' => 'productname-' . $i, 'required' => '']) !!}
            @endif
        </div>
            @if ( Gate::allows('product_create') )
                @if( 'button' === $addnew_type )
                <button type="button" class="btn btn-danger modalForm" data-toggle="modal"  data-id="{{$i}}" data-post="data-php" data-action="createproduct" data-redirect="{{route('admin.purchase_orders.create')}}" onclick="modalForm('createproduct', '{{$i}}')">{{ trans('global.app_add_new') }}</button>
                @else        
                &nbsp;<a class="modalForm" data-toggle="modal"  data-action="createproduct" data-id="{{$i}}" onclick="modalForm('createproduct', '{{$i}}')"><i class="fa fa-plus-square"></i></a>
                @endif
            @endif
        </td>
        <td valign="top">
            <div class="form-group">
            {!! Form::number('product_qty[]', old('product_qty[]'), ['class' => 'form-control req amnt','min'=>'1' ,'placeholder' => trans('custom.products.quantity'), 'id' => 'quantity-' . $i, 'onkeypress' => 'return isNumber(event)', 'onkeyup' => "rowTotal($i)", 'onmouseover' => "quantityfield_previous(this.value)"]) !!}
            <p><i class="fa fa-minus-circle fa-lg quantity-decrease" aria-hidden="true" onclick="quantity_decrease('{{$i}}')"></i>&nbsp;<i class="fa fa-plus-circle fa-lg quantity-increase" aria-hidden="true" onclick="quantity_increase('{{$i}}')"></i></p>
        </div>
        </td>
        <td valign="top">
            <div class="form-group">
            {!! Form::number('product_price[]', old('product_price[]'), ['class' => 'form-control','min'=>'1' ,'placeholder' => trans('custom.products.rate'), 'id' => 'price-' . $i, 'onkeypress' => 'return isDecimalNumber(event, this)', 'onkeyup' => "rowTotal($i)", 'autocomplete' => 'off', 'required' => '', 'step' => '0.01']) !!}
        <input type="hidden" class="product_amount" name="product_amount[]" id="product_amount-{{$i}}" value="0">
    </div>
        </td>
        <td valign="top">
            <div class="form-group">
            {!! Form::number('product_tax[]', old('product_tax[]'), ['class' => 'form-control','min'=>'0' ,'placeholder' => trans('custom.products.tax_percent'), 'id' => 'tax_rate-' . $i, 'onkeypress' => 'return isDecimalNumber(event, this)', 'onkeyup' => "rowTotal($i)", 'autocomplete' => 'off', 'required' => '', 'step' => '0.01']) !!}
            </div>
            <div class="form-group">
            <select name="tax_type[]" id="tax_type-{{$i}}" onchange="rowTotal('{{$i}}')">
                <option value="percent" <?php if( old('tax_type[]') == 'percent' ) echo ' selected' ?>>{{trans('custom.common.percent')}}</option>
                <option value="value" <?php if( old('tax_type[]') == 'value' ) echo ' selected' ?>>{{trans('custom.common.value')}}</option>
            </select>
            <input type="hidden" name="tax_value[]" id="tax_value-{{$i}}" value="{{old('tax_value[]')}}">
        </div>

        </td>
        <td class="text-center" id="tax_value_display-{{$i}}" valign="top">{{digiCurrency($i, $currency_id)}}</td>
        <td valign="top">
            <div class="form-group">
            <input type="text" class="form-control discount" name="product_discount[]" onkeypress="return isDecimalNumber(event, this)" id="discount_rate-{{$i}}" onkeyup="rowTotal('{{$i}}')" autocomplete="off" value="{{old('product_discount[]')}}" placeholder="@lang('custom.products.discount_percent')" step="0.01">
        </div>
        <div class="form-group">
            <select name="discount_type[]" id="discount_type-{{$i}}" onchange="rowTotal('{{$i}}')">
                <option value="percent" <?php if( old('tax_type[]') == 'percent' ) echo ' selected' ?>>{{trans('custom.common.percent')}}</option>
                <option value="value" <?php if( old('tax_type[]') == 'value' ) echo ' selected' ?>>{{trans('custom.common.value')}}</option>
            </select>
            <input type="hidden" name="discount_value[]" id="discount_value-{{$i}}" value="{{old('tax_value[]')}}">
        </div>

        </td>
        <td class="text-center" id="discount_value_display-{{$i}}" valign="top">{{digiCurrency(0, $currency_id)}}</td>

        <td class="text-center" valign="top">
            <div class="form-group">
            <strong><span class="products_amount" id="result-{{$i}}">{{digiCurrency(0, $currency_id)}}</span></strong>
        </div>
        </td>
        <td class="text-center" valign="top">
            <div class="form-group">
            <button type="button" data-rowid="{{$i}}" class="btn btn-danger removeProd" title="Remove" onclick="removeProd('{{$i}}')"> <i class="fa fa-trash-o"></i> </button>
        </div>
        </td>
        
        <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-{{$i}}" value="{{old('product_subtotal[]')}}">
        <input type="hidden" class="pdIn" name="pid[]" id="pid-{{$i}}" value="{{old('pid[]')}}">
        <input type="hidden" name="unit[]" id="unit-{{$i}}" value="{{old('unit[]')}}">
        <input type="hidden" name="hsn[]" id="hsn-{{$i}}" value="{{old('hsn[]')}}">

        <input type="hidden" name="alert[]" id="alert-{{$i}}" value="{{old('alert[]')}}">
        <input type="hidden" name="stock_quantity[]" id="stock_quantity-{{$i}}" value="{{old('stock_quantity[]')}}">
        <input type="hidden" name="product_ids[]" id="product_ids-{{$i}}" value="{{old('product_ids[]')}}" class="product_ids">
    </tr>

    <tr id="product_desc_row_{{$i}}">
        <td colspan="8">
            <textarea id="dpid-{{$i}}" class="form-control" name="product_description[]" placeholder="Enter product description" autocomplete="off">{{old('product_description[]')}}</textarea><br>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

    @if ( ! in_array( $controller, ['ClientProjectsController'] ) && ! in_array( $action, ['invoiceProjectPreview']) )
    <tr class="last-item-row">
        <td class="add-row">
            <button type="button" class="btn btn-success" id="addproduct1" onclick="addproduct()">
                <i class="fa fa-plus-square"></i>&nbsp;&nbsp;@lang('custom.products.add_new')</button>
        </td>
        <td colspan="7"></td>
    </tr>
    @endif

    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right"><strong>@lang('custom.products.total_tax')</strong>
        </td>
        <td align="left" colspan="2" class="text-right">
            <span id="total_tax_display" class="lightMode">{{digiCurrency($total_tax, $currency_id)}}</span>
            <input type="hidden" name="total_tax" class="form-control" id="total_tax" value="{{$total_tax}}">
        </td>
    </tr>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right"><strong>@lang('custom.products.sub_total')</strong>
        </td>
        <td align="left" colspan="2" class="text-right">
            <span id="sub_total_display" class="lightMode">{{digiCurrency($sub_total, $currency_id)}}</span> 
            
            <input type="hidden" name="sub_total" class="form-control" id="sub_total" value="{{$sub_total}}">
			<input type="hidden" name="products_amount" class="form-control" id="products_amount" value="{{$products_amount}}">
			
			
        </td>
    </tr>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right" class="text-right">
            <strong>@lang('custom.products.total_discount') </strong></td>
        <td class ="text-right" colspan="2">
            <span id="total_discount_display" class="lightMode">{{digiCurrency($total_discount, $currency_id)}}</span>
            <input type="hidden" name="total_discount" class="form-control" id="total_discount" value="{{$total_discount}}">
        </td>
    </tr>

    <tr class="sub_c" style="display: table-row;">
        <td colspan="2">&nbsp;</td>
        <td colspan="4" align="right"><strong> @lang('custom.products.grand_total')</strong>
        </td>
        <td class="text-right" colspan="2">
            <span id="grand_total_display" class="lightMode">{{digiCurrency($grand_total, $currency_id)}}</span>
            <input type="hidden" name="grand_total" class="form-control" id="grand_total" value="{{$grand_total}}">
        </td>
    </tr>

    <tr style="display: table-row;"><td colspan="8">&nbsp;</td></tr>
    <?php
    $additionals = false;
    if ( ! empty( $products->cart_tax ) && $products->cart_tax > 0 ) {
        $additionals = true;
    ?>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="2">&nbsp;</td>
        <td colspan="4" align="right"><strong> @lang('custom.products.additional-tax')</strong>
        </td>
        <td class="text-right" colspan="2">
            <span id="additional_tax_display" class="lightMode">{{digiCurrency($products->cart_tax, $currency_id)}}</span>
			<input type="hidden" name="additional_tax" class="form-control" id="additional_tax" value="{{$products->cart_tax}}">
        </td>
    </tr>
    <?php } ?>
    <?php
    if ( ! empty( $products->cart_discount ) && $products->cart_discount > 0 ) {
        $additionals = true;
    ?>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="2">&nbsp;</td>
        <td colspan="4" align="right"><strong> @lang('custom.products.additional-discount')</strong>
        </td>
        <td class="text-right" colspan="2">
            <span id="additional_discount_display" class="lightMode">{{digiCurrency($products->cart_discount, $currency_id)}}</span>
			<input type="hidden" name="additional_discount" class="form-control" id="additional_discount" value="{{$products->cart_discount}}">
        </td>
    </tr>
    <?php } ?>
    <?php
    if ( true === $additionals ) {
    ?>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="2">&nbsp;</td>
        <td colspan="4" align="right"><strong> @lang('custom.products.amount-payable')</strong>
        </td>
        <td class="text-right" colspan="2">
            <span id="amount_payable_display" class="lightMode">{{digiCurrency($products->amount_payable, $currency_id)}}</span>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>
</div>
</div>