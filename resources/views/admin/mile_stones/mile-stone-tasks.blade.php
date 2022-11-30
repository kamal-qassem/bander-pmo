@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))

    <h3 class="page-title">

    @lang('global.project-tasks.title')
    @if( 'mileStoneTasks' === getController('action') )
        <?php
        $details = \App\MileStone::find( $mile_stone_id );
        ?>
        @if( $details )
            ( {{ $details->name }} )
        @endif
    @endif
</h3>    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('project_task_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('project_task_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.project-tasks.fields.name')</th>
                        <th>@lang('global.project-tasks.fields.priority')</th>
                        <th>@lang('global.project-tasks.fields.startdate')</th>
                        <th>@lang('global.project-tasks.fields.duedate')</th>
                        <th>@lang('global.project-tasks.fields.status')</th>
                        
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
        @can('project_task_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.project_tasks.mass_destroy', $project->id) }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptions.ajax = '{!! route('admin.mile_stones.tasks', [ 'project_id' => $project->id, 'mile_stone_id' => $mile_stone_id ] ) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptions.columns = [@can('project_task_delete')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'name', name: 'name'},
                {data: 'priority.title', name: 'priority.title'},
                {data: 'startdate', name: 'startdate'},
                {data: 'duedate', name: 'duedate'},
                {data: 'status.title', name: 'status.title'},
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection