<table class="table table-bordered table-striped ajaxTable @can('client_project_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
    <thead>
        <tr>
            @can('client_project_delete_multi')
                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
            @endcan

            <th>@lang('global.client-projects.fields.title')</th>
            <th>@lang('global.client-projects.fields.client')</th>
            <th>@lang('global.client-projects.fields.assigned-to')</th>
            <th>@lang('global.client-projects.fields.start-date')</th>
            <th>@lang('global.client-projects.fields.due-date')</th>
            <th>@lang('global.client-projects.fields.priority')</th>
            <th>@lang('global.client-projects.fields.status')</th>
            <th>@lang('global.purchase-orders.fields.currency')</th>
            
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
</table>