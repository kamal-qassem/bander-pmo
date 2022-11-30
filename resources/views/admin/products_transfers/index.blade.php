@extends('layouts.app')
@section('content')
<h3 class="page-title">@lang('global.products-transfer.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.products_transfers.store'],'class'=>'formvalidation']) !!}
<div class="panel panel-default">
   <div class="panel-heading">
      @lang('global.app_create')
   </div>
   <div class="panel-body">
      <div class="row">
         <div class="col-xs-6">
            <div class="form-group">
               {!! Form::label('ware_house_id_from', trans('custom.products-transfer.transfer_from').'*', ['class' => 'control-label']) !!}
               {!! Form::select('ware_house_id_from', $ware_houses, old('ware_house_id'), ['class' => 'form-control select2', 'data-live-search' => 'true','data-show-subtext' => 'true' ,'required' => '']) !!}
               <p class="help-block"></p>
               @if($errors->has('ware_house_id_from'))
               <p class="help-block">
                  {{ $errors->first('ware_house_id_from') }}
               </p>
               @endif
            </div>
         </div>
          <div class="col-xs-6">
            <div class="form-group">
               {!! Form::label('ware_house_id_to', trans('custom.products-transfer.transfer_to').'*', ['class' => 'control-label']) !!}
               {!! Form::select('ware_house_id_to', $ware_houses, old('ware_house_id'), ['class' => 'form-control select2', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
               <p class="help-block"></p>
               @if($errors->has('ware_house_id_to'))
               <p class="help-block">
                  {{ $errors->first('ware_house_id_to') }}
               </p>
               @endif
            </div>
         </div>
         </div>
          <div class="row">
         <div class="col-xs-8">
            <div class="form-group">
               {!! Form::label('products', trans('global.products.title').'', ['class' => 'control-label']) !!}
               <button type="button" class="btn btn-primary btn-xs" id="selectbtn-product">
               {{ trans('global.app_select_all') }}
               </button>
               <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-product">
               {{ trans('global.app_deselect_all') }}
               </button>
               {!! Form::select('product[]', $products, old('product'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-product' ,'data-live-search' => 'true','data-show-subtext' => 'true']) !!}
               <p class="help-block"></p>
               @if($errors->has('product'))
               <p class="help-block">
                  {{ $errors->first('product') }}
               </p>
               @endif
            </div>
        </div>
        
      </div>
   </div>
</div>
{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger']) !!}
{!! Form::close() !!}
@stop
@section('javascript')
@parent
<script>
   $("#selectbtn-product").click(function(){
       $("#selectall-product > option").prop("selected","selected");
       $("#selectall-product").trigger("change");
   });
   $("#deselectbtn-product").click(function(){
       $("#selectall-product > option").prop("selected","");
       $("#selectall-product").trigger("change");
   });
   
   $('#ware_house_id_from').change(function() {
      var d_csrf=crsf_token+'='+crsf_hash;
      $.ajax({
            url: '{{url('admin/search_products')}}/products',
            dataType: "json",
            method: 'post',
            data: 'type=warehouse_products&wid='+$(this).val()+'&'+d_csrf,
            success: function (data) {
               console.log(data);
               var options;
               $.each(data, function( key, val ) {
                 options += '<option value="'+ key +'">'+val+'</option>';                 
               });
               $('#selectall-product').empty().append( options ).trigger('refresh'); 
            }
        });
   });
   
</script>
@stop