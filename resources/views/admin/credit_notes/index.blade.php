@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    
<h3 class="page-title">@lang('global.credit_notes.title')</h3>
    <p>
    @can('credit_note_create')    
        <a href="{{ route('admin.credit_notes.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
    
    @include('admin.credit_notes.canvas.canvas')
    
    </p>
    
    @include('admin.credit_notes.filters')

    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.credit_notes.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">
            @lang('global.app_all')
            <span class="badge">{{\App\CreditNote::count()}}</span>
            </a></li>
            @can('credit_note_delete') 
            |
            <li><a href="{{ route('admin.credit_notes.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge"> {{\App\CreditNote::onlyTrashed()->count()}}</span>
            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.credit_notes.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.credit_notes.records-display-scripts')
@endsection