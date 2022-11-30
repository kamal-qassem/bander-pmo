<script>
        @can('internal_notification_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.internal_notifications.mass_destroy') }}';
        @endcan

</script>