@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $brand->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.brands.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'brand_edit',
            ], 
            [
                'route' => 'admin.brands.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'brand_delete',
            ],
        ],
        'record' => $brand,
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
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

 <li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>   
    
<li role="presentation" class="{{$tabs['products_active']}}"><a href="{{route('admin.brands.show', [ 'brand_id' => $brand->id, 'list' => 'products' ])}}" title= "@lang('others.canvas.products')">@lang('others.canvas.products')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

    <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">
            <div class="pull-right">
            @can('brand_edit')
                <a href="{{ route('admin.brands.edit',[$brand->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
            </div>     
       <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.brands.fields.title')</th>
                            <td field-key='title'>{{ $brand->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.brands.fields.icon')</th>
                            <td field-key='icon'>
                                @if($brand->icon && file_exists(public_path() . '/thumb/' . $brand->icon))
                                <a href="{{ asset(env('UPLOAD_PATH').'/' . $brand->icon) }}" target="_blank"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $brand->icon) }}"/></a>@endif</td>
                        </tr>
                        <tr>
                            <th>@lang('global.brands.fields.status')</th>
                            <td field-key='status'>{{ $brand->status }}</td>
                        </tr>
                    </table>
                </div>

        @if ( 'active' === $tabs['products_active'] )
        <div role="tabpanel" class="tab-pane {{$tabs['products_active']}}" id="products">
            @include('admin.products.records-display')
        </div>
        @endif
</div>
    </div>
        <p>&nbsp;</p>

            <a href="{{ route('admin.brands.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript') 
    @if ( 'active' === $tabs['products_active'] )
        @include('admin.products.records-display-scripts', [ 'type' => 'brand', 'type_id' => $brand->id ])

     @endif
@endsection
