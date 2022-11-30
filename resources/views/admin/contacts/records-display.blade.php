<table class="table table-bordered table-striped ajaxTable @can('contact_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('contact_delete_multi')
               @if ( request('show_deleted') != 1 ) <th style="text-align:center;"><input type="checkbox" id="select-all" /></th> @endif
            @endcan

            <th>@lang('global.contacts.fields.name')</th>
            <th>@lang('global.contacts.fields.contact-type')</th>
            <th>@lang('global.contacts.fields.email')</th>
            <th>@lang('global.contacts.fields.address')</th>
            <th>&nbsp;</th>

        </tr>
    </thead>
</table>