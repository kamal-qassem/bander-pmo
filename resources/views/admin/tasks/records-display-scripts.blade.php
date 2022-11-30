<script>
        @can('task_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.tasks.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_tasks.index', [ 'type' => $type, 'type_id' => $type_id ] ) !!}?show_deleted={{ request('show_deleted') }}';
        @else
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.tasks.index') !!}?show_deleted={{ request('show_deleted') }}';
        @endif
            window.dtDefaultOptionsNew.columns = [@can('task_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                    @endcan
                
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'status.name', name: 'status.name'},
                {data: 'start_date', name: 'start_date'},
                
                {data: 'due_date', name: 'due_date'},
                {data: 'user.name', name: 'user.name'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>