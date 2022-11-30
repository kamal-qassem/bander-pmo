@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('contracts::custom.contracts.title')</h3>
    @can('contract_create')
    <p>
        <a href="{{ route('admin.contracts.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        <a href="{{ route('admin.contract_types.index') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new_contract_type')</a>
        
	

   @include('contracts::admin.contracts.canvas.canvas')

    </p>
    @include('contracts::admin.contracts.filters')
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.contracts.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
            <span class="badge">
            @if( isAdmin() || isExecutive()  )
                {{\Modules\Contracts\Entities\Contract::count()}}
            @else
                {{\Modules\Contracts\Entities\Contract::where('customer_id', '=', getContactId())->count()}}
            @endif
            </span>
            </a></li>
            @can('contract_delete')
            @if ( isAdmin() || isExecutive()  )
            |
            <li><a href="{{ route('admin.contracts.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">
            @if( isAdmin() || isExecutive()  )
                {{\Modules\Contracts\Entities\Contract::onlyTrashed()->count()}}
            @else
                {{\Modules\Contracts\Entities\Contract::onlyTrashed()->where('customer_id', '=', getContactId())->count()}}
            @endif
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
            @include('contracts::admin.contracts.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('contracts::admin.contracts.records-display-scripts')
@endsection