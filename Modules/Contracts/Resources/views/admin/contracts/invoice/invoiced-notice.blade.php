@if ( $invoice->invoice_id > 0 )
<div class="row">
	<div class="col-md-12">		
		<h4 class="text-muted mtop15">@lang('custom.invoices.contract-invoiced', ['url' => route('admin.invoices.show', $invoice->invoice_id), 'contract_no' => $invoice->invoice_id])</h4>               
	</div>

	<div class="clearfix"></div>
	<hr class="hr-10">
</div>
@endif