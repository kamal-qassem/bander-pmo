@extends('layouts.app')

@section('content')
<div style="width:920px;margin-left:0px;margin-top: 5px;">
<a href="{{route('admin.recurring_invoices.show', $invoice->id)}}" class="btn btn-primary" role="button">@lang('custom.invoices.app_back_to_recurring_invoice')</a>

</div>
    <h3 class="page-title">@lang('custom.invoices.created-invoices')</h3>
    
      <div class="panel panel-default">
            <div class="panel-heading">
                @lang('global.invoices.title')
            </div>
            
            <div class="panel-body">
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
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.recurring-invoices.child-invoices', $invoice->id) !!}?show_deleted={{ request('show_deleted') }}';
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