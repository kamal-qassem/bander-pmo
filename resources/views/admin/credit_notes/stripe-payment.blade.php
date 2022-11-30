@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.credit_notes.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-12">
                    @include('admin.credit_notes.invoice.invoice', compact('invoice'))
                </div>
            </div>

            {!! Form::model($invoice, ['method' => 'POST', 'route' => ['admin.payment.process-payment', $token, 'invoice'], 'id' => 'paymentform', 'style' => 'display:none']) !!}
                <input type="hidden" id="stripeToken" name="stripeToken" value="" />
                <input type="hidden" id="stripeEmail" name="stripeEmail" value="" />
                <input type="hidden" id="paymethod" name="paymethod" value="stripe" />
            {!! Form::close() !!}

            <a href="{{ route('admin.credit_notes.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent

    @include('admin.common.standard-ckeditor')

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>

    @php
    $currency_code = getDefaultCurrency( 'code' );

	$currency_code = getCurrency( $invoice->currency_id, 'code');

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
        $amount_payable = $invoice->amount;
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
