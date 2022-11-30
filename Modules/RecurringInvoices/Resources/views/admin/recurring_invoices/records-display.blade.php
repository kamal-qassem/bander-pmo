<table class="table table-bordered table-striped ajaxTable @can('recurring_invoice_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
   <thead>
      <tr>
         @can('recurring_invoice_delete_multi')
         @if ( request('show_deleted') != 1 )
         <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
         @endif
         @endcan
         <th>@lang('global.recurring-invoices.fields.invoice-no')</th>
         <th>@lang('global.recurring-invoices.fields.customer')</th>
         <th>@lang('global.recurring-invoices.fields.currency')</th>
         <th>@lang('global.recurring-invoices.fields.title')</th>
         <th>@lang('global.recurring-invoices.fields.status')</th>
         <th>@lang('global.recurring-invoices.fields.invoice-date')</th>
         <th>@lang('global.recurring-invoices.fields.invoice-due-date')</th>
         <th>@lang('global.recurring-invoices.fields.recurring-period')</th>
         <th>@lang('global.recurring-invoices.fields.amount')</th>
         <th>@lang('global.recurring-invoices.fields.paymentstatus')</th>
         @if( request('show_deleted') == 1 )
         <th>&nbsp;</th>
         @else
         <th>&nbsp;</th>
         @endif
      </tr>
   </thead>
</table>