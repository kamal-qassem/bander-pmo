@php
$order = ($record->order) ? $record->order : $record;

$currency_id = getDefaultCurrency( 'id' );
$currency_code = getDefaultCurrency( 'code' );
if ( isCustomer() ) {
	$customer_currency_id = getContactInfo( Auth::id(), 'currency_id' );
	if ( ! empty( $customer_currency_id ) ) {
	  $currency_id = $customer_currency_id;
	}

	$customer_currency_details = \App\Currency::find( $currency_id );
	if ( ! empty( $customer_currency_details ) ) {
	  $currency_code = $customer_currency_details->code;
	}
}
@endphp
<div class="row">
<div class="col-md-12 payment-bg">
<div class="col-md-4">
    <div class="well">
        <h4><strong>@lang('custom.payments.order-no')</strong>{{$order->id}}</h4>
        <p><strong>@lang('custom.payments.customer') </strong>{{$order->customer->first_name}}</p>
         <p><strong>@lang('custom.payments.amount')</strong>{{digiCurrency($order->price, $currency_id)}}</p>
        <p><strong>@lang('custom.payments.date')</strong>{{digiDate($order->created_at)}}</p>
        <p><strong>@lang('custom.payments.status')</strong>
        <?php
        $class = 'info';
        $order_status = $order->status;
        if( 'Active' === $order_status ) {
        	$class = 'success';
        }
        if( 'Cancelled' === $order_status ) {
        	$class = 'danger';
        }
        if( 'Pending' === $order_status ) {
        	$class = 'warning';
        }
        ?>
        <span class="label label-{{$class}} label-many">{{ucfirst($order_status)}}</span></p>
    </div>
</div>
<div class="col-md-8 payment-pro">
    <h4>@lang('custom.payments.products')</h4>

    <hr>

    <div class="table-responsive">
        <table class="table invoice-items">
            <thead>
            <tr class="h5 text-dark text-view text-center">
                <th id="cell-item" class="text-semibold text-center">@lang('orders::global.orders.product-only')</th>
                <th id="cell-price" class="text-center text-semibold">@lang('orders::global.orders.price-only')</th>
                <th id="cell-qty" class="text-center text-semibold">@lang('orders::global.orders.quantity-only')</th>
                <th id="cell-total" class="text-center text-semibold">@lang('orders::global.orders.total-only')</th>
            </tr>
            </thead>
			<tbody>
			@php
			$products = $order->products;
			if ( ! empty( $products ) ) {
				$products = json_decode( $products );
			}
		
			@endphp

			@if( ! empty( $products ) )
				@php
					$names = $products->product_name;
					$sub_total = $products->sub_total;
					$discount_total = $products->total_discount;
					$tax_total = $products->total_tax;
					$grand_total = $products->grand_total;
				@endphp
				@for( $index = 0; $index < count( $names ); $index++ )
				@php
					$price = $products->product_price[$index] ?? '0';
					$quantity = $products->product_qty[$index] ?? '0';
					$product_total = $price * $quantity;
					$discount_value = $products->discount_value[$index] ?? '0';
					$tax_value = $products->tax_value[$index] ?? '0';
					$product_subtotal = $products->product_subtotal[$index] ?? '0';
					$details = '';
					$details .= trans('orders::global.orders.price') . digiCurrency( $price  );

			      	if( $quantity > 1 ):
			      		$details .= "\n\n" . trans('orders::global.orders.total') . digiCurrency( $product_total  ) . '('. trans('orders::global.orders.price-only') . 'x' . $quantity . ')';
			      	endif;
			      	
			      	if( $discount_value > 0 ):
			      		$details .= '\n\n'. trans('orders::global.orders.discount') . '-' . digiCurrency( $discount_value  );
			      	endif;

			      	if( $tax_value > 0 ):
			      		$details .= '\n\n' . trans('orders::global.orders.tax') . '+' . digiCurrency( $tax_value  );
			      	endif;
					$details .= '';
				@endphp
				<tr>
					<td class="text-semibold text-center text-dark">
						{{ $products->product_name[$index] ?? '' }}
					</td>
					<td class="text-center amount">{{ digiCurrency( $price )  }}</td>
					<td class="text-center">{{ $quantity }}</td>
					<td class="text-center amount">
						<p data-toggle="tooltip"
			title ="{{$details}}"
			data-placement="bottom">{{digiCurrency( $product_subtotal )}}</p>
					</td>
				</tr>
				@endfor
			@endif

			<tr><td colspan="4">
				<div class="col-md-9 text-right">@lang('orders::global.orders.total-tax')
				</div><div class="col-md-3 payment-text-center">(+){{digiCurrency($tax_total)}}</div>
				<div class="col-md-9 text-right">@lang('orders::global.orders.subtotal')</div><div class="col-md-3 payment-text-center">{{digiCurrency($sub_total)}}</div>
				<div class="col-md-9 text-right">@lang('orders::global.orders.total-discount')</div><div class="col-md-3 payment-text-center">(-){{digiCurrency($discount_total)}}</div>
				<div class="col-md-9 text-right"><h3>@lang('orders::global.orders.total')</h3></div><div class="col-md-3 payment-text-center"><h3>{{digiCurrency($grand_total)}}</h3></div>
			</td></tr>
			
			</tbody>
			</table>

    </div>
    </div>
</div>
</div>