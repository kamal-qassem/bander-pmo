@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.payments.title')</h3>
    
    <div class="panel-body packages">
        <div class="row">
         
          <div class="col-md-12 text-center"> 
            <i class="fa fa-thumbs-down fa-5x" aria-hidden="true"></i><h1>@lang('global.payments.failed') </h1>
          </div>

          <div class="col-md-12 text-center"> 
          <a href="{{ route('admin.orders.index') }}" class="btn btn-success">@lang('orders::global.orders.myorders')</a>
          &nbsp;|&nbsp;
          <a href="{{ route('admin.orders.create') }}" class="btn btn-info">@lang('orders::global.orders.place-new-order')</a>
           </div>
       
        </div>
   
               
    </div>
@stop