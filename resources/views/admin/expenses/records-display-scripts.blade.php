<script>
        @can('expense_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.expenses.mass_destroy') }}';
        @endcan
        $(document).ready(function () {
          @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptions.ajax = '{!! route('admin.list_expenses.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}';
        @else
            window.dtDefaultOptions.ajax = '{!! route('admin.expenses.index') !!}';
        @endif
            window.dtDefaultOptions.columns = [@can('expense_delete_multi')
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endcan
                {data: 'name', name: 'name'},
                {data: 'account.name', name: 'account.name'},
                {data: 'expense_category.name', name: 'expense_category.name'},
                {data: 'entry_date', name: 'entry_date'},
                {data: 'amount', name: 'amount'},
                {data: 'payee.name', name: 'payee.name'},
                {data: 'payment_method.name', name: 'payment_method.name'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>