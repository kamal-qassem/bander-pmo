@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.payments.title')</h3>
    
    <div class="panel-body packages">
        <div class="row">
         
          @include('orders::admin.orders.order-payment-details', compact('record'))

          {!! Form::model($record, ['method' => 'POST', 'route' => ['admin.orders.process-payment-now', $record->slug, 'order']]) !!}
            @include('orders::admin.orders.cart.payment-gateways', array('order' => $record))
          {!! Form::submit(trans('orders::global.orders.pay-now'), ['class' => 'btn btn-info', 'name' => 'btnsavemanage', 'value' => 'savemanage']) !!}

          &nbsp;|&nbsp;
          <a href="{{ route('admin.orders.index') }}" class="btn btn-success">@lang('orders::global.orders.myorders')</a>
          &nbsp;|&nbsp;
          <a href="{{ route('admin.orders.create') }}" class="btn btn-info">@lang('orders::global.orders.place-new-order')</a>
          {!! Form::close() !!}


          
          
       
        </div>
   
               
    </div>
@stop