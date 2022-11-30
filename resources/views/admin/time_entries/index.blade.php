@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))

    <h3 class="page-title">@lang('global.time-entries.title')</h3>
    @can('time_entry_create')
    <p>
        <a href="{{ route('admin.time_entries.create', $project->id) }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>   
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.time_entries.index', $project->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
                <span class="badge">  

              {{\App\TimeEntry::where('project_id', $project->id)->count()}}
   
              </span>
            </a></li>
            @can('time_entry_delete')
            |
            <li><a href="{{ route('admin.time_entries.index', $project->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

                <span class="badge">
          
          {{\App\TimeEntry::where('project_id', $project->id)->onlyTrashed()->count()}}
          
          </span>     

            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('time_entry_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('time_entry_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.app_task')</th>
                        <th>@lang('global.project-tasks.completed-by')</th>
                        <th>@lang('global.time-entries.fields.start-date')</th>
                        <th>@lang('global.time-entries.fields.end-date')</th>
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
        @can('time_entry_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.time_entries.mass_destroy', $project->id) }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptions.ajax = '{!! route('admin.time_entries.index', $project->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptions.columns = [@can('time_entry_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan
                {data: 'task.name', name: 'task.name',searchable: false, sortable: false},
                {data: 'completed_by.name', name: 'completed_by.name',searchable: false, sortable: false},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection