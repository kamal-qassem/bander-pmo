<div class="row">
   <div class="col-xs-4">
      <div class="form-group">
         {!! Form::label('subject', trans('global.purchase-orders.fields.subject').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            {!! Form::text('subject', old('subject'), ['class' => 'form-control', 'placeholder' => 'subject']) !!}
            <p class="help-block"></p>
            @if($errors->has('subject'))
            <p class="help-block">
               {{ $errors->first('subject') }}
            </p>
            @endif
         </div>
      </div>
   </div>
    

      <div class="col-xs-4">
          <div class="form-group">
         {!! Form::label('customer_id', trans('global.purchase-orders.fields.customer').'*', ['class' => 'control-label']) !!}
         @if ( Gate::allows('customer_create') )
          @if( 'button' === $addnew_type )
           &nbsp;<button type="button" class="btn btn-danger modalForm" data-id="0" data-post="data-php" data-action="createsupplier" data-redirect="{{route('admin.purchase_orders.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.customer') )])}}">{{ trans('global.app_add_new') }}</button>
          @else        
          &nbsp;<a class="modalForm" data-action="createsupplier" data-selectedid="customer_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.customer') )])}}"><i class="fa fa-plus-square"></i></a>
          @endif
        @endif
         {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control select2', 'required' => '']) !!}
         <p class="help-block"></p>
         @if($errors->has('customer_id'))
         <p class="help-block">
            {{ $errors->first('customer_id') }}
         </p>
         @endif
     </div>
      </div>

       <div class="col-xs-4">
<div class="form-group">
 {!! Form::label('currency_id', trans('global.purchase-orders.fields.currency').'', ['class' => 'control-label']) !!}
  <?php
    $currency_id = ! empty( old('currency_id_old') ) ? old('currency_id_old') : '';
    if ( empty( $currency_id ) && ! empty( $invoice ) ) {
        $currency_id = $invoice->currency_id;
    }
    ?>
 {!! Form::select('currency_id', $currencies, old('currency_id',$currency_id), ['class' => 'form-control','data-live-search' => 'true','data-show-subtext' => 'true', 'disabled' => '']) !!}
 <input type="hidden" name="currency_id_old" id="currency_id_old" value="{{$currency_id}}">
 <p class="help-block"></p>
 @if($errors->has('currency_id'))
 <p class="help-block">
    {{ $errors->first('currency_id') }}
 </p>
 @endif
</div>
</div>
 
</div>
<div class="row">  
   <div class="col-xs-6">
      <div class="form-group">
  
         {!! Form::label('address', trans('global.purchase-orders.fields.address').'', ['class' => 'control-label']) !!}
         {!! Form::textarea('address', old('address'), ['class' => 'form-control ', 'placeholder' => '','rows' => 4]) !!}
         <p class="help-block"></p>
         @if($errors->has('address'))
         <p class="help-block">
            {{ $errors->first('address') }}
         </p>
         @endif
      </div>
   </div>

  <div class="col-xs-6 round-border">
    <div class="form-group">
    {!! Form::label('update_stock', trans('global.purchase-orders.fields.update-stock').'', ['class' => 'control-label']) !!}
    {!! Form::select('update_stock', yesnooptions( true ), old('update_stock', 'No'), ['class' => 'form-control select2']) !!}
      @if( ! empty( $invoice ) && $invoice->update_stock == 'Yes')
      <p class="help-block"><h3>@lang('custom.messages.stock-updated')</h3></p>
      @endif
      @if($errors->has('update_stock'))
      <p class="help-block">
      {{ $errors->first('update_stock') }}
      </p>
      @endif
    </div>
  </div>
</div>

<div class="row"> 
    
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('invoice_prefix', trans('global.purchase-orders.fields.invoice-prefix').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            <?php
              $po_prefix = getSetting( 'Po_Number_Prefix', 'purchase-orders-settings' );
              ?>
            {!! Form::text('invoice_prefix', old('invoice_prefix',$po_prefix), ['class' => 'form-control', 'placeholder' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has('invoice_prefix'))
            <p class="help-block">
               {{ $errors->first('invoice_prefix') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('show_quantity_as', trans('global.purchase-orders.fields.show-quantity-as').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            <?php
              $show_quantity_as = getSetting( 'show_quantity_as', 'purchase-orders-settings' );
               if ( ! empty( $invoice ) ) {
                $show_quantity_as = $invoice->show_quantity_as;
            }
              ?>
            {!! Form::text('show_quantity_as', old('show_quantity_as',$show_quantity_as), ['class' => 'form-control', 'placeholder' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has('show_quantity_as'))
            <p class="help-block">
               {{ $errors->first('show_quantity_as') }}
            </p>
            @endif
         </div>
      </div>
   </div>
     <div class="col-xs-{{COLUMNS}}">
         <div class="form-group">
         {!! Form::label('status', trans('global.purchase-orders.fields.status').'', ['class' => 'control-label']) !!}
         {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2']) !!}
         <p class="help-block"></p>
         @if($errors->has('status'))
         <p class="help-block">
            {{ $errors->first('status') }}
         </p>
         @endif
     </div>
      </div>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('invoice_no', trans('global.purchase-orders.fields.invoice-no').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
              <?php
            $invoice_no = getNextNumber('PO');
            if ( ! empty( $invoice ) ) {
                $invoice_no = $invoice->invoice_no;
            }
            ?>
            {!! Form::text('invoice_no', old('invoice_no',$invoice_no), ['class' => 'form-control', 'placeholder' => 'Enter purchase order number']) !!}
            <p class="help-block"></p>
            @if($errors->has('invoice_no'))
            <p class="help-block">
               {{ $errors->first('invoice_no') }}
            </p>
            @endif
         </div>
      </div>
   </div>


   </div>
   <div class="row">
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('order_date', trans('global.purchase-orders.fields.order-date').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            <?php
            $order_date = ! empty($invoice->order_date) ? digiDate( $invoice->order_date ) : '';
            ?>
            {!! Form::text('order_date', old('order_date', $order_date), ['class' => 'form-control date', 'placeholder' => '' ,'required'=>'']) !!}
            <p class="help-block"></p>
            @if($errors->has('order_date'))
            <p class="help-block">
               {{ $errors->first('order_date') }}
            </p>
            @endif
         </div>
      </div>
   </div>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('order_due_date', trans('global.purchase-orders.fields.order-due-date').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            <?php
            $order_due_date = ! empty($invoice->order_due_date) ? digiDate( $invoice->order_due_date ) : '';
            ?>
            {!! Form::text('order_due_date', old('order_due_date', $order_due_date), ['class' => 'form-control date', 'placeholder' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has('order_due_date'))
            <p class="help-block">
               {{ $errors->first('order_due_date') }}
            </p>
            @endif
         </div>
      </div>
   </div>


  @if( isPluginActive('productwarehouse') )
  <div class="col-xs-{{COLUMNS}}">
   <div class="form-group">
         {!! Form::label('warehouse_id', trans('global.purchase-orders.fields.warehouse').'', ['class' => 'control-label']) !!}
         @if ( Gate::allows('warehouse_create') )
           @if( 'button' === $addnew_type )
           &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createwarehouse" data-selectedid="ware_house_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.warehouse') )])}}">{{ trans('global.app_add_new') }}</button>
           @else        
          &nbsp;<a class="modalForm" data-action="createwarehouse" data-selectedid="ware_house_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.warehouse') )])}}"><i class="fa fa-plus-square"></i></a>
          @endif
        @endif
         {!! Form::select('warehouse_id', $warehouses, old('warehouse_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'id' => 'ware_house_id']) !!}
         <p class="help-block"></p>
         @if($errors->has('warehouse_id'))
         <p class="help-block">
            {{ $errors->first('warehouse_id') }}
         </p>
         @endif
      </div>
   </div>
   @endif

      <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
         {!! Form::label('reference', trans('global.purchase-orders.fields.reference').'', ['class' => 'control-label form-label']) !!}
         <div class="form-line">
            {!! Form::text('reference', old('reference'), ['class' => 'form-control', 'placeholder' => '']) !!}
            <p class="help-block"></p>
            @if($errors->has('reference'))
            <p class="help-block">
               {{ $errors->first('reference') }}
            </p>
            @endif
         </div>
      </div>
   </div>

     </div>
    
   <?php
      $enable_products_slider = getSetting( 'enable_products_slider', 'site_settings' );
      if ( 'yes' === $enable_products_slider ) {
          ?>
   <div class="col-xs-12">
      <div class="form-group productsslider" style="display: none;">
         <?php
         if ( empty( $invoice ) ) {
            $invoice = array();
         }
         ?>
         @include('admin.common.products-slider',array('products_return' => $invoice))
      </div>
      <span id="productsslider_loader" style="display: block;">
      <img src="{{asset('images/loading-small.gif')}}"/>
      </span>
   </div>
   <?php
      }
      ?>
   <div class="col-xs-12">
      <div class="productsrow">
         <?php
         if ( empty( $invoice ) ) {
            $invoice = array();
         }
         ?>
         @include('admin.common.add-products', array('products_return' => $invoice))
      </div>
   </div>
    
   <div class="col-xs-12">
      <div class="form-group">
         {!! Form::label('notes', trans('global.purchase-orders.fields.notes').'', ['class' => 'control-label']) !!}
          <?php
             $predefined_notes = getSetting( 'predefined_notes', 'purchase-orders-settings' );
              if ( ! empty( $invoice ) ) {
                $predefined_notes = $invoice->invoice_notes;
            }
         ?>
         {!! Form::textarea('notes', old('notes',$predefined_notes), ['class' => 'form-control editor', 'placeholder' => '']) !!}
         <p class="help-block"></p>
         @if($errors->has('notes'))
         <p class="help-block">
            {{ $errors->first('notes') }}
         </p>
         @endif
      </div>
   </div>


      
  <div class="col-xs-{{COLUMNS}}">
   <div class="form-group">
  
         {!! Form::label('tax_id', trans('global.purchase-orders.fields.tax').'', ['class' => 'control-label']) !!}
         @if ( Gate::allows('tax_create') )
          @if( 'button' === $addnew_type )
          &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createtax" data-selectedid="tax_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.tax') )])}}">{{ trans('global.app_add_new') }}</button>
          @else        
          &nbsp;<a class="modalForm" data-action="createtax" data-selectedid="tax_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.tax') )])}}"><i class="fa fa-plus-square"></i></a>
          @endif
        @endif
         {!! Form::select('tax_id', $taxes, old('tax_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
         <p class="help-block"></p>
         @if($errors->has('tax_id'))
         <p class="help-block">
            {{ $errors->first('tax_id') }}
         </p>
         @endif
      </div>
   </div>

   <?php
      $products_details = ( ! empty( $invoice ) ) ? $invoice->products : array();
      if ( ! empty( $products_details ) ) {
          $products_details = json_decode( $products_details );
      }
      $tax_format = ! empty( $products_details->tax_format ) ? $products_details->tax_format : 'after_tax';
      ?>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
  
         {!! Form::label('tax_format', trans('global.invoices.fields.tax_format').'', ['class' => 'control-label']) !!}
         {!! Form::select('tax_format', $enum_tax_format, $tax_format, ['class' => 'form-control select2']) !!}
      </div>
   </div>
    <div class="col-xs-{{COLUMNS}}">
        <div class="form-group">
  
         {!! Form::label('discount_id', trans('global.purchase-orders.fields.discount').'', ['class' => 'control-label']) !!}
         @if ( Gate::allows('discount_create') )
          @if( 'button' === $addnew_type )
          &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="creatediscount" data-selectedid="discount_id" data-redirect="{{route('admin.products.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.discount') )])}}">{{ trans('global.app_add_new') }}</button>
          @else        
          &nbsp;<a class="modalForm" data-action="creatediscount" data-selectedid="discount_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.purchase-orders.fields.discount') )])}}"><i class="fa fa-plus-square"></i></a>
          @endif
        @endif
         {!! Form::select('discount_id', $discounts, old('discount_id'), ['class' => 'form-control select2']) !!}
         @if($errors->has('discount_id'))
         <p class="help-block">
            {{ $errors->first('discount_id') }}
         </p>
         @endif
      </div>
   </div>
   <?php          
      $products_details = ( ! empty( $invoice ) ) ? $invoice->products : array();
      if ( ! empty( $products_details ) ) {
          $products_details = json_decode( $products_details );
      }
      $discount_format = ! empty( $products_details->discount_format ) ? $products_details->discount_format : 'after_tax';
      ?>
   <div class="col-xs-{{COLUMNS}}">
      <div class="form-group">
                          
         {!! Form::label('discount_format', trans('global.invoices.fields.discount_format').'', ['class' => 'control-label']) !!}
         {!! Form::select('discount_format', $enum_discounts_format, $discount_format, ['class' => 'form-control select2', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
      </div>
   </div>