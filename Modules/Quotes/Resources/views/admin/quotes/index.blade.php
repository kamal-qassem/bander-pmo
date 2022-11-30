@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quotes::custom.quotes.title')</h3>
    @can('quote_create')
    <p>
        <a href="{{ route('admin.quotes.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
    

   @include('quotes::admin.quotes.canvas.canvas')

    </p>
    @include('quotes::admin.quotes.filters')
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.quotes.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
            <span class="badge">{{\Modules\Quotes\Entities\Quote::count()}}</span>
            </a></li>
            @can('quote_delete')            
            |
            <li><a href="{{ route('admin.quotes.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">{{\Modules\Quotes\Entities\Quote::onlyTrashed()->count()}}</span>
            </a></li>           
            @endcan

        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('quotes::admin.quotes.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('quotes::admin.quotes.records-display-scripts')
@endsection