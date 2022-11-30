@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.user-actions.title')</h3>
    @can('user_action_create')
    <p>
        
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.user_actions.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.user_actions.records-display-scripts')
@endsection
