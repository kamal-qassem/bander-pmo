<table class="table table-bordered table-striped {{ count($roles) > 0 ? 'datatable' : '' }} @can('role_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('role_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.roles.fields.title')</th>
                        <th>@lang('global.roles.title-module')</th>
                        <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($roles) > 0)
                        @foreach ($roles as $role)
                            <tr data-entry-id="{{ $role->id }}">
                                @can('role_delete_multi')
                                    <td></td>
                                @endcan

                                <td field-key='title'>{{ $role->title }}</td>
                                <td field-key='permission'>
                                    
                                    @foreach ($role->permission->groupBy('module') as $module => $singlePermission)
                                        <span class="label label-info label-many">{{ $module }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @can('role_view')
                                    <a href="{{ route('admin.roles.show',[$role->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('role_edit')
                                    <a href="{{ route('admin.roles.edit',[$role->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('role_delete_multi')
                            {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.roles.destroy', $role->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>