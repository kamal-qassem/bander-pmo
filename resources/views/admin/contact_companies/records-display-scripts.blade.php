<script>
    @can('contact_company_delete_multi')
        window.route_mass_crud_entries_destroy = '{{ route('admin.contact_companies.mass_destroy') }}';
    @endcan
    $(document).ready(function () {
        window.dtDefaultOptions.buttons = [];
        @if ( ! empty( $type ) && ! empty( $type_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_companies.index', [ 'type' => $type, 'type_id' => $type_id ] ) !!}?show_deleted={{ request('show_deleted') }}';
        @else
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.contact_companies.index') !!}';
        @endif
        window.dtDefaultOptionsNew.columns = [@can('contact_company_delete_multi')
        @if ( request('show_deleted') != 1 )
           {data: 'massDelete', name: 'id', searchable: false, sortable: false},
          @endif 
            @endcan
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'address', name: 'address'},
            {data: 'country.title', name: 'country.title'},
            {data: 'website', name: 'website'},
            
            {data: 'actions', name: 'actions', searchable: false, sortable: false}
        ];
        processAjaxTablesNew();
    });
</script>