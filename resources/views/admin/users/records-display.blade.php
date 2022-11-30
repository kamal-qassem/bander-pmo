 <table class="table table-bordered table-striped ajaxTable @can('user_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('user_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.users.fields.name')</th>
                        <th>@lang('global.users.fields.email')</th>
                        <th>@lang('global.users.fields.role')</th>                        
                        <th>@lang('global.users.fields.status')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
            </table>