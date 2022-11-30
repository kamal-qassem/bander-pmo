@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $product->name }}</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
              @if( Gate::allows('product_edit') || Gate::allows('product_delete'))
            <div class="pull-right">   
                @if( Gate::allows('product_edit') )
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('product_delete'))
                    @include('admin.common.delete-link', ['record' => $product, 'routeName' => 'admin.products.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.products.fields.name')</th>
                            <td field-key='name'>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.product-code')</th>
                            <td field-key='product_code'>{{ $product->product_code }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.actual-price')</th>
                            <td field-key='actual_price'>{{ $product->actual_price }} - {{getDefaultCurrency('code')}} (@lang('custom.common.base-currency'))</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.sale-price')</th>
                            <td field-key='sale_price'>{{ $product->sale_price }} - {{getDefaultCurrency('code')}} (@lang('custom.common.base-currency'))</td>
                        </tr>
						<?php
						$prices = ! empty( $product->prices ) ? json_decode( $product->prices, true ) : array();
						
						$currencies = \App\Currency::where('status', '=', 'Active')->get()->pluck('code', 'id');
						$index = 0;
						$prices = ! empty( $product->prices ) ? json_decode( $product->prices, true ) : array();
						foreach ($currencies as $key => $value) {
							if ( $value == getDefaultCurrency('code') ) {
								continue;
							}
							
							$actual_value = ! empty( $prices['actual'][ $value ] ) ? $prices['actual'][ $value ] : '0';
							$sale_value = ! empty( $prices['sale'][ $value ] ) ? $prices['sale'][ $value ] : '0';
							?>
							<tr>
								<th>@lang('global.products.fields.actual-price')</th>
								<td field-key='actual_price'>{{ $actual_value }} - {{$value}}</td>
							</tr>
							<tr>
								<th>@lang('global.products.fields.sale-price')</th>
								<td field-key='sale_price'>{{ $sale_value }} - {{$value}}</td>
							</tr>
							<?php
						}
						?>
                        <tr>
                            <th>@lang('custom.products.fields.product_status')</th>
                            <td field-key='status'>{{ $product->product_status }}</td>
                        </tr>
                        @if( isPluginActive('productcategory') )
                        <tr>
                            <th>@lang('global.products.fields.category')</th>
                            <td field-key='category'>
                                @foreach ($product->category as $singleCategory)
                                    <span class="label label-info label-many">{{ $singleCategory->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                        @if( isPluginActive('productwarehouse') )
                        <tr>
                            <th>@lang('global.products.fields.ware-house')</th>
                            <td field-key='ware_house'>{{ $product->ware_house->name ?? '' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>@lang('global.products.fields.excerpt')</th>
                            <td field-key='excerpt'>{!! clean($product->excerpt) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.description')</th>
                            <td field-key='description'>{!! clean($product->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.stock-quantity')</th>
                            <td field-key='stock_quantity'>{{ $product->stock_quantity }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.alert-quantity')</th>
                            <td field-key='alert_quantity'>{{ $product->alert_quantity }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.image-gallery')</th>
                            <td field-key='image_gallery'> @foreach($product->getMedia('image_gallery') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.thumbnail')</th>
                            <td field-key='thumbnail'>@if($product->thumbnail && file_exists(public_path() . '/thumb/' . $product->thumbnail))<a href="{{ route('admin.home.media-file-download', [ 'model' => 'Product', 'field' => 'thumbnail', 'record_id' => $product->id ]) }}"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}"/></a>@endif</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.other-files')</th>
                            <td field-key='other_files's> @foreach($product->getMedia('other_files') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.hsn-sac-code')</th>
                            <td field-key='hsn_sac_code'>{{ $product->hsn_sac_code }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.product-size')</th>
                            <td field-key='product_size'>{{ $product->product_size }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.products.fields.product-weight')</th>
                            <td field-key='product_weight'>{{ $product->product_weight }}</td>
                        </tr>
                        @if( isPluginActive('productbrand') )
                        <tr>
                            <th>@lang('global.products.fields.brand')</th>
                            <td field-key='brand'>{{ $product->brand->title ?? '' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.products.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

@stop
