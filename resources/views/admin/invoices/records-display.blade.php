<table class="table table-bordered table-striped ajaxTable @can('invoice_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('invoice_delete_multi')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.invoices.fields.invoice-no')</th>
            <th>@lang('global.invoices.fields.customer')</th>
            <th>@lang('global.invoices.fields.paymentstatus')</th>
            <th>@lang('global.invoices.fields.title')</th>                        
            <th>@lang('global.invoices.fields.status')</th>
            <th>@lang('global.invoices.fields.invoice-date')</th>
            <th>@lang('global.invoices.fields.invoice-due-date')</th>
            <th>@lang('global.invoices.fields.amount')</th>
            @if ( ! config('app.action_buttons_hover') )
                @if( request('show_deleted') == 1 )
                <th>&nbsp;</th>
                @else
                <th>&nbsp;</th>
                @endif
            @endif
        </tr>
    </thead>
</table>