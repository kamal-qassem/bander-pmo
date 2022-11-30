@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
@include('admin.client_projects.operations.menu', array('client_project' => $project))


<div class="row">
  <div class="col-md-6">
       <h3 class="page-title" style="margin-bottom: -20px;">@lang('global.invoices.title')</h3>
  </div>
  </div>

    <p style="margin-bottom: 17px;">
    
    @include('admin.invoices.canvas.canvas', compact('project'))
    
    
    </p>

    @include('admin.invoices.filters', compact('project'))

  
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.client_projects.invoices', $project->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">
            @lang('global.app_all')
            <span class="badge"> 
            @if( isAdmin() || isProjectManager() || isExecutive())
               {{\App\Invoice::where('project_id', '=', $project->id)->count()}}
            @else
                {{\App\Invoice::where('project_id', '=', $project->id)->where('customer_id', '=', getContactId())->count()}}
            @endif
            </span>
            </a></li>
            @if ( isAdmin() )
            |
            <li><a href="{{ route('admin.client_projects.invoices', $project->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge"> 
            @if( isAdmin() )
               {{\App\Invoice::where('project_id', '=', $project->id)->onlyTrashed()->count()}}
            @else
            {{\App\Invoice::where('project_id', '=', $project->id)->onlyTrashed()->where('customer_id', '=', getContactId())->count()}}
            @endif
            </span>
            </a></li>
            @endif
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('invoice_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('invoice_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.invoices.fields.invoice-no')</th>
                        <th>@lang('global.invoices.fields.customer')</th>
                        <th>@lang('global.invoices.fields.paymentstatus')</th>
                        <th>@lang('global.invoices.fields.title')</th>                        
                        <th>@lang('global.invoices.fields.status')</th>
                        <th>@lang('global.invoices.fields.invoice-date')</th>
                        <th>@lang('global.invoices.fields.invoice-due-date')</th>
                        <th>@lang('global.invoices.fields.amount')</th>
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
        @can('invoice_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.invoices.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.client_projects.invoices', $project->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('invoice_delete')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan
                {data: 'invoice_no', name: 'invoice_no'},
                {data: 'customer.first_name', name: 'customer.first_name'},
                {data: 'paymentstatus', name: 'paymentstatus'},
                {data: 'title', name: 'title'},
                
                {data: 'status', name: 'status'},
                {data: 'invoice_date', name: 'invoice_date'},
                {data: 'invoice_due_date', name: 'invoice_due_date'},
                {data: 'amount', name: 'amount'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection