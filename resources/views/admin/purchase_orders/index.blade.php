@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('content')
<h3 class="page-title">@lang('global.purchase-orders.title')</h3>


   @can('purchase_order_create')
   <p>
   <a href="{{ route('admin.purchase_orders.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
   @include('admin.purchase_orders.canvas.canvas')
 @include('admin.purchase_orders.filters')    
   </p>
   @endcan

<p>
<ul class="list-inline">
   <li><a href="{{ route('admin.purchase_orders.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
      <span class="badge">  
      
      {{\App\PurchaseOrder::count()}}
       
      </span>
      </a>
   </li>
   @can('purchase_order_delete')
   |
   <li><a href="{{ route('admin.purchase_orders.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
      <span class="badge">
      
      {{\App\PurchaseOrder::onlyTrashed()->count()}} 
     
      </span>

      </a>
   </li>
   @endcan
</ul>
</p>
<div class="panel panel-default">
   <div class="panel-heading">
      @lang('global.app_list')
   </div>
   <div class="panel-body table-responsive">
      @include('admin.purchase_orders.records-display')
   </div>
</div>
@stop
@section('javascript') 
  @include('admin.purchase_orders.records-display-scripts')
@endsection