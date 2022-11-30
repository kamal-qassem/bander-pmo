@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.departments.title')</h3>
    @can('department_create')
    <p>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        <a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        @include('csvImport.modal', ['model' => 'Department', 'csvtemplatepath' => 'departments.csv','duplicatecheck' => 'name'])
        
        @if(!is_null(Auth::getUser()->role_id) && config('global.can_see_all_records_role_id') == Auth::getUser()->role_id)
            @if(Session::get('Department.filter', 'all') == 'my')
                <a href="?filter=all" class="btn btn-default">Show all records</a>
            @else
                <a href="?filter=my" class="btn btn-default">Filter my records</a>
            @endif
        @endif
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.departments.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
             <span class="badge"> 
           
               {{\App\Department::count()}}
                       </span>

            </a></li> 
            @can('department_delete')
            |
            <li><a href="{{ route('admin.departments.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

                 <span class="badge"> 
            
               {{\App\Department::onlyTrashed()->count()}}
            
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
          @include('admin.departments.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.departments.records-display-scripts')
@endsection