@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.orders.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            
            <div class="col-xs-12 form-group">
                    @include('admin.common.cart.add-products-cart')
            </div>            
        </div>
    </div>

    {!! Form::close() !!}
@stop

