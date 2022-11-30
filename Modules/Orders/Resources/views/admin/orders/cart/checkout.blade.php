@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('orders::global.orders.checkout')
			
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-12" id="cart-products">                    
                    @include('orders::admin.orders.cart.cart-products', compact('cartorderproducts'))
				</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.orders.index') }}" style="margin-left:20px;" class="btn btn-success">@lang('orders::global.orders.myorders')</a>
            &nbsp;|&nbsp;
            <a href="{{ route('admin.orders.create') }}" class="btn btn-warning">@lang('orders::global.orders.continue-shop')</a>
            &nbsp;|&nbsp;
           
            <a class="btn btn-info text-right" href="{{route('admin.orders.create')}}">@lang('orders::global.orders.back-to-cart')</a>
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
    $remember_me_box = $use_billing_address = $use_shipping_address = 0;

    $remember_me_box = ( getSetting( 'hide_stripe_remember_me_box', 'stripe', 'yes' ) == 'yes' ) ? 'false' : 'true';
    $use_billing_address = ( getSetting( 'require_billing_address', 'stripe', 'yes' ) == 'yes' ) ? 'true' : 'false';
    $use_shipping_address = ( getSetting( 'require_shipping_address', 'stripe', 'yes' ) == 'yes' ) ? 'true' : 'false';
    
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
              allowRememberMe: <?php echo $remember_me_box; ?>,
              billingAddress: <?php echo $use_billing_address; ?>,
              shippingAddress: <?php echo $use_shipping_address; ?>,
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