@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.client-projects.title')</h3>
    <p>
    @can('client_project_create')
        <a href="{{ route('admin.client_projects.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
    @endcan
   @include('admin.client_projects.canvas.canvas') 
        
        
    </p>

    @include('admin.client_projects.filters')

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.client_projects.index') }}" style="{{ request('show_deleted') == 1 ? '' : '    font-weight: 700' }}">@lang('global.app_all')
                 <?php
                   $project = \App\ClientProject::query();
                ?>
                @if ( isEmployee() )
                <?php
                $emp_count = $project->whereHas("assigned_to",
                   function ($query) {
                       $query->where('id', Auth::id());
                   })->count();
                ?>
                <span class="badge"> {{ $emp_count }}</span>
                @elseif ( isProjectManager() )
                <span class="badge"> {{\App\ClientProject::count()}}</span>
                @else
                <span class="badge"> {{\App\ClientProject::count()}}</span>
                @endif
            </a></li>
            @can('client_project_delete')
             |
            <li><a href="{{ route('admin.client_projects.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

                 <span class="badge">{{\App\ClientProject::onlyTrashed()->count()}}</span>

            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.client_projects.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.client_projects.records-display-scripts')
@endsection