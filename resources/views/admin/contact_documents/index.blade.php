@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contact-documents.title')</h3>
    @can('contact_document_create')
    <p>
        <a href="{{ route('admin.contact_documents.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.contact_documents.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
          <span class="badge">  {{\App\ContactDocument::count()}}</span>
            </a></li> 
            @can('contact_document_delete')
            |
            <li><a href="{{ route('admin.contact_documents.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

             <span class="badge">  {{\App\ContactDocument::onlyTrashed()->count()}}</span>

            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.contact_documents.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.contact_documents.records-display-scripts')
@endsection