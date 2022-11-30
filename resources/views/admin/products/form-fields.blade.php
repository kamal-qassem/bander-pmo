<div class="row">
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('name', trans('global.products.fields.name').'*', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => trans('global.products.fields.name'), 'required' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has('name'))
            <p class="help-block">
               {{ $errors->first('name') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('product_code', trans('global.products.fields.product-code').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            {!! Form::text('product_code', old('product_code'), ['class' => 'form-control', 'placeholder' => trans('global.products.fields.product-code')]) !!}
            <p class="help-block"></p>
            @if($errors->has('product_code'))
            <p class="help-block">
               {{ $errors->first('product_code') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-3">
      <div class="form-group">
         {!! Form::label('product_status', trans('custom.products.fields.product_status').'', ['class' => 'control-label']) !!}
        
          {!! Form::select('product_status', $enum_product_status, old('product_status'), ['class' => 'form-control select2', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}

         @if($errors->has('product_status'))
         <p class="help-block">
            {{ $errors->first('product_status') }}
         </p>
         @endif
      </div>
   </div>
   @if( isPluginActive('productwarehouse') )
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('ware_house_id', trans('global.products.fields.ware-house').'', ['class' => 'control-label']) !!}
         @can('warehouse_create')
            @if( empty( $is_ajax )  || ! empty( $is_ajax ) && 'no' === $is_ajax )
               @if ( Gate::allows('warehouse_create'))
                  @if( 'button' === $addnew_type )
                  &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createwarehouse" data-selectedid="ware_house_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products.fields.ware-house') )])}}">{{ trans('global.app_add_new') }}</button>
                  @else        
                 &nbsp;<a class="modalForm" data-action="createwarehouse" data-selectedid="ware_house_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products.fields.ware-house') )])}}"><i class="fa fa-plus-square"></i></a>
                 @endif
               @endif
           @endif
        @endcan
         {!! Form::select('ware_house_id', $ware_houses, old('ware_house_id'), ['class' => 'form-control select2', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true', 'title' => trans('global.products.fields.ware-house')]) !!}
         <p class="help-block"></p>
         @if($errors->has('ware_house_id'))
         <p class="help-block">
            {{ $errors->first('ware_house_id') }}
         </p>
         @endif
      </div>
   </div>
   @endif

   <div class="col-xs-6">
      <div class="form-group">
         {!! Form::label('actual_price', trans('global.products.fields.actual-price').'*', ['class' => 'control-label form-label']) !!} - {{getDefaultCurrency('code')}} (@lang('custom.common.base-currency'))
         <div class="form-line">
            {!! Form::number('actual_price', old('actual_price'), ['class' => 'form-control amount', 'placeholder' => trans('global.products.fields.actual-price'), 'required' => '', 'min'=>'0','step'=>'0.01','id' => 'actual_price']) !!}
            <p class="help-block"></p>
            @if($errors->has('actual_price'))
            <p class="help-block">
               {{ $errors->first('actual_price') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-6">
      <div class="form-group">
         {!! Form::label('sale_price', trans('global.products.fields.sale-price').'', ['class' => 'control-label form-label']) !!} - {{getDefaultCurrency('code')}} (@lang('custom.common.base-currency'))
         <div class="form-line">
            {!! Form::number('sale_price', old('sale_price'), ['class' => 'form-control amount', 'placeholder' => trans('global.products.fields.sale-price'),'min'=>'0','step'=>'0.01', 'id' => 'sale_price']) !!}
            <p class="help-block"></p>
            @if($errors->has('sale_price'))
            <p class="help-block">
               {{ $errors->first('sale_price') }}
            </p>
            @endif
         </div>
      </div>
   </div>
</div>
<?php
   $prices = ! empty( $product->prices ) ? json_decode( $product->prices, true ) : array();
   
               $currencies = \App\Currency::where('status', '=', 'Active')->get()->pluck('code', 'id');
   $index = 0;
               foreach ($currencies as $key => $value) {
                   if ( $value == getDefaultCurrency('code') ) {
                       continue;
                   }
                   ?>
<div class="row">
   <div class="col-xs-6">
      <div class="form-group">
         {!! Form::label('prices[actual]['.$value.']', trans('global.products.fields.actual-price').'', ['class' => 'control-label form-label']) !!} - {{$value}}
         <?php
            if ( $index == 0 ) {
              echo '&nbsp;<a href="javascript:void(0);" class="btn btn-primary btn-xs fillprices" data-target="'.route('admin.products.fillprices').'">'.trans('global.products.fillprices').'</a>' . digi_get_help(trans('global.products.price-fill-instructions'));
            }
            $index++;
            $actual_value = ! empty( $prices['actual'][ $value ] ) ? $prices['actual'][ $value ] : '0';
            $sale_value = ! empty( $prices['sale'][ $value ] ) ? $prices['sale'][ $value ] : '0';
            ?>
         <div class="form-line">
            {!! Form::number('prices[actual]['.$value.']', $actual_value, ['class' => 'form-control amount', 'placeholder' => trans('global.products.fields.actual-price'), 'min'=>'0','step'=>'0.01','id' => 'prices_actual_' . $value]) !!}
            <p class="help-block"></p>
            @if($errors->has('prices[actual]['.$value.']'))
            <p class="help-block">
               {{ $errors->first('prices[actual]['.$value.']') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-6">
      <div class="form-group">
         {!! Form::label('prices[sale]['.$value.']', trans('global.products.fields.sale-price').'', ['class' => 'control-label form-label']) !!} - {{$value}}
         <div class="form-line">
            {!! Form::number('prices[sale]['.$value.']', $sale_value, ['class' => 'form-control amount','min'=>'0','step'=>'0.01', 'placeholder' => trans('global.products.fields.sale-price'), 'id' => 'prices_sale_' . $value]) !!}
            <p class="help-block"></p>
            @if($errors->has('prices[sale]['.$value.']'))
            <p class="help-block">
               {{ $errors->first('prices[sale]['.$value.']') }}
            </p>
            @endif
         </div>
      </div>
   </div>
</div>
<?php
   }
   ?>
<div class="row">
   @if( isPluginActive('productcategory') )
   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('category', trans('global.products.fields.category').'', ['class' => 'control-label']) !!}
         <button type="button" class="btn btn-primary btn-xs" id="selectbtn-category">
         {{ trans('global.app_select_all') }}
         </button>
         <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-category">
         {{ trans('global.app_deselect_all') }}
         </button>
         <?php
         $category = array();
         if ( ! empty( $product ) ) {
            $category = $product->category->pluck('id')->toArray();
         }
         ?>
         {!! Form::select('category[]', $categories, old('category') ? old('category') : $category, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-category' ,'data-live-search' => 'true','data-show-subtext' => 'true', 'title' => trans('global.products.fields.category')]) !!}
         <p class="help-block"></p>
         @if($errors->has('category'))
         <p class="help-block">
            {{ $errors->first('category') }}
         </p>
         @endif
      </div>
   </div>
   @endif

      <div class="col-xs-4">
      <div class="form-group"> 
         {!! Form::label('stock_quantity', trans('global.products.fields.stock-quantity').'', ['class' => 'control-label form-label','style'=>'padding-top:6px;']) !!}
         <div class="form-line">
            {!! Form::number('stock_quantity', old('stock_quantity'), ['class' => 'form-control', 'placeholder' => trans('global.products.fields.stock-quantity'),'min'=>'1']) !!}
            <p class="help-block"></p>
            @if($errors->has('stock_quantity'))
            <p class="help-block">
               {{ $errors->first('stock_quantity') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('alert_quantity', trans('global.products.fields.alert-quantity').'', ['class' => 'control-label form-label','style'=>'padding-top:6px;']) !!}
         <div class="form-line">
            {!! Form::number('alert_quantity', old('alert_quantity'), ['class' => 'form-control', 'placeholder' => trans('global.products.fields.alert-quantity'),'min'=>'1']) !!}
            <p class="help-block"></p>
            @if($errors->has('alert_quantity'))
            <p class="help-block">
               {{ $errors->first('alert_quantity') }}
            </p>
            @endif
         </div>
      </div>
   </div>
</div>
   
   <div class="row">
   @if( isPluginActive('productbrand') )
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('brand_id', trans('global.products.fields.brand').'', ['class' => 'control-label']) !!}
         @can('brand_create')
            @if( empty( $is_ajax )  || ! empty( $is_ajax ) && 'no' === $is_ajax )

            @if ( Gate::allows('brand_create'))
               @if( 'button' === $addnew_type )
               &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createbrand" data-selectedid="brand_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products.fields.brand') )])}}">{{ trans('global.app_add_new') }}</button>
               @else        
              &nbsp;<a class="modalForm" data-action="createbrand" data-selectedid="brand_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products.fields.brand') )])}}"><i class="fa fa-plus-square"></i></a>
              @endif
             @endif 
             @endif 
          @endcan
         {!! Form::select('brand_id', $brands, old('brand_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'title' => trans('global.products.fields.brand')]) !!}
         <p class="help-block"></p>
         @if($errors->has('brand_id'))
         <p class="help-block">
            {{ $errors->first('brand_id') }}
         </p>
         @endif
      </div>
   </div>
   @endif
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('tax_id', trans('global.products-return.fields.tax').'', ['class' => 'control-label']) !!}
        @can('tax_create')
           @if( empty( $is_ajax )  || ! empty( $is_ajax ) && 'no' === $is_ajax ) 
           @if ( Gate::allows('tax_create') )
               @if( 'button' === $addnew_type )
               &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createtax" data-selectedid="tax_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products-return.fields.tax') )])}}">{{ trans('global.app_add_new') }}</button>
               @else        
              &nbsp;<a class="modalForm" data-action="createtax" data-selectedid="tax_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products-return.fields.tax') )])}}"><i class="fa fa-plus-square"></i></a>
              @endif
            @endif
          @endif
       @endcan
         {!! Form::select('tax_id', $taxes, old('tax_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'title' => trans('global.products-return.fields.tax')]) !!}
         <p class="help-block"></p>
         @if($errors->has('tax_id'))
         <p class="help-block">
            {{ $errors->first('tax_id') }}
         </p>
         @endif
      </div>
   </div>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('discount_id', trans('global.products-return.fields.discount').'', ['class' => 'control-label']) !!}
        @can('discount_create')
           @if( empty( $is_ajax )  || ! empty( $is_ajax ) && 'no' === $is_ajax ) 

            @if ( Gate::allows('discount_create') )
               @if( 'button' === $addnew_type )
               &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="creatediscount" data-selectedid="discount_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products-return.fields.discount') )])}}">{{ trans('global.app_add_new') }}</button>
               @else        
              &nbsp;<a class="modalForm" data-action="creatediscount" data-selectedid="discount_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.products-return.fields.discount') )])}}"><i class="fa fa-plus-square"></i></a>
              @endif
            @endif
          @endif 
       @endcan
         {!! Form::select('discount_id', $discounts, old('discount_id'), ['class' => 'form-control select2', 'title' => trans('global.products-return.fields.discount')]) !!}
         <p class="help-block"></p>
         @if($errors->has('discount_id'))
         <p class="help-block">
            {{ $errors->first('discount_id') }}
         </p>
         @endif
      </div>
   </div>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('excerpt', trans('custom.products.fields.excerpt').'', ['class' => 'control-label']) !!}
         {!! Form::textarea('excerpt', old('excerpt'), ['class' => 'form-control', 'placeholder' => trans('custom.products.fields.excerpt_placeholder'), 'rows' => 1]) !!}
         <p class="help-block"></p>
         @if($errors->has('excerpt'))
         <p class="help-block">
            {{ $errors->first('excerpt') }}
         </p>
         @endif
      </div>
   </div>
</div>
<div class="row">
   <div class="col-xs-12">
      <div class="form-group">
         {!! Form::label('description', trans('global.products.fields.description').'', ['class' => 'control-label']) !!}
         {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => trans('global.products.fields.description')]) !!}
         <p class="help-block"></p>
         @if($errors->has('description'))
         <p class="help-block">
            {{ $errors->first('description') }}
         </p>
         @endif
      </div>
   </div>

   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('hsn_sac_code', trans('global.products.fields.hsn-sac-code').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            {!! Form::text('hsn_sac_code', old('hsn_sac_code'), ['class' => 'form-control', 'placeholder' => trans('global.products.fields.hsn-sac-code')]) !!}
            <p class="help-block"></p>
            @if($errors->has('hsn_sac_code'))
            <p class="help-block">
               {{ $errors->first('hsn_sac_code') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('product_size', trans('global.products.fields.product-size').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            {!! Form::text('product_size', old('product_size'), ['class' => 'form-control', 'placeholder' => trans('global.products.fields.product-size')]) !!}
            <p class="help-block"></p>
            @if($errors->has('product_size'))
            <p class="help-block">
               {{ $errors->first('product_size') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('product_weight', trans('global.products.fields.product-weight').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            {!! Form::text('product_weight', old('product_weight'), ['class' => 'form-control', 'placeholder' => trans('global.products.fields.product-weight')]) !!}
            <p class="help-block"></p>
            @if($errors->has('product_weight'))
            <p class="help-block">
               {{ $errors->first('product_weight') }}
            </p>
            @endif
         </div>
      </div>
   </div>

   @if( empty( $is_ajax ) || ( ! empty( $is_ajax ) &&  'no' == $is_ajax ) )
   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('image_gallery', trans('global.products.fields.image-gallery').'', ['class' => 'control-label']) !!}
         {!! Form::file('image_gallery[]', [
         'multiple',
         'class' => 'form-control file-upload',
         'data-url' => route('admin.media.upload'),
         'data-bucket' => 'image_gallery',
         'data-filekey' => 'image_gallery',
         'data-accept' => FILE_TYPES_GALLERY,
         'id' => 'image_gallery',
         ]) !!}
         <p class="help-block">@lang('global.products.gallery-file-types')</p>
         <div class="photo-block">
            <div class="progress-bar">&nbsp;</div>
            <div class="files-list">
               @if( ! empty( $product ) )
                  @foreach($product->getMedia('image_gallery') as $media)
                  <p class="form-group">
                     <a href="{{ route('admin.home.media-download', $media->id) }}" >{{ $media->name }} ({{ $media->size }} KB)</a>
                     <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                     <input type="hidden" name="image_gallery_id[]" value="{{ $media->id }}">
                  </p>
                  @endforeach
               @endif
            </div>
         </div>
         @if($errors->has('image_gallery'))
         <p class="help-block">
            {{ $errors->first('image_gallery') }}
         </p>
         @endif
      </div>
   </div>
   <div class="col-xs-4">
      <div class="form-group">
         @if(! empty( $product ) && ! empty($product->thumbnail) && file_exists(public_path() . '/thumb/' . $product->thumbnail))
         <a href="{{ route('admin.home.media-file-download', ['model' => 'Product', 'field' => 'thumbnail', 'record_id' => $product->id]) }}" ><img src="{{ asset(env('APP_URL').'/thumb/'.$product->thumbnail) }}"></a>
         @endif
         {!! Form::label('thumbnail', trans('global.products.fields.thumbnail').'', ['class' => 'control-label']) !!}
         {!! Form::file('thumbnail', ['class' => 'form-control', 'style' => 'margin-top: 4px;']) !!}
         {!! Form::hidden('thumbnail_max_size', 10) !!}
         {!! Form::hidden('thumbnail_max_width', 4096) !!}
         {!! Form::hidden('thumbnail_max_height', 4096) !!}
         <p class="help-block">@lang('global.products.gallery-file-types')</p>
         @if($errors->has('thumbnail'))
         <p class="help-block">
            {{ $errors->first('thumbnail') }}
         </p>
         @endif
      </div>
   </div>
   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('other_files', trans('global.products.fields.other-files').'', ['class' => 'control-label']) !!}
         {!! Form::file('other_files[]', [
         'multiple',
         'class' => 'form-control file-upload',
         'data-url' => route('admin.media.upload'),
         'data-bucket' => 'other_files',
         'data-filekey' => 'other_files',
         'data-accept' => FILE_TYPES_GENERAL,
         'id' => 'other_files',
         ]) !!}
         <p class="help-block">{{trans('others.global_file_types_general')}}</p>
         <div class="photo-block">
            <div class="progress-bar">&nbsp;</div>
            <div class="files-list">
               @if( ! empty( $product ) )
                  @foreach($product->getMedia('other_files') as $media)
                  <p class="form-group">
                     <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                     <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                     <input type="hidden" name="other_files_id[]" value="{{ $media->id }}">
                  </p>
                  @endforeach
               @endif
            </div>
         </div>
         @if($errors->has('other_files'))
         <p class="help-block">
            {{ $errors->first('other_files') }}
         </p>
         @endif
      </div>
   </div>
   @endif
   
</div>