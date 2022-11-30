@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu') 
    <h3 class="page-title">@lang('global.client-projects.title-tickets')</h3>
    <p>
    @can('client_project_create')
        <a href="{{ route('admin.project_tickets.create', $client_project->id) }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
    @endcan
   @include('admin.project_tickets.canvas.canvas') 
       

        
    </p>

    @include('admin.project_tickets.filters')    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('client_project_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('client_project_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.client-projects.subject')</th>
                        <th>@lang('global.client-projects.status')</th>
                        
                        <th>@lang('global.client-projects.last-updated')</th>
                        <th>@lang('global.client-projects.contact')</th>
                        <th>@lang('global.client-projects.agent')</th>
                        <th>@lang('global.client-projects.created')</th>
                        
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
        @can('client_project_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.project_tickets.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.project_tickets.index', $client_project->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('client_project_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'subject', name: 'subject'},
                {data: 'status', name: 'status',searchable: false},
                
                {data: 'updated_at', name: 'updated_at'},
                {data: 'owner', name: 'owner',searchable: false},
                {data: 'agent', name: 'agent',searchable: false,sortable: false},
                {data: 'created_at', name: 'created_at'},
                
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection