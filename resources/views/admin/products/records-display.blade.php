<table class="table table-bordered table-striped ajaxTable @can('product_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        
                @can('product_delete_multi')
                    @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                @endcan
                        <th>@lang('global.products.fields.name')</th>
                        <th>@lang('global.products.fields.product-code')</th>
                        <th>@lang('global.products.fields.actual-price')</th>
                        <th>@lang('global.products.fields.sale-price')</th>
                        @if( isPluginActive('productcategory') )
                        <th>@lang('global.products.fields.category')</th>
                        @endif
                        
                        <th>@lang('global.products.fields.stock-quantity')</th>
                        <th>@lang('global.products.fields.thumbnail')</th>
                         @if( isPluginActive('productbrand') )
                        <th>@lang('global.products.fields.brand')</th>
                        @endif
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
            </table>