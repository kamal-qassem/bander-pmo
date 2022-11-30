@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.assets-history.title')</h3>
    @can('assets_history_create')
    <p>
        
        
    </p>
    @endcan

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.assets_histories.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.assets_histories.records-display-scripts')
@endsection