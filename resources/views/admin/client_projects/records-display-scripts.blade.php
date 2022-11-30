<script>
    @can('client_project_delete_multi')
        @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.client_projects.mass_destroy') }}'; @endif
    @endcan
    $(document).ready(function () {
        @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_client_projects.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}?show_deleted={{ request('show_deleted') }}';
        @else
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.client_projects.index') !!}?show_deleted={{ request('show_deleted') }}';
        @endif
        window.dtDefaultOptionsNew.columns = [@can('client_project_delete_multi')
            @if ( request('show_deleted') != 1 )
                {data: 'massDelete', name: 'id', searchable: false, sortable: false},
            @endif
            @endcan{data: 'title', name: 'title'},
            {data: 'client.first_name', name: 'client.first_name'},
            {data: 'assigned_to.name', name: 'assigned_to.name', sortable: false},
            {data: 'start_date', name: 'start_date'},
            {data: 'due_date', name: 'due_date'},
            {data: 'priority', name: 'priority'},
            {data: 'status.name', name: 'status.name'}, 
            {data: 'currency.name', name: 'currency.name'},           
            {data: 'actions', name: 'actions', searchable: false, sortable: false}
        ];
        processAjaxTablesNew();
    });
</script>