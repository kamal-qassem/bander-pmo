<script>
    @can('contact_delete_multi')
       @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.contacts.mass_destroy') }}';
        @endif
    @endcan
    $(document).ready(function () {
        @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_contacts.index', [ 'type' => $type, 'type_id' => $type_id ] ) !!}?show_deleted={{ request('show_deleted') }}';
        @else
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.contacts.index') !!}?show_deleted={{ request('show_deleted') }}';
        @endif
        window.dtDefaultOptionsNew.columns = [@can('contact_delete_multi')
             @if ( request('show_deleted') != 1 )
                {data: 'massDelete', name: 'id', searchable: false, sortable: false},
              @endif  
            @endcan
            {data: 'contacts.name', name: 'contacts.name'},
            
            {data: 'contact_type.title', name: 'contact_type.title', sortable: false},
           
            {data: 'email', name: 'email'},
            {data: 'fulladdress', name: 'fulladdress'},
            
            
            {data: 'actions', name: 'actions', searchable: false, sortable: false}
        ];
        processAjaxTablesNew();
    });
</script>
<script src="{{ url('adminlte/plugins/ckeditor/ckeditor.js') }}"></script>
@include('admin.contacts.mail.scripts')