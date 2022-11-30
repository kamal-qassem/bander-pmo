@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.content-pages.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ ucfirst($content_page->title) }} <span style="float:right;"><i class="fa fa-clock-o"> {{$content_page->created_at->diffForHumans()}}</i></span>
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                
                @php
                  $cols = 8;
                    if ( $content_page->category_id->count() ) {
                        $otherarticles = \App\ContentPage::where('id', '!=', $content_page->id)->whereHas("category_id", function ($query) use( $content_page ) {
                        $query->where('id', $content_page->category_id->pluck('id')->toArray());
                        })->limit(10)->orderBy('id', 'desc');
                        if ( $otherarticles->count() == 0 ) {
                            $cols = 12;
                        }
                    } else {
                        $cols = 12;
                    }
                  @endphp
                  <div class="col-md-{{$cols}}">
                     @if($content_page->featured_image && file_exists(public_path() . '/thumb/' . $content_page->featured_image))
                     <div class="text-center col-md-3 well-note"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $content_page->featured_image) }}" style="width:100px;height:100px;"/></div>
                     @endif
                       <div class="col-md-9 well-note">
                      @if( $content_page->page_text )
                          {!! clean($content_page->page_text) !!}
                      @endif
                      </div>
                     
                     <small class="form-text text-muted">
                        @if($content_page->category_id->count())
                        <div class="col-md-4">                        
                        <i class="fa fa-folder"></i><span>@lang('custom.articles.category')<br/></span>
                        @foreach ($content_page->category_id as $singleCategoryId)
                            <a href="{{route('admin.pages.search', ['type' => 'categories', 'catid' => $singleCategoryId->id] )}}"><span class="label label-info label-many">{{ $singleCategoryId->title }}</span></a>
                        @endforeach                        
                        </div>
                        @endif                       
                        
                      

                        @if($content_page->tag_id->count())
                        <div class="col-md-4">                        
                            <i class="fa fa-tags"></i><span>@lang('custom.articles.tag')<br/></span>
                            @foreach ($content_page->tag_id as $singleCategoryId)
                                <a href="{{route('admin.pages.search', ['type' => 'tags', 'catid' => $singleCategoryId->id] )}}"><span class="label label-info label-many">{{ $singleCategoryId->title }}</span></a>
                            @endforeach                        
                        </div>
                        @endif

                    </small>
                  </div>
                  @if( $cols == 8 )
                  <div class="col-md-4">
                     <h4>@lang('custom.articles.related-pages')</h4>
                     <hr>
                     <ul>
                        @foreach( $otherarticles->get() as $oarticle)
                        <li>
                          <a style="font-weight: bold;" href="{{route('admin.content_pages.show', $oarticle->id)}}">{{$oarticle->title}}</a> 

                        <p>
                            @if( $oarticle->page_text )
                                {!! clean($oarticle->page_text) !!}
                            @endif
                        </p>

                        <small class="form-text text-muted">{{$oarticle->created_at->diffForHumans()}}</small>                   
                        </li>
                        <hr>
                        @endforeach
                    </ul>
                     
                  </div>
                  @endif
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.content_pages.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent

    @include('admin.common.standard-ckeditor')
    
@stop
