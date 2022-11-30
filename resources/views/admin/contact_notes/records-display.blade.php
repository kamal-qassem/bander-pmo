<table class="table table-bordered table-striped ajaxTable @can('contact_note_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('contact_note_delete_multi')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.contact-notes.fields.title')</th>
            <th>@lang('global.contact-notes.fields.contact')</th>
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
</table>