<?php
$customer_id = old('customer_id');

if ( empty( $currency_id ) ) {
    $currency_id = ! empty( $products_return->currency_id ) ? $products_return->currency_id : getDefaultCurrency('id');
}

$currency_code = getCurrency($currency_id, 'code');
$currency_symbol = getCurrency($currency_id, 'symbol');
?>
<table class="table-responsive order_products" width="100%">
    <thead>
    <tr class="item_header bg-gradient-directional-pink white">
        <th width="30%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.item_name') }}</th>
        <th width="8%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.quantity') }}</th>
        <th width="10%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.rate') }}</th>
        <th width="10%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.tax_percent') }}</th>
        <th width="10%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.tax') }}</th>
        <th width="7%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.discount_percent') }}</th>
        <th width="7%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.discount') }}</th>
        <th width="10%" class="text-center" style="padding-top: 10px; padding-bottom: 10px;">{{ trans('custom.products.amount') }}</th>
        
    </tr>
    </thead>
    <tbody>
    <?php
    $products = ! empty( $products_return->products ) ? json_decode( $products_return->products ) : array();

    $total_tax = 0;
    $total_discount = 0;
    $products_amount = 0;
    $sub_total = 0;
    $grand_total = 0;
    $total_rows = 0;

    $products_selection = getSetting( 'products_selection', 'site_settings' );
    
    if ( ! empty( $products ) ) {

        $product_names = $products->product_name;
        $total_tax = $products->total_tax;
        $total_discount = $products->total_discount;
        $products_amount = $products->products_amount;
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
        $product_descriptions = ! empty($products->product_description) ? $products->product_description : array();

        for( $i = 0; $i < count( $product_names ); $i++ ) {
            $product_name = ! empty( $product_names[ $i ] ) ? $product_names[ $i ] : '';
            if ( is_numeric( $product_name )) {
                $product_details = \App\Product::find($product_name);
                if ( $product_details ) {
                    $product_name = $product_details->name;
                }
            }
            $product_qty = ! empty( $product_qtys[ $i ] ) ? $product_qtys[ $i ] : '1';
            $product_price = ! empty( $product_prices[ $i ] ) ? $product_prices[ $i ] : '0';
            $product_amount = $product_qty * $product_price;

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
    <tr height="90px" class="product_row" data-rowid="{{$i}}" data-product_id="{{$pid}}">
        <td valign="top" valign="top" style="border: 1px solid lightgray;">
            {{$product_name}}           
        </td>
        <td valign="top" style="border: 1px solid lightgray;">
            {{$product_qty}}
        </td>
        <td valign="top" style="border: 1px solid lightgray;">
            {{digiCurrency($product_amount,$currency_id)}}
        </td>
        <td valign="top" style="border: 1px solid lightgray;">
            {{$product_tax}} @if( 'percent' === $tax_type ) %  @endif
        </td>
        <td class="text-center" id="tax_value_display-{{$i}}" valign="top" style="border: 1px solid lightgray;">{{digiCurrency($tax_value,$currency_id)}}</td>
        <td valign="top" style="border: 1px solid lightgray;">
            {{$product_discount}} @if( 'percent' === $discount_type ) %  @endif
        </td>
        <td class="text-center" id="discount_value_display-{{$i}}" valign="top" style="border: 1px solid lightgray;">{{digiCurrency($discount_value,$currency_id)}}</td>

        <td class="text-center" valign="top" style="border: 1px solid lightgray;"><strong><span class="ttlText" id="result-{{$i}}">{{digiCurrency($amount,$currency_id)}}</span></strong>
        </td>
       
    </tr>
    <?php 
    $total_rows++;
    }
    }
    ?>

    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right" style="border: 1px solid lightgray;"><strong>@lang('custom.products.total_tax')</strong>
        </td>
        <td align="right" colspan="2" class="text-right" style="border: 1px solid lightgray;">
            <span id="total_tax_display" class="lightMode">{{digiCurrency($total_tax,$currency_id)}}</span>
            <input type="hidden" name="total_tax" class="form-control" id="total_tax" value="{{$total_tax}}">
        </td>
    </tr>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right" style="border: 1px solid lightgray;"><strong>@lang('custom.products.sub_total')</strong>
        </td>
        <td align="right" colspan="2" class="text-right" style="border: 1px solid lightgray;">
            <span id="sub_total_display" class="lightMode">{{digiCurrency($sub_total,$currency_id)}}</span>
            
            <input type="hidden" name="sub_total" class="form-control" id="sub_total" value="{{$sub_total}}">
        </td>
    </tr>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right" class="text-right" style="border: 1px solid lightgray;">
            <strong>@lang('custom.products.total_discount') </strong></td>
        <td class ="text-right" colspan="2" style="border: 1px solid lightgray;" align="right">
            <span id="total_discount_display" class="lightMode">{{digiCurrency($total_discount,$currency_id)}}</span>
            <input type="hidden" name="total_discount" class="form-control" id="total_discount" value="{{$total_discount}}">
        </td>
    </tr>

    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right" class="text-right" style="border: 1px solid lightgray;">
        <strong> @lang('custom.products.grand_total')</strong>
        </td>
        <td class="text-right" colspan="2" style="border: 1px solid lightgray;" align="right">
            <span id="grand_total_display" class="lightMode">{{digiCurrency($grand_total,$currency_id)}}</span>
            <input type="hidden" name="grand_total" class="form-control" id="grand_total" value="{{$grand_total}}">
        </td>
    </tr>

    
    <?php
    $additionals = false;
    if ( ! empty( $products->cart_tax ) && $products->cart_tax > 0 ) {
        $additionals = true;
    ?>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="2" style="border: 1px solid lightgray;">&nbsp;</td>
        <td colspan="4" align="right" style="border: 1px solid lightgray;"><strong> @lang('custom.products.additional-tax')</strong>
        </td>
        <td class="text-right" colspan="2" style="border: 1px solid lightgray;" align="right">
            <span id="grand_total_display" class="lightMode">{{digiCurrency($products->cart_tax,$currency_id)}}</span>
        </td>
    </tr>
    <?php } ?>
    <?php
    if ( ! empty( $products->cart_discount ) && $products->cart_discount > 0 ) {
        $additionals = true;
    ?>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="2" style="border: 1px solid lightgray;">&nbsp;</td>
        <td colspan="4" align="right" style="border: 1px solid lightgray;"><strong> @lang('custom.products.additional-discount')</strong>
        </td>
        <td class="text-right" colspan="2" style="border: 1px solid lightgray;" align="right">
            <span id="grand_total_display" class="lightMode">{{digiCurrency($products->cart_discount,$currency_id)}}</span>
        </td>
    </tr>
    <?php } ?>
    <?php
    if ( true === $additionals && ! empty( $products->amount_payable )) {
    ?>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="2" style="border: 1px solid lightgray;">&nbsp;</td>
        <td colspan="4" align="right" style="border: 1px solid lightgray;"><strong> @lang('custom.products.amount-payable')</strong>
        </td>
        <td class="text-right" colspan="2" style="border: 1px solid lightgray;" align="right">
            <span id="grand_total_display" class="lightMode">{{digiCurrency($products->amount_payable,$currency_id)}}</span>
        </td>
    </tr>
    <?php } ?>

    <?php
    $amount_due = 0;

    $total_paid = \Modules\Orders\Entities\OrdersPayments::where('payment_status', 'success')->where('order_id', $products_return->id)->sum('amount');
    if ( ! empty( $products->amount_payable ) ) {
        $amount_due = $products->amount_payable;
        if ( $total_paid > 0 ) {
            $amount_due = $products->amount_payable - $total_paid;
        }
    }
    ?>
    <tr class="sub_c" style="display: table-row;">
        <td colspan="6" align="right" style="border: 1px solid lightgray;"><strong> @lang('custom.invoices.total-paid')</strong>
        </td>
        <td class="text-right" colspan="2" style="border: 1px solid lightgray;" align="right">
            <span id="grand_total_display" class="lightMode">{{digiCurrency($total_paid,$currency_id)}}</span>
        </td>
    </tr>
    <tr class="sub_c" style="display: table-row;">
        
        <td colspan="6" align="right" style="border: 1px solid lightgray;"><strong> @lang('custom.invoices.amount-due')</strong>
        </td>
        <td class="text-right" colspan="2" style="border: 1px solid lightgray;" align="right">
            <span id="grand_total_display" class="lightMode">{{digiCurrency($amount_due,$currency_id)}}</span>
        </td>
    </tr>
    </tbody>
</table>