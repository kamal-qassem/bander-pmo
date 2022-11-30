<table class="table table-bordered table-striped ajaxTable @can('transfer_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('transfer_delete_multi')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.transfers.fields.from')</th>
            <th>@lang('global.transfers.fields.to')</th>
            <th>@lang('global.transfers.fields.date')</th>
            <th>@lang('global.transfers.fields.amount')</th>
            <th>@lang('global.transfers.fields.ref-no')</th>
            <th>@lang('global.transfers.fields.payment-method')</th>
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
</table>