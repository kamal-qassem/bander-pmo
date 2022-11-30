<script>
        @can('article_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.articles.mass_destroy') }}';
        @endcan
        $(document).ready(function () {
            
            @if ( ! empty( $type ) && ! empty( $type_id ) )
                window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_articles.index', ["type" => $type, "type_id" => $type_id] ) !!}';
            @else
            window.dtDefaultOptions.ajax = '{!! route('admin.articles.index') !!}';
            @endif
            window.dtDefaultOptionsNew.columns = [@can('article_delete_multi')
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endcan{data: 'title', name: 'title'},
                @if( isAdmin() )
                {data: 'category_id.title', name: 'category_id.title'},
                {data: 'tag_id.title', name: 'tag_id.title'},
                {data: 'excerpt', name: 'excerpt'},
                {data: 'featured_image', name: 'featured_image'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
                @endif
            ];
            processAjaxTablesNew();
        });
</script>