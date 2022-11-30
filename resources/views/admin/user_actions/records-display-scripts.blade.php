<script>
        
        window.route_mass_crud_entries_destroy = '{{ route('admin.user_actions.mass_destroy') }}';
        $(document).ready(function () {

            @if ( ! empty( $type ) && ! empty( $type_id ) )
                window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_user_actions.index', [ 'type' => $type, 'type_id' => $type_id ] ) !!}?show_deleted={{ request('show_deleted') }}';
            @else
                window.dtDefaultOptionsNew.ajax = '{!! route('admin.user_actions.index') !!}?show_deleted={{ request('show_deleted') }}';
            @endif

            window.dtDefaultOptionsNew.columns = [
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'user.name', name: 'user.name'},
                    {data: 'action', name: 'action'},
                    {data: 'action_model', name: 'action_model'},
                    {data: 'action_id', name: 'action_id'},
                    {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
</script>