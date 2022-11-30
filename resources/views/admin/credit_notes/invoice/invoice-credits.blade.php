<?php
$currency_id = $invoice->currency_id;
$transactions = $invoice->credit_history();
?>
<div id="invoice-template" class="row">
    <div class="col-lg-12">
<h3>@lang('custom.invoices.related-credits')</h3>
    <table class="table table-bordered sys_table">
        <tbody>
            <tr class="text-view">
                <th>@lang('custom.invoices.date')</th>                
                <th>@lang('custom.invoices.invoice_no')</th>
                <th class="text-right" style="text-align: left !important;">@lang('custom.invoices.amount')</th>
            </tr>
            @foreach( $transactions->get() as $transaction )
            <tr class="info">
                <td>@if( ! empty( $transaction->created_at ) ) {{digiDate( $transaction->created_at, true )}} @else - @endif</td>
                <td>{{$transaction->applied_invoice->invoicenumberdisplay}}</td>
                <td class="text-right">{{digiCurrency( $transaction->amount, $currency_id )}}</td>                
            </tr>
            @endforeach

    </tbody>

</table>
</div>
</div>
