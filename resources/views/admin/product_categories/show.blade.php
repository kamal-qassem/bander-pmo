@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{$product_category->name}}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.product_categories.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'product_category_edit',
            ], 
            [
                'route' => 'admin.product_categories.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'product_category_delete',
            ],
        ],
        'record' => $product_category,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
        <?php
        $tabs = [
            'details_active' => 'active',
            'products_active' => '',
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
            <div class="row">
                <div class="col-md-6">
              
                </div>
            </div>
<ul class="nav nav-tabs" role="tablist">
 <li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>   
<li role="presentation" class="{{$tabs['products_active']}}"><a href="{{route('admin.product_categories.show', [ 'product_category_id' => $product_category->id, 'list' => 'products' ])}}" title= "@lang('others.canvas.products')" >@lang('others.canvas.products')</a></li>
</ul>
    
<div class="tab-content">
    
   <div role="tabpanel" class="tab-pane  {{$tabs['details_active']}}" id="details">
     <div class="pull-right">
            @can('product_category_edit')
                <a href="{{ route('admin.product_categories.edit',[$product_category->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
            </div>   


                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.product-categories.fields.name')</th>
                            <td field-key='name'>{{ $product_category->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.product-categories.fields.description')</th>
                            <td field-key='description'>{!! clean($product_category->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.product-categories.fields.photo')</th>
                            <td field-key='photo'>
                                @if (!empty($product_category->photo) && file_exists(public_path() . '/thumb/' . $product_category->photo) )
                                <a href="{{ route('admin.home.media-file-download', [ 'model' => 'ProductCategory', 'field' => 'photo', 'record_id' => $product_category->id ]) }}" ><img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product_category->photo) }}"/></a>@endif</td>
                        </tr>
                    </table>
                </div>
                
            <!-- Nav tabs -->

@if ( 'active' === $tabs['products_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['products_active']}}" id="products">
    @include('admin.products.records-display')
</div>
@endif

    

</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.product_categories.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['products_active'] )
        @include('admin.products.records-display-scripts', [ 'type' => 'product_category', 'type_id' => $product_category->id ])
     @endif
@endsection


