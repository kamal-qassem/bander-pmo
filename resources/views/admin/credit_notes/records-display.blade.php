<table class="table table-bordered table-striped ajaxTable @can('credit_note_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            
            @can('credit_note_delete_multi')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.credit_notes.fields.credit-num')</th>
            <th>@lang('global.credit_notes.fields.credit-note-date')</th>
            <th>@lang('global.credit_notes.fields.customer')</th>
            <th>@lang('global.credit_notes.fields.credit-status')</th>
            <th>@lang('global.credit_notes.fields.reference')</th>
            <th>@lang('global.credit_notes.fields.amount')</th>
              @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
</table>