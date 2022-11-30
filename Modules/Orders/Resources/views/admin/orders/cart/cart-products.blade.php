@if( ! empty( $cartorderproducts ) && count( $cartorderproducts ) > 0 )
<div class="container col-md-12">
   <div class="top-cart-title">
      <h4><strong>@lang('orders::global.orders.checkout')</strong></h4>
   </div>
   <table id="cart" class="table table-hover table-condensed">
      <thead>
         <tr>
            <th style="text-align:center; width:50%;">Product</th>
            <th style="text-align:center; width:15%;">Price</th>
            <th style="text-align:center; width:8%;">Quantity</th>
            <th style="text-align:center; width:30%;">Total</th>
         </tr>
      </thead>
      @php
      $total = $sub_total = $discount_total = $tax_total = $rowid = 0;
      $currency_id = getDefaultCurrency( 'id' );
      $currency_code = getDefaultCurrency( 'code' );
      @endphp
      @foreach( $cartorderproducts as $product)
      <tbody>
         <tr>
            <td data-th="Product">
               <div class="row">
                  <?php
                     $record = $product->product; // Related product details.

                     $price = $record->sale_price;
                     $prices = ! empty($record->prices) ? json_decode( $record->prices, true ) : array();
                     if ( isCustomer() && ! empty( $prices['sale'][ $currency_code ] ) ) {
                        $price = $prices['sale'][ $currency_code ]; // If customer is on orders page we need to display prices in his own currency.
                     }
                     $quantity = $product->quantity;
                     $amount = $product_total = $quantity * $price;
                     $sub_total += $product_total; // Amount with out tax and discount.
                     
                     
                     $tax_value = 0;
                     $tax = $record->tax;
                     if ( $tax ) {
                       $tax_rate = $tax->rate;
                       $tax_value = $tax_rate * $quantity;
                       $rate_type = $tax->rate_type;
                       if ( $tax_rate > 0 && 'percent' === $rate_type ) {
                           $tax_value = ($amount * $tax_rate) / 100;
                       }
                     }
                     $tax_total += $tax_value;
                     
                     $discount_value = 0;
                     $discount = $record->discount;
                     if ( $discount ) {
                       $discount_rate = $discount->discount;
                       $discount_value = $discount_rate * $quantity;
                       $discount_type = $discount->discount_type;
                       if ( $discount_rate > 0 && 'percent' === $discount_type ) {
                           $discount_value = ($amount * $discount_rate) / 100;
                       }
                     }
                     $discount_total += $discount_value;
                     
                     $amount = $amount - $discount_value + $tax_value;
                     $total += $amount;
                     ?> 
                     <?php
                     $thumbnail = asset('images/product-50x50.jpg');
                     if ( ! empty( $product->product->thumbnail ) && file_exists(public_path() . '/thumb/' . $product->product->thumbnail) ) {
                      $thumbnail = asset(env('UPLOAD_PATH').'/thumb/' . $product->product->thumbnail);
                     }
                     ?>   
                  <div class="col-sm-2 hidden-xs"><img src="{{$thumbnail}}" class="img-orbit"/></div>
                  <div class="narrow-right col-sm-8">
                     <h5 class="nomargin">{{$product->product->name}}</h5>
                  
                  </div>
               </div>
            </td>
            <td data-th="Price">{{digiCurrency($price, $currency_id)}}</td>
            <td data-th="Quantity">
               <div class="qty-tr">
                  <div class="qty-tr-t">
                     <button class="qty-btn removeProd" data-record_slug="{{$product->slug}}" onclick="quantity_decrease_new('{{$product->slug}}')">
                     – 
                     </button>
                     <div class="qty-mr">
                        <input type="text" value="{{$product->quantity}}" class="qty-ret number" name="quantity_{{$product->slug}}" id="quantity_{{$product->slug}}" value="{{$product->quantity}}" min="1" data-stock_quantity="{{$record->stock_quantity}}" data-slug="{{$product->slug}}" onchange="quantityfield(this); quantity_increase_new('{{$product->slug}}', 'no');" onmouseover="quantityfield_previous(this.value)">
                     </div>
                     <button class="qty-btn updateProduct" data-record_slug="{{$product->slug}}" onclick="quantity_increase_new('{{$product->slug}}', 'yes')">
                     + 
                     </button>
                  </div>
               </div>
            </td>
            <td data-th="Subtotal">
               <table style="font-size: 11px;text-align: right;" >
                  <tr>     
                  <tr>
                     <td style="padding:0px;">   
                        @lang('orders::global.orders.price')&nbsp;{{digiCurrency( $price, $currency_id  ) }} 
                     </td>
                  </tr>
                  @if( $product->quantity > 1 )
                  <tr>
                     <td style="padding:0px;">
                        @lang('orders::global.orders.total')&nbsp;{{digiCurrency( $product_total, $currency_id  ) }}
                      
                     </td>
                  </tr>
                  @endif
                  @if( $discount_value > 0 )
                  <tr>
                     <td style="padding:0px;">
                        @lang('orders::global.orders.discount')&nbsp;-{{digiCurrency( $discount_value, $currency_id  ) }}
                     </td>
                  </tr>
                  @endif
                  @if( $tax_value > 0 )
                  <tr>
                     <td style="padding:0px;">
                        @lang('orders::global.orders.tax')&nbsp;+{{digiCurrency( $tax_value, $currency_id  ) }}
                     </td>
                  </tr>
                  @endif
                  </tr>
                  <tr>
                     <td style="padding:0px;">
                        <hr style="margin:1px;">
                       <strong>Total Sum:
                        {{digiCurrency($amount, $currency_id)}}</strong>
                     </td>
                  </tr>
               </table>
            </td>
            <td class="actions" data-th="">
               <button class="btn btn-danger btn-sm" onclick="remove_item('{{$product->slug}}')"><i class="fa fa-trash-o"></i></button>                                
            </td>
         </tr>
      </tbody>
      <script type="text/javascript">
         var product = {};
         product["product_id"] = '{{$record->id}}';
         product["rowid"] = '{{$rowid}}';
         product["quantity"] = '{{$product->quantity}}';
         js_global["cartproducts"].push( product );
      </script>
      @php
      $rowid++;
      @endphp
      @endforeach           
      <tfoot>
         <tr class="visible-xs">
            <td class="text-center"><strong>@lang('orders::global.orders.discount') (-){{digiCurrency($discount_total, $currency_id)}}</strong></td>
         </tr>
         <tr class="visible-xs">
            <td class="text-center"><strong>@lang('orders::global.orders.tax') (+){{digiCurrency($tax_total, $currency_id)}}</strong></td>
         </tr>
         <tr class="visible-xs">
            <td class="text-center"><strong>@lang('orders::global.orders.total') {{digiCurrency($total, $currency_id)}}</strong></td>
         </tr>
         <tr>
            <td colspan="2" class="hidden-xs"></td>
            <td class="hidden-xs text-center">@lang('orders::global.orders.total-discount')</td>
            <td class="hidden-xs text-center"> (-){{digiCurrency($discount_total, $currency_id)}}</td>
         </tr>
         <tr>
            <td colspan="2" class="hidden-xs"></td>
            <td class="hidden-xs text-center">@lang('orders::global.orders.total-tax')</td>
            <td class="hidden-xs text-center"> (+){{digiCurrency($tax_total, $currency_id)}}</td>
         </tr>
         <tr>
            <td colspan="2" class="hidden-xs"></td>
            <td class="hidden-xs text-center"><strong>@lang('orders::global.orders.total')</strong></td>
            <td class="hidden-xs text-center"><strong> {{digiCurrency($total, $currency_id)}}</strong></td>
            <hr>
         </tr>
      </tfoot>
   </table>

    @if ( isset( $ischeckout ) && 'no' === $ischeckout )
    <hr>
   <a class="btn btn-danger text-right" href="{{route('admin.orders.clear_cart')}}">@lang('orders::global.orders.clear')</a>
   <a class="btn btn-primary text-right pull-right" href="{{route('admin.orders.checkout')}}">@lang('orders::global.orders.checkout')</a>
   @endif

   <hr>
   <!-- payment method-->
   <div class="paymentCont">
      <div class="headingWrap">
         <h4 class="headingTop text-center">
            <strong>Select Your Payment Method</strong>
         </h4>
      </div>
      {!! Form::model($order, ['method' => 'POST', 'route' => ['admin.shop.paynow', $order->slug, 'order'], 'id' => 'paymentform']) !!}
      <div class="paymentWrap text-center">
         <div class="paymentWrap text-center">
            @php
            $payment_gateways = \App\Settings::where('moduletype', '=', 'payment')->where('status', '=', 'Active')->get()->pluck('module', 'key');
            $default = getSetting('default_payment_gateway', 'site_settings', 'offline');
            @endphp
            @foreach( $payment_gateways as $key => $title )
            <?php
            $can_display = true;
            if ( isCustomer() ) {
              $customer_currency_id = getContactInfo( Auth::id(), 'currency_id' );
              if ( ! empty( $customer_currency_id ) ) {
                $customer_currency_details = \App\Currency::find( $customer_currency_id );
                if ( ! empty( $customer_currency_details ) ) {
                  $customer_currency_code = $customer_currency_details->code;
                  if ( 'stripe' === $key ) {
                    if ( ! in_array( strtolower( $customer_currency_code ), stripeCurrencies() ) ) {
                      $can_display = false;
                    }
                  } elseif ( 'payu' === $key ) {
                    if ( ! in_array( strtolower( $customer_currency_code ), ['inr'] ) ) {
                      $can_display = false;
                    }
                  } elseif ( 'paypal' === $key ) {
                    if ( ! in_array( strtoupper( $customer_currency_code ), paypalCurrencies() ) ) {
                        $can_display = false;
                    }
                  }
                }
              }
            }

            if ( $can_display ) {
            ?>
            <div class="btn-group paymentBtnGroup btn-group-justified" data-toggle="buttons">
               {{ Form::radio('payment_gateway', $key, ( $key == $default ), ['id' => $key] ) }} <label for="{{$key}}" class="paymentMethod" name="options">{{$title}}</label>
               <p class="help-block"></p>
               @if($errors->has('payment_gateway'))
               <p class="help-block">
                  {{ $errors->first('payment_gateway') }}
               </p>
               @endif
            </div>
            <?php
            }
            ?>
            @endforeach            
            <input type="hidden" id="stripeToken" name="stripeToken" value="" />
            <input type="hidden" id="stripeEmail" name="stripeEmail" value="" />
            {!! Form::submit(trans('orders::global.orders.pay-now'), ['class' => 'btn btn-info', 'name' => 'btnsavemanage', 'id' => 'payment-button', 'value' => 'savemanage']) !!}
         </div>
      </div>
      {!! Form::close() !!}
   </div>  
</div>
@else
@lang('orders::global.orders.no-products', compact('cartorderproducts'))
@endif 
@section('javascript')
@parent


@php
$currency_code = getDefaultCurrency( 'code' );
if ( ! empty( $cartorderproducts ) && in_array( strtolower( $currency_code ), stripeCurrencies() ) ) {
@endphp
<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
<?php
   $jQuery_selector = "#stripe-button, #simontaxi-purchase-button";
   $stripe_options = array(
       'stripe_checkout_popup_title' => getSetting( 'stripe_checkout_popup_title', 'stripe', 'Stripe' ),
       'name' => getSetting( 'site_title', 'site-settings', 'Stripe' ),
       'stripe_checkout_popup_description' => getSetting( 'stripe_checkout_popup_description', 'stripe', 'Stripe' ),
   );
   $remember_me_box = $use_billing_address = $use_shipping_address = 0;
   
   $remember_me_box = ( getSetting( 'hide_stripe_remember_me_box', 'stripe', 'yes' ) == 'yes' ) ? 1 : 0;
   $use_billing_address = ( getSetting( 'require_billing_address', 'stripe', 'yes' ) == 'yes' ) ? 1 : 0;
   
   $email = getContactInfo('', 'email');
   $amount_payable = productsAmountDetails( $cartorderproducts, 'total' );
   
   $stripe_popup_image = asset( 'uploads/settings/' . getSetting( 'stripe_checkout_popup_image', 'stripe' ) );
   ?>
<script>
   var pop_checkout = true;
   console.log('Currency:{{$currency_code}}');
   
   var handler = StripeCheckout.configure(
   {
       key: '<?php echo getSetting( 'stripe_key', 'stripe' ); ?>',
       token: function(token, args)
       {
         
           jQuery( '#stripeToken' ).val( token.id );
           jQuery( '#stripeEmail' ).val( token.email );
           document.getElementById( 'paymentform' ).submit();
       }
   });

   
   
   jQuery( '#payment-button' ).unbind( 'click' );
   jQuery( '#payment-button' ).on( 'click', function(e)
   {
   
       e.preventDefault();
       
       var gateway = $('input[name="payment_gateway"]:checked').val();

       
       if ( 'stripe' == gateway ) {
           pop_checkout = true;
   
           if(pop_checkout)
           {
               // Open Checkout with further options
               handler.open({
                 image: '<?php echo $stripe_popup_image; ?>',
                 name: '<?php echo ( isset( $stripe_options['stripe_checkout_popup_title']) AND $stripe_options['stripe_checkout_popup_title'] != '' ) ? str_replace("'","\'", stripslashes( $stripe_options['stripe_checkout_popup_title'])) : str_replace("'","\'", stripslashes($stripe_options['name'])); ?>',
                 description: '<?php if( isset( $stripe_options['stripe_checkout_popup_description'] ) ) echo str_replace("'","\'", stripslashes( $stripe_options['stripe_checkout_popup_description'])); ?>',
                 currency: '<?php echo $currency_code; ?>',
                 allowRememberMe: '<?php echo $remember_me_box; ?>',
                 billingAddress: '<?php echo $use_billing_address; ?>',
                 shippingAddress: '<?php echo $use_shipping_address; ?>',
                 email: '<?php echo $email; ?>',
                 amount:'<?php echo $amount_payable * 100; ?>'
               });
           }
       } else {
           document.getElementById( 'paymentform' ).submit();
       }
   
   });
   <?php
      }
      ?>


   
   function updatecart() {
       jQuery.ajax({
           url: '{{route("admin.orders.updatecart")}}',
           type: 'GET',
           data: {
               '_token': crsf_hash,
               'source' : 'checkout'
           },
       
           beforeSend: function() {
             
           },
           success: function (data) {
               $('#cart-products').html( data );               
           },
           error: function (data) {
               $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
               $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
               $("html, body").scrollTop($("body").offset().top);
           }
       });
   }

</script>
@include('orders::admin.orders.cart.inc-dec-scripts')
@stop