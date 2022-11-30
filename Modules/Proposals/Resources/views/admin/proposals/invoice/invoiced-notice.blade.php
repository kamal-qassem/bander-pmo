@if ( $invoice->invoice_id > 0 )
<div class="row">
	<div class="col-md-12">		
		<h4 class="text-muted mtop15">@lang('custom.invoices.proposal-invoiced', ['url' => route('admin.invoices.show', $invoice->invoice_id), 'invoice_no' => $invoice->invoice_id])</h4>               
	</div>

	<div class="clearfix"></div>
	<hr class="hr-10">
</div>
@endif
@if ( $invoice->quote_id > 0 )
<div class="row">
	<div class="col-md-12">		
		<h4 class="text-muted mtop15">@lang('custom.invoices.proposal-quoted', ['url' => route('admin.quotes.show', $invoice->quote_id), 'quote_no' => $invoice->quote_id])</h4>               
	</div>

	<div class="clearfix"></div>
	<hr class="hr-10">
</div>
@endif