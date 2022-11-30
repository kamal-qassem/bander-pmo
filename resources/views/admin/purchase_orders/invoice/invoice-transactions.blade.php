<?php
$currency_id = $invoice->currency_id;
$transactions = $invoice->transactions();
if ( $transactions->count() > 0 ) {
    
?>
<div id="invoice-template">
<h3>@lang('custom.invoices.related-transactions')</h3>
    <table class="table table-bordered sys_table">
        <tbody>
            <tr>
                <th>@lang('custom.purchase_orders.date')</th>
                <th>Account</th>
                <th class="text-right">@lang('custom.purchase_orders.amount')</th>
                <th>@lang('custom.purchase_orders.description')</th>
                <th>@lang('global.app_status')</th>
              
            </tr>
            @foreach( $transactions->get() as $transaction )
            <?php
            $account = $transaction->account()->where('id', '=', $transaction->account_id)->first();
            $account_name = ! empty( $account ) ? $account->name : '';
            ?>
            <tr class="info">
                <td>{{digiDate( $transaction->created_at, true )}}</td>
                <td>{{$account_name}}</td>
                <td class="text-right amount">{{digiCurrency( $transaction->amount ,$currency_id)}}
                      <p><span class="label label-info label-many">
                        <?php
                        $paymentmethod = $transaction->paymentmethod;
                        if ( is_numeric( $paymentmethod ) ) {
                            $paymentmethod = \App\PaymentGateway::find($paymentmethod)->name;
                        }
                        ?>
                        {{$paymentmethod}}</span></p>

                </td>
                <td>{{$transaction->description}}</td>
                <td>{{$transaction->payment_status ?? $transaction->payment_status}}</td>
              
            </tr>
            @endforeach

    </tbody></table>
</div>
<?php } ?>