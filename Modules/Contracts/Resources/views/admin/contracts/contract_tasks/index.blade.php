@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @include('contracts::admin.contracts.invoice.invoice-menu', ['invoice' => $contract])

    <h3 class="page-title">@lang('global.contract-tasks.title')</h3>
    @can('contract_task_create')
    <p>
        <a href="{{ route('admin.contract_tasks.create', $contract->id) }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
        @if(!is_null(Auth::getUser()->role_id) && config('global.can_see_all_records_role_id') == Auth::getUser()->role_id)
            @if(Session::get('ContractTask.filter', 'all') == 'my')
                <a href="?filter=all" class="btn btn-default">Show all records</a>
            @else
                <a href="?filter=my" class="btn btn-default">Filter my records</a>
            @endif
        @endif
    </p>
    @endcan

    <p>
        <ul class="list-inline">
              <?php
                    $count = Modules\Contracts\Entities\ContractTask::where('contract_id', '=',$contract->id )->count();
                    ?>
            <li><a href="{{ route('admin.contract_tasks.index', $contract->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')<span class="badge">{{$count}}</span></a></li>
            @can('contract_task_delete')
            |
             <?php
                    $trash_count = Modules\Contracts\Entities\ContractTask::where('contract_id', '=',$contract->id )->onlyTrashed()->count();
                    ?> 
           
            <li><a href="{{ route('admin.contract_tasks.index', $contract->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">{{$trash_count}}</span></a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('contract_task_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('contract_task_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.contract-tasks.fields.name')</th>
                        <!--<th>@lang('global.contract-tasks.fields.priority')</th>-->
                        <th>@lang('global.contract-tasks.fields.startdate')</th>
                        <th>@lang('global.contract-tasks.fields.duedate')</th>
                        <th>@lang('global.contract-tasks.fields.status')</th>
                        <!--<th>@lang('global.contract-tasks.fields.recurring')</th>
                        <th>@lang('global.contract-tasks.fields.recurring-value')</th>
                        <th>@lang('global.contract-tasks.fields.last-recurring-date')</th>
                        <th>@lang('global.contract-tasks.fields.billable')</th>
                        <th>@lang('global.contract-tasks.fields.hourly-rate')</th>
                        <th>@lang('global.contract-tasks.fields.milestone')</th>
                        <th>@lang('global.contract-tasks.fields.created-by')</th>-->
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
        @can('contract_task_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.contract_tasks.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.contract_tasks.index', $contract->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('contract_task_delete')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'name', name: 'name'},
               
                {data: 'startdate', name: 'startdate'},
                {data: 'duedate', name: 'duedate'},
                {data: 'status_id', name: 'status_id'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection