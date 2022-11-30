@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
<h3 class="page-title">@lang('global.invoices.title')</h3>
    <p>
    @can('invoice_create')
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
    
    @include('admin.invoices.canvas.canvas')
    
    </p>
    
    @include('admin.invoices.filters')

    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.invoices.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">
            @lang('global.app_all')
            <span class="badge">{{\App\Invoice::count()}}</span>
            </a></li>
            @can('invoice_delete')  
                |
                <li><a href="{{ route('admin.invoices.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
                <span class="badge"> {{\App\Invoice::onlyTrashed()->count()}}</span>
                </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.invoices.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.invoices.records-display-scripts')
@endsection