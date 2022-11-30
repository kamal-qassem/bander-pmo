@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('proposals::custom.proposals.title')</h3>
    @can('proposal_create')
    <p>
        <a href="{{ route('admin.proposals.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
	

   @include('proposals::admin.proposals.canvas.canvas')

    </p>
    @include('proposals::admin.proposals.filters')
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.proposals.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
            <span class="badge"> {{\Modules\Proposals\Entities\Proposal::count()}} </span>
            </a></li>
            @can('proposal_delete')
            @if ( isAdmin() )
            |
            <li><a href="{{ route('admin.proposals.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">{{\Modules\Proposals\Entities\Proposal::onlyTrashed()->count()}}
            </span>
            </a></li>
            @endif
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('proposals::admin.proposals.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('proposals::admin.proposals.records-display-scripts')
@endsection