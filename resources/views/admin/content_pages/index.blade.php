@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.content-pages.title')</h3>
    @can('content_page_create')
    <p>
        <a href="{{ route('admin.content_pages.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
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
            @include('admin.content_pages.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.content_pages.records-display-scripts')
@endsection