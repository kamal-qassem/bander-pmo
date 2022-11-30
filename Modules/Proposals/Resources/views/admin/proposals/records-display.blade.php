<table class="table table-bordered table-striped ajaxTable @can('recurring_invoice_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('recurring_invoice_delete')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.proposals.fields.proposal-no')</th>
            <th>@lang('global.recurring-invoices.fields.title')</th>
            <th>@lang('global.proposals.fields.customer')</th>
            <th>@lang('global.recurring-invoices.fields.amount')</th>
            <th>@lang('global.proposals.fields.status')</th>
            
            
            <th>@lang('global.proposals.fields.publish-status')</th>
            <th>@lang('global.proposals.fields.proposal-date')</th>
            <th>@lang('global.proposals.fields.proposal-expiry-date')</th>
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
</table>