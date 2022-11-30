 <table class="table table-bordered table-striped ajaxTable @can('currency_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('currency_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.currencies.fields.name')</th>
                        <th>@lang('global.currencies.fields.symbol')</th>
                        <th>@lang('global.currencies.fields.code')</th>
                        <th>@lang('global.currencies.fields.rate')</th>
                        <th>@lang('global.currencies.fields.status')</th>
                        <th>@lang('global.currencies.fields.is_default')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
            </table>