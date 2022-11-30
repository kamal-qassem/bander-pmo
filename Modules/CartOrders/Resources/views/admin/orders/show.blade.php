@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('orders::global.orders.fields.customer')</th>
                            <td field-key='customer'>{{ $order->customer->first_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('orders::global.orders.fields.status')</th>
                            <td field-key='status'>{{ $order->status }}</td>
                        </tr>
                        <tr>
                            <th>@lang('orders::global.orders.fields.price')</th>
                            <td field-key='price'>{{ $order->price }}</td>
                        </tr>
                        <tr>
                            <th>@lang('orders::global.orders.fields.billing-cycle')</th>
                            <td field-key='billing_cycle'>{{ $order->billing_cycle->title ?? trans('orders::global.orders.onetime') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


