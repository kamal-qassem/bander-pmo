<script>
        $(document).ready(function () {
            @can('role_delete_multi')
                window.route_mass_crud_entries_destroy = '{{ route('admin.roles.mass_destroy') }}';
            @endcan
            window.dtDefaultOptions.buttons = [];
        });
</script>