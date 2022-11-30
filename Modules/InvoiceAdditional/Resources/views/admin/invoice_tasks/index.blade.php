@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @include('admin.invoices.invoice.invoice-menu', ['invoice' => $invoice])
    
    <h3 class="page-title">@lang('global.invoice-tasks.title')</h3>
    @can('invoice_task_create')
    <p>
        <a href="{{ route('admin.invoice_tasks.create', $invoice->id) }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

                   

    <p>
        <ul class="list-inline">
                 <?php
                    $count = Modules\InvoiceAdditional\Entities\InvoiceTask::where('invoice_id', '=',$invoice->id )->count();
                    ?>
                    <li>
                        <a href="{{route('admin.invoice_tasks.index', $invoice->id)}}"  style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')<span class="badge">{{$count}}</span>
                        </a></li>
                        @can('invoice_task_delete')
                        | 
                   <?php
                    $trash_count = Modules\InvoiceAdditional\Entities\InvoiceTask::where('invoice_id', '=',$invoice->id )->onlyTrashed()->count();
                    ?>          
             <li><a href="{{ route('admin.invoice_tasks.index', $invoice->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge"> 
               {{ $trash_count  }}
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
            <table class="table table-bordered table-striped ajaxTable @can('invoice_task_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('invoice_task_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.invoice-tasks.fields.name')</th>
                     
                        <th>@lang('global.invoice-tasks.fields.startdate')</th>
                        <th>@lang('global.invoice-tasks.fields.duedate')</th>
                        <th>@lang('global.invoice-tasks.fields.status')</th>
                        <th>@lang('global.invoice-tasks.fields.assigned-to')</th>
                    
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
        @can('invoice_task_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.invoice_tasks.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.invoice_tasks.index', $invoice->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('invoice_task_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'name', name: 'name'},
            
                {data: 'startdate', name: 'startdate'},
                {data: 'duedate', name: 'duedate'},
                {data: 'status.title', name: 'status.title'},
                {data: 'assigned_to.name', name: 'assigned_to.name', sortable: false},
              
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection