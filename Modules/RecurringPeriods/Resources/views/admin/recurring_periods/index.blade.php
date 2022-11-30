@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.recurring-periods.title')</h3>
    @can('recurring_period_create')
    <p>
        <a href="{{ route('admin.recurring_periods.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
        {{--<a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        @include('csvImport.modal', ['model' => 'RecurringPeriod'])--}}
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.recurring_periods.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
    <span class="badge">  
      {{\Modules\RecurringPeriods\Entities\RecurringPeriod::count()}}
         
      </span>
            </a></li> 
            @can('recurring_period_delete')
            |
            <li><a href="{{ route('admin.recurring_periods.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
                 <span class="badge">
      {{\Modules\RecurringPeriods\Entities\RecurringPeriod::onlyTrashed()->count()}}
     
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
            <table class="table table-bordered table-striped ajaxTable @can('recurring_period_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('recurring_period_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.recurring-periods.fields.title')</th>
                        <th>@lang('global.recurring-periods.fields.value')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('recurring_period_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.recurring_periods.mass_destroy') }}'; @endif
        @endcan

        $(document).ready(function () {
            window.dtDefaultOptions.buttons = [];
            window.dtDefaultOptions.ajax = '{!! route('admin.recurring_periods.index') !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptions.columns = [@can('recurring_period_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'title', name: 'title'},
                {data: 'value', name: 'value'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>
@endsection