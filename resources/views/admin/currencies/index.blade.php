@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.currencies.title')</h3>
    @can('currency_create')
    <p>
        <a href="{{ route('admin.currencies.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
			
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.currencies.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
                  <span class="badge">{{\App\Currency::count()}}</span>

            </a></li> 
            @can('currency_delete')
            |
            <li><a href="{{ route('admin.currencies.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

                   <span class="badge"> {{\App\Currency::onlyTrashed()->count()}}</span>
            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <p style="padding: 10px;">@lang('custom.currencies.currency_layer_message', ['url' => '<a href="https://currencylayer.com" target="_blank">https://currencylayer.com</a>', 'settings_url' => '<a href="'.url('admin/mastersettings/settings/view/currency-settings').'" target="_blank">here</a>'])

        <a href="{{route('admin.currency.update_rates')}}" class="btn btn-xs btn-success">Update currency rates</a>
        
        </p>
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
          @include('admin.currencies.display-records')
        </div>
    </div>
@stop

@section('javascript') 
 @include('admin.currencies.display-records-scripts')
@endsection