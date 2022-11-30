@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title')</h3>
    <p>
    @can('order_create')
        <a href="{{ route('admin.orders.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
    @endcan

    @include('orders::admin.orders.canvas.canvas')
    
        
    </p>

    @include('orders::admin.orders.filters')

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.orders.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
                <span class="badge"> 
            @if ( isCustomer() )
                {{\Modules\Orders\Entities\Order::where( 'customer_id', '=', getContactId())->count()}}
            @else
                {{\Modules\Orders\Entities\Order::count()}}
            @endif
        </span>
            </a></li>
            @can('order_delete')
            @if ( ! isCustomer() )
             | <li><a href="{{ route('admin.orders.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">
                 
                    @lang('global.app_trash')
                    <span class="badge">
                        {{\Modules\Orders\Entities\Order::onlyTrashed()->count()}}
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
            <table class="table table-bordered table-striped ajaxTable @can('order_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('order_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan
                        <th>@lang('orders::global.orders.fields.id')</th>
                        <th>@lang('orders::global.orders.fields.customer')</th>
                        <th>@lang('orders::global.orders.fields.status')</th>
                        <th>@lang('orders::global.orders.fields.price')</th>
                        <th>@lang('orders::global.orders.fields.billing-cycle')</th>
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
        @can('order_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.orders.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.orders.index') !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('order_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan
                {data: 'id', name: 'id'},
                {data: 'customer.first_name', name: 'customer.first_name'},
                {data: 'status', name: 'status'},
                {data: 'price', name: 'price'},
                {data: 'billing_cycle.title', name: 'billing_cycle.title'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection