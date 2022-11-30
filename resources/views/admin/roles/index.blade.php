@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.roles.title')</h3>
    @can('role_create')
    <p>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        {{--<a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        @include('csvImport.modal', ['model' => 'Role'])
        --}}
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
             @include('admin.roles.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.roles.records-display-scripts')
@endsection