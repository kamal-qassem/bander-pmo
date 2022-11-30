@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title-new-order')</h3>
    <div class="panel panel-default">        
        <div class="panel-body">            
            <div class="col-xs-12">
                <div class="form-group">
                    @include('orders::admin.orders.cart.add-products-cart')
                </div>
            </div>            
        </div>
    </div>
@stop