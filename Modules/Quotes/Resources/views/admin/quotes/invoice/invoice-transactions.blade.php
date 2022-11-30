<?php
$transactions = $invoice->transactions();
if ( $transactions->count() > 0 ) {
?>
<h3>@lang('custom.invoices.related-transactions')</h3>
    <table class="table table-bordered sys_table">
        <tbody>
            <tr>
                <th>@lang('custom.invoices.date')</th>
                <th>Account</th>
                <th class="text-right">@lang('custom.invoices.amount')</th>
                <th>@lang('custom.invoices.description')</th>
            </tr>
            @foreach( $transactions->get() as $transaction )
            <?php
            $account = $transaction->account()->where('id', '=', $transaction->account_id)->first();
            $account_name = ! empty( $account ) ? $account->name : '';
            ?>
            <tr class="info">
                <td>{{digiDate( $transaction->created_at, true )}}</td>
                <td>{{$account_name}}</td>
                <td class="text-right amount">{{digiCurrency( $transaction->amount,$invoice->currency_id)}}</td>
                <td>{{$transaction->description}}</td>
            </tr>
            @endforeach

    </tbody></table>
<?php } ?>