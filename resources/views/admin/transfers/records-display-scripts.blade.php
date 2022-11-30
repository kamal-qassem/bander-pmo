<script>
    @can('transfer_delete_multi')
        @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.transfers.mass_destroy') }}'; @endif
    @endcan
    $(document).ready(function () {
        @if ( ! empty( $type ) && ! empty( $type_id ) )
        window.dtDefaultOptions.ajax = '{!! route('admin.list_transfers.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}';
        @else
        window.dtDefaultOptions.ajax = '{!! route('admin.transfers.index') !!}?show_deleted={{ request('show_deleted') }}';
        @endif
        window.dtDefaultOptions.columns = [@can('transfer_delete_multi')
            @if ( request('show_deleted') != 1 )
                {data: 'massDelete', name: 'id', searchable: false, sortable: false},
            @endif
            @endcan{data: 'from.name', name: 'from.name'},
            {data: 'to.name', name: 'to.name'},
            {data: 'date', name: 'date'},
            {data: 'amount', name: 'amount'},
            {data: 'ref_no', name: 'ref_no'},
            {data: 'payment_method.name', name: 'payment_method.name'},
            
            {data: 'actions', name: 'actions', searchable: false, sortable: false}
        ];
        processAjaxTables();
    });
</script>