@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $content_tag->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.content_tags.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'product_category_edit',
            ], 
            [
                'route' => 'admin.content_tags.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'content_tag_delete',
            ],
        ],
        'record' => $content_tag,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        <?php
        $tabs = [
            'details_active' => 'active',
            'content_pages_active' => '',
            'articles_active' => '',
        ];
        
        if ( ! empty( $list ) ) {
            foreach ($tabs as $key => $value) {
                $tabs[ $key ] = '';
                if ( substr( $key, 0, -7) == $list ) {
                    $tabs[ $key ] = 'active';
                }
            }
        }
        ?>
        <div class="panel-body table-responsive">
            <!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>    
<li role="presentation" class="{{$tabs['content_pages_active']}}"><a href="{{route('admin.content_tags.show', [ 'tag_id' => $content_tag->id, 'list' => 'content_pages' ])}}" title="@lang('others.canvas.pages')">@lang('others.canvas.pages')</a></li>
<li role="presentation" class="{{$tabs['articles_active']}}"><a href="{{route('admin.content_tags.show', [ 'tag_id' => $content_tag->id, 'list' => 'articles' ])}}" title="@lang('global.articles.title')">@lang('global.articles.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

<div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

         <div class="pull-right">
            @can('content_tag_edit')
                <a href="{{ route('admin.content_tags.edit',[$content_tag->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
         </div> 

        <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.content-tags.fields.title')</th>
                            <td field-key='title'>{{ $content_tag->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.content-tags.fields.slug')</th>
                            <td field-key='slug'>{{ $content_tag->slug }}</td>
                        </tr>
                    </table>

             </div>    
     @if ( 'active' === $tabs['content_pages_active'] )
        <div role="tabpanel" class="tab-pane {{$tabs['content_pages_active']}}" id="content_pages">
            @include('admin.content_pages.records-display')
        </div>
    @endif
    @if ( 'active' === $tabs['articles_active'] )
        <div role="tabpanel" class="tab-pane {{$tabs['articles_active']}}" id="articles">
            @include('admin.articles.records-display')
        </div>
    @endif

</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.content_tags.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['content_pages_active'] )
        @include('admin.content_pages.records-display-scripts', [ 'type' => 'tags', 'type_id' => $content_tag->id ])
     @endif
     @if ( 'active' === $tabs['articles_active'] )
        @include('admin.articles.records-display-scripts', [ 'type' => 'tags', 'type_id' => $content_tag->id ])
     @endif
@endsection

