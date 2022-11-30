<script>
        @can('asset_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.assets.mass_destroy') }}';
        @endcan

</script>