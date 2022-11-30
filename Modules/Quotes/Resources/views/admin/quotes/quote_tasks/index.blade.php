@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @include('quotes::admin.quotes.invoice.invoice-menu', ['invoice' => $quote])

    <h3 class="page-title">@lang('global.quote-tasks.title')</h3>
    @can('quote_task_create')
    <p>
        <a href="{{ route('admin.quote_tasks.create', $quote->id) }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
        @if(!is_null(Auth::getUser()->role_id) && config('global.can_see_all_records_role_id') == Auth::getUser()->role_id)
            @if(Session::get('QuoteTask.filter', 'all') == 'my')
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
                    $count = Modules\Quotes\Entities\QuoteTask::where('quote_id', '=',$quote->id )->count();
                    ?>
            <li><a href="{{ route('admin.quote_tasks.index', $quote->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')<span class="badge">{{$count}}</span></a></li>
            @can('quote_task_delete') |
             <?php
                    $trash_count = Modules\Quotes\Entities\QuoteTask::where('quote_id', '=',$quote->id )->onlyTrashed()->count();
                    ?> 
            <li><a href="{{ route('admin.quote_tasks.index', $quote->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">{{$trash_count}}</span></a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('quote_task_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('quote_task_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.quote-tasks.fields.name')</th>
                       
                        <th>@lang('global.quote-tasks.fields.startdate')</th>
                        <th>@lang('global.quote-tasks.fields.duedate')</th>
                        <th>@lang('global.quote-tasks.fields.status')</th>
                       
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
        @can('quote_task_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.quote_tasks.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.quote_tasks.index', $quote->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('quote_task_delete_multi')
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