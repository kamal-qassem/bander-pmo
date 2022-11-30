<div class="col-md-{{ ! empty($widget->columns) ? $widget->columns : 2 }}">
<div class="panel panel-default">
    <div class="panel-heading">@lang('others.dashboard.recent-orders')</div>

    <div class="panel-body table-responsive">
        <table class="table table-bordered table-striped ajaxTable">
            <thead>
            <tr>
                
                <th> @lang('orders::global.orders.fields.order-id')</th> 
                <th> @lang('orders::global.orders.fields.customer')</th> 
                <th> @lang('orders::global.orders.fields.status')</th> 
                <th> @lang('orders::global.orders.fields.price')</th> 
                <th> @lang('orders::global.orders.fields.billing-cycle')</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            @foreach($orders as $order)
                <tr>
                   
                    <td>{{ $order->id }} </td> 
                    <td>{{ $order->customer->first_name }} </td> 
                    <td>{{ $order->status }} </td> 
                    <td>{{ digiCurrency($order->price,$order->currency_id) }} </td> 
                    <td>{{ $order->billing_cycle->title ?? trans('orders::global.orders.onetime') }} </td> 
                    
                    <td>

                        @can('order_view')
                        <a href="{{ route('admin.orders.show',[$order->slug]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                        @endcan

                        @can('order_edit')
                        <a href="{{ route('admin.orders.edit',[$order->slug]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                        @endcan

                        @can('order_delete')
{!! Form::open(array(
                            'style' => 'display: inline-block;',
                            'method' => 'DELETE',
                            'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                            'route' => ['admin.orders.destroy', $order->id])) !!}
                        {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                        {!! Form::close() !!}
                        @endcan
                    
</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
</div>