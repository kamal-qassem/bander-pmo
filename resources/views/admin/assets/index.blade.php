@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.assets.title')</h3>
    @can('asset_create')
    <p>
        <a href="{{ route('admin.assets.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan


    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.assets.records-display')
        </div>
    </div>
@stop

@section('javascript') 
     @include('admin.assets.records-display-scripts')
@endsection