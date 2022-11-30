<script>
   @can('purchase_order_delete_multi')
       @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.purchase_orders.mass_destroy') }}'; @endif
   @endcan
   $(document).ready(function () {
       @if ( ! empty( $type ) && ! empty( $type_id ) )
        window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_purchase_orders.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}?show_deleted={{ request('show_deleted') }}';
       @else
        window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.purchase_orders.index') !!}?show_deleted={{ request('show_deleted') }}';
       @endif
       window.dtDefaultOptionsNew.columns = [@can('purchase_order_delete_multi')
           @if ( request('show_deleted') != 1 )
               {data: 'massDelete', name: 'id', searchable: false, sortable: false},
           @endif
           @endcan
           {data: 'invoice_no', name: 'invoice_no'},
           {data: 'customer.first_name', name: 'customer.first_name'},
           {data: 'subject', name: 'subject'},
           {data: 'status', name: 'status'},
           
           
           {data: 'order_date', name: 'order_date'},
           {data: 'order_due_date', name: 'order_due_date'},
           {data: 'currency.name', name: 'currency.name'},
           {data: 'paymentstatus', name: 'paymentstatus'},
           {data: 'amount', name: 'amount'},
           
           {data: 'actions', name: 'actions', searchable: false, sortable: false}
       ];
       processAjaxTablesNew();
   });
</script>