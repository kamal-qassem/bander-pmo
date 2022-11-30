<script>
        @can('content_page_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.content_pages.mass_destroy') }}';
        @endcan

        $(document).ready(function () {
            
            @if ( ! empty( $type ) && ! empty( $type_id ) )
                window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_content_pages.index', ["type" => $type, "type_id" => $type_id] ) !!}';
            @else
            window.dtDefaultOptions.ajax = '{!! route('admin.content_pages.index') !!}';
            @endif
            window.dtDefaultOptionsNew.columns = [@can('content_page_delete_multi')
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endcan
                {data: 'title', name: 'title'},
                {data: 'created_at', name: 'created_at'},
                @if( isAdmin() )
                {data: 'category_id.title', name: 'category_id.title',sortable: false},
                {data: 'tag_id.title', name: 'tag_id.title',sortable: false},
                
                {data: 'featured_image', name: 'featured_image',sortable: false},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
                @endif
            ];
            processAjaxTablesNew();
        });

</script>