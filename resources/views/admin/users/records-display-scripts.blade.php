<script>
        @can('user_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.users.mass_destroy') }}';
        @endcan
        $(document).ready(function () {
         @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_users.index', [ 'type' => $type, 'type_id' => $type_id ] ) !!}?show_deleted={{ request('show_deleted') }}';
        @else
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.users.index') !!}?show_deleted={{ request('show_deleted') }}';
        @endif
            
            window.dtDefaultOptionsNew.columns = [@can('user_delete_multi')
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endcan{data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'role.title', name: 'role.title'},
                {data: 'status', name: 'status'},                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>


  