<script>

        $(document).ready(function () {
            window.dtDefaultOptions.buttons = [];
            window.dtDefaultOptions.ajax = '{!! route('admin.home.dashboard-widgets', $role_id) !!}';
            window.dtDefaultOptions.columns = [
                {data: 'title', name: 'title'},
                {data: 'status', name: 'status'},
                {data: 'role.title', name: 'roles.title', searchable: false},
                
                {data: 'type', name: 'type'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>