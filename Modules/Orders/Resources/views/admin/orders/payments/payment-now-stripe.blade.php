@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.payments.title')</h3>
    
    <div class="panel-body packages">
        <div class="row">
         
          @include('orders::admin.orders.order-payment-details', compact('record'))

          {!! Form::model($record, ['method' => 'POST', 'id' => 'paymentform', 'route' => ['admin.orders.process-payment-now', $record->slug, 'order']]) !!}
         
            <input type="hidden" id="stripeToken" name="stripeToken" value="" />
            <input type="hidden" id="stripeEmail" name="stripeEmail" value="" />
            <input id="stripe" name="payment_gateway" type="hidden" value="stripe">
          {!! Form::submit(trans('orders::global.orders.pay-now'), ['class' => 'btn btn-info', 'name' => 'btnsavemanage', 'value' => 'savemanage', 'style' => 'display:none;']) !!}

          &nbsp;|&nbsp;
          <a href="{{ route('admin.orders.index') }}" class="btn btn-success">@lang('orders::global.orders.myorders')</a>
          &nbsp;|&nbsp;
          <a href="{{ route('admin.orders.create') }}" class="btn btn-info">@lang('orders::global.orders.place-new-order')</a>
          {!! Form::close() !!}


          
          
       
        </div>
   
               
    </div>
@stop

@section('javascript')
    @parent       

    @php
    $currency_code = getDefaultCurrency( 'code' );
    if ( in_array( strtolower( $currency_code ), stripeCurrencies() ) ) {
    @endphp
    <script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
    <?php
        $jQuery_selector = "#stripe-button, #simontaxi-purchase-button";
        $stripe_options = array(
            'stripe_checkout_popup_title' => getSetting( 'stripe_checkout_popup_title', 'stripe', 'Stripe' ),
            'name' => getSetting( 'site_title', 'site-settings', 'Stripe' ),
            'stripe_checkout_popup_description' => getSetting( 'stripe_checkout_popup_description', 'stripe', 'Stripe' ),
        );
        $remember_me_box = $use_billing_address = $use_shipping_address = 'false';

        $remember_me_box = ( getSetting( 'hide_stripe_remember_me_box', 'stripe', 'yes' ) == 'yes' ) ? 'false' : 'true';
        $use_billing_address = ( getSetting( 'require_billing_address', 'stripe', 'yes' ) == 'yes' ) ? 'true' : 'false';
        $use_shipping_address = ( getSetting( 'require_shipping_address', 'stripe', 'yes' ) == 'yes' ) ? 'true' : 'false';
        
        $email = getContactInfo('', 'email');
        $amount_payable = $record->price;
        $stripe_popup_image = asset( 'uploads/settings/' . getSetting( 'stripe_checkout_popup_image', 'stripe' ) );
    ?>
    <script>
    var pop_checkout = true;
   

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

    if(pop_checkout)
    {
        // Open Checkout with further options
        handler.open({
          image: '<?php echo $stripe_popup_image; ?>',
          name: '<?php echo ( isset( $stripe_options['stripe_checkout_popup_title']) AND $stripe_options['stripe_checkout_popup_title'] != '' ) ? str_replace("'","\'", stripslashes( $stripe_options['stripe_checkout_popup_title'])) : str_replace("'","\'", stripslashes($stripe_options['name'])); ?>',
          description: '<?php if( isset( $stripe_options['stripe_checkout_popup_description'] ) ) echo str_replace("'","\'", stripslashes( $stripe_options['stripe_checkout_popup_description'])); ?>',
          currency: '<?php echo $currency_code; ?>',
          allowRememberMe: <?php echo $remember_me_box; ?>,
          billingAddress: <?php echo $use_billing_address; ?>,
          shippingAddress: <?php echo $use_shipping_address; ?>,
          email: '<?php echo $email; ?>',
          amount:'<?php echo $amount_payable * 100; ?>'
        });
    }
    <?php
    }
    ?>
    </script>
            
@stop
