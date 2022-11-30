<script>
    @can('income_delete_multi')
        window.route_mass_crud_entries_destroy = '{{ route('admin.incomes.mass_destroy') }}';
    @endcan
    $(document).ready(function () {
        @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_incomes.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}';
        @else
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.incomes.index') !!}';
        @endif
        window.dtDefaultOptionsNew.columns = [@can('income_delete_multi')
                {data: 'massDelete', name: 'id', searchable: false, sortable: false},
            @endcan   
            {data: 'account.name', name: 'account.name'},
            {data: 'income_category.name', name: 'income_category.name'},
            {data: 'entry_date', name: 'entry_date'},
            {data: 'amount', name: 'amount'},
            {data: 'payer.first_name', name: 'payer.first_name'},
            {data: 'pay_method.name', name: 'pay_method.name'},
            
            {data: 'actions', name: 'actions', searchable: false, sortable: false}
        ];
        processAjaxTablesNew();
    });
</script>