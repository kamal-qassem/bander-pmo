@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('content')
<h3 class="page-title">@lang('global.recurring-invoices.title')</h3>

   @can('recurring_invoice_create')
   <p>
   <a href="{{ route('admin.recurring_invoices.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
   @include('recurringinvoices::admin.recurring_invoices.canvas.canvas')
  </p>    
   @include('recurringinvoices::admin.recurring_invoices.filters')
   @endcan    

<p>
<ul class="list-inline">
   <li><a href="{{ route('admin.recurring_invoices.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
      <span class="badge">  
      
      {{\Modules\RecurringInvoices\Entities\RecurringInvoice::count()}}
      
      </span>
      </a>
   </li>
   @can('recurring_invoice_delete')
   
   |
   <li><a href="{{ route('admin.recurring_invoices.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
      <span class="badge">
      {{\Modules\RecurringInvoices\Entities\RecurringInvoice::onlyTrashed()->count()}}
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
      @include('recurringinvoices::admin.recurring_invoices.records-display')
   </div>
</div>
@stop
@section('javascript') 
  @include('recurringinvoices::admin.recurring_invoices.records-display-scripts')
@endsection