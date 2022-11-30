<table class="table table-bordered table-striped ajaxTable @can('quote_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('quote_delete_multi')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.quotes.fields.quote-no')</th>
            <th>@lang('global.quotes.fields.customer')</th>
            <th>@lang('global.recurring-invoices.fields.amount')</th>
            <th>@lang('global.quotes.fields.status')</th>
            <th>@lang('global.recurring-invoices.fields.title')</th>
            
            <th>@lang('global.quotes.fields.publish-status')</th>
            <th>@lang('global.quotes.fields.quote-date')</th>
            <th>@lang('global.quotes.fields.quote-expiry-date')</th>
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
</table>