@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
    @include('admin.client_projects.operations.menu', array( 'client_project' => $project))

    <h3 class="page-title">@lang('global.mile-stones.title')</h3>
    @can('mile_stone_create')
    <p>
        <a href="{{ route('admin.mile_stones.create', $project->id) }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">

            <li><a href="{{ route('admin.mile_stones.index', $project->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
                 <span class="badge">  

              {{\App\MileStone::where('project_id', $project->id)->count()}}
                            
              </span>
            </a></li>
            @can('mile_stone_delete') |
            <li><a href="{{ route('admin.mile_stones.index', $project->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">
         
          {{\App\MileStone::where('project_id', $project->id)->onlyTrashed()->count()}}
         
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
            <table class="table table-bordered table-striped ajaxTable @can('mile_stone_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('mile_stone_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.mile-stones.fields.name')</th>
                        
                        <th>@lang('global.mile-stones.fields.description-visible-to-customer')</th>
                        <th>@lang('global.mile-stones.fields.due-date')</th>
                        
                        <th>@lang('global.mile-stones.fields.milestone-order')</th>
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
        @can('mile_stone_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.mile_stones.mass_destroy', $project->id) }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptions.ajax = '{!! route('admin.mile_stones.index', $project->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptions.columns = [@can('mile_stone_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'name', name: 'name'},
                
                {data: 'description_visible_to_customer', name: 'description_visible_to_customer'},
                {data: 'due_date', name: 'due_date'},
                
                {data: 'milestone_order', name: 'milestone_order'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection