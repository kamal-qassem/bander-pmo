@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.users.title')</h3>
       

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
           @include('admin.users.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.users.records-display-scripts')
@endsection