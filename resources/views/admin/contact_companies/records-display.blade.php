<table class="table table-bordered table-striped ajaxTable @can('contact_company_delete_multi') @if ( request('show_deleted') != 1 ) dt-select  @endif @endcan">
    <thead>
        <tr>
            @can('contact_company_delete_multi')
               @if ( request('show_deleted') != 1 ) <th style="text-align:center;"><input type="checkbox" id="select-all" /></th> @endif
            @endcan

            <th>@lang('global.contact-companies.fields.name')</th>
            <th>@lang('global.contact-companies.fields.email')</th>
            <th>@lang('global.contact-companies.fields.address')</th>
            <th>@lang('global.contacts.fields.country')</th>
            <th>@lang('global.companies.fields.url')</th>
            <th>&nbsp;</th>

        </tr>
    </thead>
</table>