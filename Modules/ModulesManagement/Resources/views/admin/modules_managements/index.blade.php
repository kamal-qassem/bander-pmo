@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('modulesmanagement::global.modules-management.title')</h3>
    @if(Gate::check('modules_management_create') || Gate::check('modules_management_upload') )
    <p>
        @can('modules_management_create')
        <a href="{{ route('admin.modules_managements.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        @endcan
        @if( Gate::check('modules_management_upload') && isEnable('debug') )
        &nbsp;|&nbsp;<a href="{{ route('admin.plugins.upload') }}" class="btn btn-success"><i class="fa fa-upload"></i>&nbsp;@lang('custom.common.upload')</a>
        @endif
    </p>
    @endif

    @if( isEnable('debug') )
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.modules_managements.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
    <span class="badge">{{\Modules\ModulesManagement\Entities\ModulesManagement::count()}}</span>
            </a></li> 
            @can('modules_management_delete')|
            <li><a href="{{ route('admin.modules_managements.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
                   <span class="badge">{{\Modules\ModulesManagement\Entities\ModulesManagement::onlyTrashed()->count()}} </span>
            </a></li>
            @endcan
        </ul>
    </p>
    @endif
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @if( isEnable('debug') ) @can('modules_management_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan @endif">
                <thead>
                    <tr>
                        @if( isEnable('debug') )
                            @can('modules_management_delete_multi')
                                @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                            @endcan
                        @endif

                        <th>@lang('modulesmanagement::global.modules-management.fields.name')</th>
                        <th>@lang('modulesmanagement::global.modules-management.fields.slug')</th>
                        <th>@lang('modulesmanagement::global.modules-management.fields.type')</th>
                        <th>@lang('modulesmanagement::global.modules-management.fields.enabled')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @if( isEnable('debug') )
        @can('modules_management_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.modules_managements.mass_destroy') }}'; @endif
        @endcan
        @endif
        $(document).ready(function () {
            window.dtDefaultOptions.buttons = [];

            window.dtDefaultOptions.ajax = '{!! route('admin.modules_managements.index') !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptions.columns = [@if( isEnable('debug') )@can('modules_management_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan
                @endif
                {data: 'name', name: 'name'},
                {data: 'slug', name: 'slug'},
                {data: 'type', name: 'type'},
                {data: 'enabled', name: 'enabled'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection