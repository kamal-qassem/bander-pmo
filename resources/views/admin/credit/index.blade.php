@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.articles.title')</h3>
    @can('article_create')
    <p>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
            @if( $catid > 0 )
                @if( 'categories' === $type )
                    <?php
                    $category = App\ContentCategory::find($catid);
                    ?>
                    @if( $category )
                    <b>(@lang('custom.articles.category') {{$category->title}})</b>
                    @endif
                @endif
                @if( 'tags' === $type )
                    <?php
                    $tag = App\ContentTag::find($catid);
                    ?>
                    @if( $tag )
                    <b>(@lang('custom.articles.tag') {{$tag->title}})</b>
                    @endif
                @endif
            @endif
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('article_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('article_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.articles.fields.title')</th>
                        @if( isAdmin() )
                        <th>@lang('global.articles.fields.category-id')</th>
                        <th>@lang('global.articles.fields.tag-id')</th>

                        <th>@lang('global.articles.fields.featured-image')</th>
                        <th>&nbsp;</th>
                        @endif

                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('article_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.articles.mass_destroy') }}';
        @endcan
        $(document).ready(function () {
            
            @if( $catid > 0 )
                window.dtDefaultOptions.ajax = '{!! route('admin.articles.search', ["type" => $type, "catid" => $catid] ) !!}';
            @else
            window.dtDefaultOptions.ajax = '{!! route('admin.articles.index') !!}';
            @endif
            window.dtDefaultOptions.columns = [@can('article_delete_multi')
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endcan{data: 'title', name: 'title'},
                @if( isAdmin() )
                {data: 'category_id.title', name: 'category_id.title',sortable: false},
                {data: 'tag_id.title', name: 'tag_id.title',sortable: false},
                //{data: 'page_text', name: 'page_text'},
                /*{data: 'excerpt', name: 'excerpt'},*/
                {data: 'featured_image', name: 'featured_image',sortable: false},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
                @endif
            ];
            processAjaxTables();
        });
    </script>
@endsection