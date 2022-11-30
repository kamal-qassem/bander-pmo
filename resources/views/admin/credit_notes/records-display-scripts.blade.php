<script>
    @can('credit_note_delete_multi')
        @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.credit_notes.mass_destroy') }}'; @endif
    @endcan
    $(document).ready(function () {
        @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_credit_notes.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}?show_deleted={{ request('show_deleted') }}';
        @else
        window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.credit_notes.index') !!}?show_deleted={{ request('show_deleted') }}';
        @endif

        window.dtDefaultOptionsNew.columns = [@can('credit_note_delete_multi')
            @if ( request('show_deleted') != 1 )
                {data: 'massDelete', name: 'id', searchable: false, sortable: false},
            @endif
            @endcan
            {data: 'invoice_no', name: 'invoice_no'},
            {data: 'invoice_date', name: 'invoice_date'},
            {data: 'customer.first_name', name: 'customer.first_name'},
            {data: 'credit_status', name: 'credit_status'},
            
            {data: 'title', name: 'title'},
            
            {data: 'amount', name: 'amount'},
            
            {data: 'actions', name: 'actions', searchable: false, sortable: false}
        ];
        processAjaxTablesNew();
    });
</script>