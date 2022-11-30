@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $warehouse->name }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.warehouses.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'warehouse_edit',
            ], 
            [
                'route' => 'admin.warehouses.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'warehouse_delete',
            ],
        ],
        'record' => $warehouse,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif

        <?php
        $tabs = [
            'details_active' => 'active',
            'products_active' => '',
            'purchase_orders_active'=>'',

        ];
        
        if ( ! empty( $list ) ) {
            foreach ($tabs as $key => $value) {
                $tabs[ $key ] = '';
                if ( substr( $key, 0, -7) == $list ) {
                    $tabs[ $key ] = 'active';
                }
            }
        }
        ?>

        <div class="panel-body table-responsive">
            <!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
 
<li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>  
@if( isPluginActive('product') ) 
<li role="presentation" class="{{$tabs['products_active']}}"><a href="{{route('admin.warehouses.show', [ 'warehouse_id' => $warehouse->id, 'list' => 'products' ])}}" title= "@lang('others.canvas.products')">@lang('others.canvas.products')</a></li>
@endif
@if( isPluginActive('purchase_order') )
<li role="presentation" class="{{$tabs['purchase_orders_active']}}"><a href="{{route('admin.warehouses.show', [ 'warehouse_id' => $warehouse->id, 'list' => 'purchase_orders' ])}}">@lang('global.purchase-orders.title')</a></li>
@endif

</ul>

<!-- Tab panes -->
<div class="tab-content">

  <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

         <div class="pull-right">
            @can('warehouse_edit')
                <a href="{{ route('admin.warehouses.edit',[$warehouse->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
          </div>   

        <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.warehouses.fields.name')</th>
                            <td field-key='name'>{{ $warehouse->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.warehouses.fields.address')</th>
                            <td field-key='address'>{!! clean($warehouse->address) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.warehouses.fields.description')</th>
                            <td field-key='description'>{!! clean($warehouse->description) !!}</td>
                        </tr>
                    </table>

    </div>    

    @if ( 'active' === $tabs['products_active'] )
    <div role="tabpanel" class="tab-pane {{$tabs['products_active']}}" id="products">
        @include('admin.products.records-display')
    </div>
    @endif
    </div>
    @if ( 'active' === $tabs['purchase_orders_active'] )
    <div role="tabpanel" class="tab-pane {{$tabs['purchase_orders_active']}}" id="purchase_orders">
        @include('admin.purchase_orders.records-display')
    </div>
    @endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['products_active'] )
        @include('admin.products.records-display-scripts', [ 'type' => 'ware_house', 'type_id' => $warehouse->id ])
     @endif
    @if ( 'active' === $tabs['purchase_orders_active'] )
        @include('admin.purchase_orders.records-display-scripts', [ 'type' => 'warehouse', 'type_id' => $warehouse->id ])
     @endif
@endsection

