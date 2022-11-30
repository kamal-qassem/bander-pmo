<script>
    @can('contact_document_delete_multi')
        @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.contact_documents.mass_destroy') }}'; @endif
    @endcan
    $(document).ready(function () {
        window.dtDefaultOptions.buttons = [];
        @if ( ! empty( $contact_id ) )
            window.dtDefaultOptions.ajax = '{!! route('admin.customer_contact_documents.index', $contact_id) !!}?show_deleted={{ request('show_deleted') }}';
        @else
            window.dtDefaultOptions.ajax = '{!! route('admin.contact_documents.index') !!}?show_deleted={{ request('show_deleted') }}';
        @endif
        window.dtDefaultOptions.columns = [@can('contact_document_delete_multi')
            @if ( request('show_deleted') != 1 )
                {data: 'massDelete', name: 'id', searchable: false, sortable: false},
            @endif
            @endcan{data: 'name', name: 'name'},
            {data: 'attachment', name: 'attachment', searchable: false, sortable: false},
            {data: 'contact.name', name: 'contact.name'},
            
            {data: 'actions', name: 'actions', searchable: false, sortable: false}
        ];
        processAjaxTables();
    });
</script>