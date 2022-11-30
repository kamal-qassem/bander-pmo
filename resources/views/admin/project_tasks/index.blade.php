@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))

    <h3 class="page-title">@lang('global.project-tasks.title')</h3>
    @can('project_task_create')
    <p>
        <a href="{{ route('admin.project_tasks.create', $project->id) }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
        @if(!is_null(Auth::getUser()->role_id) && config('global.can_see_all_records_role_id') == Auth::getUser()->role_id)
            @if(Session::get('ProjectTask.filter', 'all') == 'my')
                <a href="?filter=all" class="btn btn-default">Show all records</a>
            @else
                <a href="?filter=my" class="btn btn-default">Filter my records</a>
            @endif
        @endif
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.project_tasks.index', $project->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
            <span class="badge">  
             
              {{\App\ProjectTask::where('project_id', $project->id)->count()}}
            


              </span>
            </a></li> 
            @can('project_task_delete')
            |
            <li><a href="{{ route('admin.project_tasks.index', $project->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">
          {{\App\ProjectTask::where('project_id', $project->id)->onlyTrashed()->count()}}


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
            <table class="table table-bordered table-striped ajaxTable @can('project_task_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('project_task_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.project-tasks.fields.name')</th>
                        <th>@lang('global.project-tasks.fields.priority')</th>
                        <th>@lang('global.project-tasks.fields.startdate')</th>
                        <th>@lang('global.project-tasks.fields.duedate')</th>
                        <th>@lang('global.project-tasks.fields.status')</th>
                        <!--<th>@lang('global.project-tasks.fields.recurring')</th>
                        <th>@lang('global.project-tasks.fields.recurring-value')</th>
                        <th>@lang('global.project-tasks.fields.last-recurring-date')</th>
                        <th>@lang('global.project-tasks.fields.billable')</th>
                        <th>@lang('global.project-tasks.fields.hourly-rate')</th>
                        <th>@lang('global.project-tasks.fields.milestone')</th>
                        <th>@lang('global.project-tasks.fields.created-by')</th>-->
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
        @can('project_task_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.project_tasks.mass_destroy', $project->id) }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptions.ajax = '{!! route('admin.project_tasks.index', $project->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptions.columns = [@can('project_task_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'name', name: 'name'},
                {data: 'priority.title', name: 'priority.title',searchable: false, sortable: false},
                {data: 'startdate', name: 'startdate'},
                {data: 'duedate', name: 'duedate'},
                {data: 'status.title', name: 'status.title',searchable: false, sortable: false},
                //{data: 'recurring.title', name: 'recurring.title'},
                //{data: 'recurring_value', name: 'recurring_value'},
                //{data: 'last_recurring_date', name: 'last_recurring_date'},
                //{data: 'billable', name: 'billable'},
                //{data: 'hourly_rate', name: 'hourly_rate'},
                //{data: 'milestone', name: 'milestone'},
                //{data: 'created_by.name', name: 'created_by.name'},
            
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection