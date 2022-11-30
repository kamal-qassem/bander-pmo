<table class="table table-bordered table-striped ajaxTable @can('purchase_order_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
   <thead>
      <tr>
         @can('purchase_order_delete_multi')
         @if ( request('show_deleted') != 1 )
         <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
         @endif
         @endcan
         <th>@lang('global.purchase-orders.fields.invoice-no')</th>
         <th>@lang('global.purchase-orders.fields.customer')</th>
         <th>@lang('global.purchase-orders.fields.subject')</th>
         <th>@lang('global.purchase-orders.fields.status')</th>
         <th>@lang('global.purchase-orders.fields.order-date')</th>
         <th>@lang('global.purchase-orders.fields.order-due-date')</th>
         <th>@lang('global.purchase-orders.fields.currency')</th>
         <th>@lang('global.purchase-orders.fields.paymentstatus')</th>
         <th>@lang('global.purchase-orders.fields.amount')</th>
         @if( request('show_deleted') == 1 )
         <th>&nbsp;</th>
         @else
         <th>&nbsp;</th>
         @endif
      </tr>
   </thead>
</table>