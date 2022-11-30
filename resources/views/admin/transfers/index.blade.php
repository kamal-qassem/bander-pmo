@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.transfers.title')</h3>
    @can('transfer_create')
    <p>
        <a href="{{ route('admin.transfers.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        {{--<a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        @include('csvImport.modal', ['model' => 'Transfer'])
        --}}
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.transfers.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')

          <span class="badge"> 
            
               {{\App\Transfer::count()}}
            
                        </span>

            </a></li> 
            @can('transfer_delete')
            |
            <li><a href="{{ route('admin.transfers.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

                  <span class="badge"> 
            
               {{\App\Transfer::onlyTrashed()->count()}}
            
            
            </span>

            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.transfers.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.transfers.records-display-scripts')
@endsection