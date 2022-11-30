<table class="table table-bordered table-striped ajaxTable @can('recurring_invoice_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('recurring_invoice_delete')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.contracts.fields.contract-no')</th>
            <th>@lang('global.contracts.fields.customer')</th>
            <th>@lang('global.contracts.fields.contract_value')</th>
            <th>@lang('global.contracts.fields.contract_type')</th>
            <th>@lang('global.contracts.fields.status')</th>
            <th>@lang('global.contracts.fields.title')</th>
            
            <th>@lang('global.contracts.fields.publish-status')</th>
            <th>@lang('global.contracts.fields.contract-date')</th>
            <th>@lang('global.contracts.fields.contract-expiry-date')</th>
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
</table>