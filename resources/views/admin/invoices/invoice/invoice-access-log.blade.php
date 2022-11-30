<?php
$access_log = $invoice->history();
?>
<hr>
<div id="invoice-template">
<h3>@lang('custom.invoices.history')</h3>
<table class="table table-bordered sys_table">
    <tbody>
        <tr class="text-view">
            <th>@lang('custom.common.time')</th>
            <th>@lang('custom.common.ip')</th>
            <th>@lang('custom.common.country')</th>
            <th>@lang('custom.common.city')</th>
            <th>@lang('custom.common.description')</th>
        </tr>
        @foreach( $access_log->get() as $transaction )
        <tr>
            <td>{{digiDate( $transaction->created_at, true )}}</td>
            <td>{{$transaction->ip_address}}</td>
            <td>{{$transaction->country}}</td>
            <td>{{$transaction->city}}</td>
            <td>
               <p><strong>@lang('custom.common.comments'):</strong>{{$transaction->comments}}</p>
               <p><strong>@lang('custom.common.browser'):</strong>{{$transaction->browser}}</p>                   
           </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>