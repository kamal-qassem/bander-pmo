@if ( $invoice->quote_id > 0 && Route::has('admin.quotes.show') )
<div class="row">
	<div class="col-md-12">		
		<?php
		$url = route('admin.quotes.show', $invoice->quote_id);
		if( ! Gate::allows('quote_create') ) {
			$url = 'javascript:void(0);';
		}
		?>
		<h4 class="text-muted mtop15">@lang('custom.invoices.invoice-created-from-quote', ['url' => $url, 'quote_no' => $invoice->quote_id])</h4>               
	</div>

	<div class="clearfix"></div>
	<hr class="hr-10">
</div>
@endif

@if ( $invoice->order_id > 0 )
<div class="row">
	<div class="col-md-12">		
		<?php
		$url = route('admin.orders.show', $invoice->order_id);
		if( ! Gate::allows('order_create') ) {
			$url = 'javascript:void(0);';
		}
		?>
		<h4 class="text-muted mtop15">@lang('custom.invoices.invoice-created-from-order', ['url' => $url, 'order_no' => $invoice->order_id])</h4>               
	</div>

	<div class="clearfix"></div>
	<hr class="hr-10">
</div>
@endif

@if ( $invoice->cycles != $invoice->total_cycles && 'yes' === $invoice->is_recurring && ! empty( $invoice->recurring_value ) )
<div class="row">
	<div class="col-md-12">
		<div class="mbot10">
			<span class="label label-default padding-5">@lang('custom.invoices.cycles-remaining')
				<b> @if( $invoice->cycles == 0 )  
						@lang('custom.invoices.cycles-infinity') 
					@else 
						{{$invoice->cycles - $invoice->total_cycles}}
					@endif
				</b>
			</span>
			<span class="label label-default padding-5 mleft5">			
				<?php
				$last_recurring_date = $invoice->last_recurring_date;
				if ( empty( $last_recurring_date ) ) {
					$last_recurring_date = $invoice->invoice_date;
				}

				$recurring_value = $invoice->recurring_value;
				if ( empty( $recurring_value ) ) {
					$recurring_value = 1;
				}
				$recurring_type = $invoice->recurring_type;
				if ( empty( $recurring_type ) ) {
					$recurring_type = 'month';
				}
				$next_date = date('Y-m-d', strtotime('+' . $recurring_value . ' ' . strtoupper($recurring_type), strtotime($last_recurring_date)));
				?>
				@lang('custom.invoices.next-invoice-date')<b>{{digiDate( $next_date )}}</b>{!! digi_get_help(trans('custom.invoice.next-invoice-date-instruction')) !!}
			</span>         
		</div>		               
	</div>
	<div class="clearfix"></div>
	<hr class="hr-10">
</div>
@endif

@if ( $invoice->is_recurring_from > 0 )
	<div class="row">
		<div class="col-md-12">
			<?php
			$url = route('admin.recurring_invoices.show', $invoice->is_recurring_from);
			if( ! Gate::allows('recurring_invoice_create') ) {
				$url = 'javascript:void(0);';
			}
			?>
			<p class="text-muted mtop15">@lang('custom.invoices.invoice-created-from', ['url' => $url, 'invoice_no' => $invoice->invoicenumberfull])</p>
		</div>
	</div>
@endif