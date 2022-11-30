<script>
        @can('product_delete_multi')
           @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.products.mass_destroy') }}';
           @endif
        @endcan
        $(document).ready(function () {
            
            @if ( ! empty( $type ) && ! empty( $type_id ) )
                window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_products.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}?show_deleted={{ request('show_deleted') }}';
            @else
                window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.products.index') !!}?show_deleted={{ request('show_deleted') }}';
            @endif
            window.dtDefaultOptionsNew.columns = [@can('product_delete_multi')
            @if ( request('show_deleted') != 1 )
                {data: 'massDelete', name: 'id', searchable: false, sortable: false},
            @endif
                @endcan{data: 'products.name', name: 'products.name'},
                {data: 'product_code', name: 'product_code'},
                {data: 'actual_price', name: 'actual_price'},
                {data: 'sale_price', name: 'sale_price'},
                @if( isPluginActive('productcategory') )
                {data: 'category.name', name: 'category.name', sortable: false},
                @endif
                
                {data: 'stock_quantity', name: 'stock_quantity'},
                {data: 'thumbnail', name: 'thumbnail', sortable: false},
                @if( isPluginActive('productbrand') )
                {data: 'brand.title', name: 'brand.title'},
                @endif
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>