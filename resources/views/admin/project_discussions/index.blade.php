@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))

    <h3 class="page-title">@lang('global.project-discussions.title')</h3>
    @can('project_discussion_create')
    <p>
        <a href="{{ route('admin.project_discussions.create', $project->id) }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.project_discussions.index', $project->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
            <span class="badge">            
            {{\App\ProjectDiscussion::where('project_id', $project->id)->count()}}           
            </span>
            </a></li> 
            @can('project_discussion_delete')
            |
            <li><a href="{{ route('admin.project_discussions.index', $project->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">            
            {{\App\ProjectDiscussion::onlyTrashed()->where('project_id', $project->id)->count()}}           
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
            <table class="table table-bordered table-striped ajaxTable @can('project_discussion_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('project_discussion_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.project-discussions.fields.subject')</th>
                        
                        <th>@lang('global.project-discussions.fields.visible-to-customer')</th>
                        <th>@lang('global.project-discussions.fields.last-activity')</th>
                        <th>@lang('global.project-discussions.total-comments')</th>
                        
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
        @can('project_discussion_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.project_discussions.mass_destroy', $project->id) }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.project_discussions.index', $project->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('project_discussion_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'subject', name: 'subject'},
                
                {data: 'show_to_customer', name: 'show_to_customer'},
                {data: 'last_activity', name: 'last_activity'},
                {data: 'total_comments', name: 'total_comments',searchable:false,sortable: false},
                
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection