@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contact-notes.title')</h3>
    @can('contact_note_create')
    <p>
        <a href="{{ route('admin.contact_notes.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.contact_notes.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')

                 <span class="badge">{{\App\ContactNote::count()}}</span>

            </a></li> 
            @can('contact_note_delete')
            |
            <li><a href="{{ route('admin.contact_notes.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

                <span class="badge">{{\App\ContactNote::onlyTrashed()->count()}}</span>

            </a></li>
            @endcan

        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.contact_notes.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.contact_notes.records-display-scripts')
@endsection