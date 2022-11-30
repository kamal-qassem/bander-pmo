@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.tasks.title')</h3>
    <p>
    @can('task_create')
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
    @endcan

    @include('admin.tasks.canvas.canvas')

        
    </p>

    @include('admin.tasks.filters')

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
           @include('admin.tasks.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.tasks.records-display-scripts')
@endsection

