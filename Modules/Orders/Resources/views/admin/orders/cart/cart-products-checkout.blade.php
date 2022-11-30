<?php
$products = getProducts();
if ( ! empty( $products ) ) {
?>

<div class="row"> 
<div class="col-md-6">
    
    <div class="products-slider">
      @foreach( $products as $product )
      <div class="item">
            <div class="product" data-product_id="{{$product->id}}" data-toggle="modal" data-target="#loadingModal">
                <div class="st-testimo-box">
                    <div class="st-testimo-profile">
                        <img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}" alt="{{$product->name}}" class="img-circle img-responsive" title="{{$product->name}}" data-lazy="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}">
                    </div>
                    <p class="st-text">{{$product->name}}</p>
                </div>
                <p class="st-name">
                    <p>{{trans('global.products.fields.sale-price')}} : <b>{{digiCurrency($product->sale_price)}}</b></p>
                    <p>{{trans('global.products.fields.actual-price')}} : <b><strike>{{digiCurrency($product->actual_price)}}</strike></b></p>
                    
                </p>                    
            </div>
            <button class="btn btn-primary addToCartQuick" data-product_id="{{$product->id}}" data-stock_quantity="{{$product->stock_quantity}}"><i class="fa fa-shopping-cart"></i>&nbsp;{{trans('orders::global.orders.add-to-cart')}}</button>
        </div>
        @endforeach
    </div>


    <div class="container">
	<div class="row">
		<div class="col-md-6">
			<h2>{{trans('orders::global.orders.featured-products')}}</b></h2>
			<div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="0">
			<!-- Carousel indicators -->
		  
			<!-- Wrapper for carousel items -->
			<div class="carousel-inner">
				<div class="item carousel-item active">
					<div class="row">
						@foreach( $products as $product )
						<div class="col-sm-4">
							<div class="thumb-wrapper">
								<div class="img-box">
									<img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}" alt="{{$product->name}}" class="img-circle img-responsive" title="{{$product->name}}" data-lazy="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}">									
								</div>
								<div class="thumb-content">
									<h4 class="txt-shrink">{{$product->name}}</h4>									
									<div class="star-rating">
										<ul class="list-inline">
											<li class="list-inline-item"><i class="fa fa-star"></i></li>
											<li class="list-inline-item"><i class="fa fa-star"></i></li>
											<li class="list-inline-item"><i class="fa fa-star"></i></li>
											<li class="list-inline-item"><i class="fa fa-star"></i></li>
											<li class="list-inline-item"><i class="fa fa-star-o"></i></li>
										</ul>
									</div>
									<p class="item-price"><strike>{{digiCurrency($product->actual_price)}}</strike> <b>{{digiCurrency($product->sale_price)}}</b></p>
								 <button class="btn btn-primary addToCartQuickBtn" data-product_id="{{$product->id}}" data-stock_quantity="{{$product->stock_quantity}}"><i class="fa fa-shopping-cart fa-1x"></i>&nbsp;&nbsp;{{trans('orders::global.orders.add-to-cart')}}</button>
								</div>						
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>


</div>

</div>
</div>

</div>
<div class="col-md-6">
    @include('orders::admin.orders.cart.cart', compact('products_return'))
</div>
</div>


@include('orders::admin.orders.cart.modal-loading', compact('products_return'))

@section('javascript')
@parent

@include('orders::admin.orders.cart.cart-scripts')

<link rel="stylesheet" type="text/css" href="{{asset('slick/slick.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('slick/slick-theme.css')}}"/>
<script type="text/javascript" src="{{asset('slick/slick.min.js')}}"></script>
<script type="text/javascript">
    $('.products-slider').slick({
      lazyLoad: 'ondemand',
      slidesToShow: 4,
      slidesToScroll: 1,
      arrows: true,
      dots: true,
      prevArrow: '<button class="slick-prev slick-arrow" aria-label="{{trans('custom.common.previous')}}" type="button" style="display: block; background-color: #444;">{{trans('custom.common.previous')}}</button>',
      nextArrow: '<button class="slick-next slick-arrow" aria-label="{{trans('custom.common.next')}}" type="button" style="display: block; background-color: #444;">{{trans('custom.common.next')}}</button>',
      lazyLoad: 'ondemand',
      focusOnSelect: true
    });

    $('.product').on('click', function(event){
        
        var product_id = $(this).data('product_id');

        $('#loading_icon').show();

        $('#loadingModal').toggle();
        
		loadEmailTemplate( product_id );

		$("#loadingModal").draggable({
		      handle: ".modal-header"
		 });
    });

    function loadEmailTemplate (product_id ) {
		$('#loading_icon').show();

		jQuery.ajax({
	        url: '{{route("admin.orders.searchproduct")}}',
	        type: 'POST',
	        data: {
	        	'_token': crsf_hash,
	        	product_id: product_id
	        },
	      
	        beforeSend: function() {
	         
	        },
	        success: function (data) {
	            $('#loading_icon').hide();
	            $('#loadingModal #content').html( data );
	        },
	        error: function (data) {
	            $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
	            $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
	            $("html, body").scrollTop($("body").offset().top);
	        }
	    });
	}

	
	$('.addToCartQuick').click(function() {
		var product_id = $(this).data('product_id');
		var quantity = 1;
		var stock_quantity = $(this).data('stock_quantity');
		

		var cartproducts = js_global['cartproducts'];
		var incart = [];
		var rowid = 0;
		var product_found = 'no';
		if ( cartproducts.length > 0 ) {
	        jQuery( cartproducts ).each(function(key, val) {
	            incart.push( val.product_id );
	            if ( val.product_id ==  product_id ) {
	                rowid = val.rowid;
	                product_found = 'yes';
	            }
	        });
	    }

	    if( 'yes' === product_found ) {
	    	notifyMe( 'danger', '{{trans("orders::global.orders.already-in-cart")}}');
	    } else {
			addtocart( product_id, quantity );
		}
	});


	/*new js addtoquick cart*/

	$('.addToCartQuickBtn').click(function() {
		var product_id = $(this).data('product_id');
		var quantity = 1;
		var stock_quantity = $(this).data('stock_quantity');
		

		var cartproducts = js_global['cartproducts'];
		var incart = [];
		var rowid = 0;
		var product_found = 'no';
		if ( cartproducts.length > 0 ) {
	        jQuery( cartproducts ).each(function(key, val) {
	            incart.push( val.product_id );
	            if ( val.product_id ==  product_id ) {
	                rowid = val.rowid;
	                product_found = 'yes';
	            }
	        });
	    }

	    if( 'yes' === product_found ) {
	    	notifyMe( 'danger', '{{trans("orders::global.orders.already-in-cart")}}');
	    } else {
			addtocart( product_id, quantity );
		}
	});


	function addtocart( product_id, quantity ) {
		jQuery.ajax({
	        url: '{{route("admin.orders.addtocart")}}',
	        type: 'POST',
	        dataType: 'json',
	        data: {
	        	'_token': crsf_hash,
	        	product_id: product_id,
	        	quantity: quantity
	        },
	     
	        beforeSend: function() {
	           
	        },
	        success: function (data) {
	          
	            $('#loadingModal').modal('hide');
	            notifyMe( 'success', '{{trans("orders::global.orders.product-added-to-cart")}}');

	            var product = {};
			    product["product_id"] = product_id;
			    product["rowid"] = js_global["cartproducts"].length;
			    product["quantity"] = quantity;
				js_global["cartproducts"].push( product );

				console.log(js_global);
	            updatecart();
	        },
	        error: function (data) {
	            $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
	            $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
	            $("html, body").scrollTop($("body").offset().top);
	        }
	    });
	}

	$('#addToCart').click(function() {
		var product_id = $('#product_id').val();
		var quantity = parseInt( $('#quantity').val() );
		var stock_quantity = $('#stock_quantity').val();

		var cartproducts = js_global['cartproducts'];
		if ( cartproducts.length > 0 ) {
	        jQuery( cartproducts ).each(function(key, val) {
	            if ( val.product_id ==  product_id ) {
	                quantity += parseInt( val.quantity );
	            }
	        });
	    }

		if ( quantity > stock_quantity ) {
		
			alert('{{trans("orders::global.orders.quantity-not-available")}}');
			return false;
		}
		addtocart( product_id, quantity );
	});

	function updatecart() {
		jQuery.ajax({
	        url: '{{route("admin.orders.updatecart")}}',
	        type: 'GET',
	        data: {
	        	'_token': crsf_hash
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
	updatecart();
	
	function quantity_increase_new( record_slug, increse ) {
	
		var quantity = $('#quantity_' + record_slug).val();
		if ( typeof( increse ) === 'undefined' ) {
			increse = 'yes';
		}

		jQuery.ajax({
	        url: '{{route("admin.orders.update_cart_product")}}',
	        type: 'POST',
	        data: {
	        	'_token': crsf_hash,
	        	'record_slug': record_slug,
	        	'quantity': quantity,
				'increse' : increse
	        },
	    
	        beforeSend: function() {
	          
	        },
	        success: function (data) {
	            notifyMe( data.status, data.message);
	            updatecart();
	        },
	        error: function (data) {
	            $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
	            $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
	            $("html, body").scrollTop($("body").offset().top);
	        }
	    });
	}
	
	function quantity_decrease_new( record_slug ) {
		if ( confirm('{{trans("global.app_are_you_sure")}}') )
		{
		
			var quantity = $('#quantity_' + record_slug).val();
			jQuery.ajax({
		        url: '{{route("admin.orders.remove_from_cart")}}',
		        type: 'POST',
		        data: {
		        	'_token': crsf_hash,
		        	'record_slug': record_slug,
					'quantity' : quantity
		        },
		    
		        beforeSend: function() {
		          
		        },
		        success: function (data) {
		            notifyMe( 'success', data.message);
		            updatecart();
		        },
		        error: function (data) {
		            $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
		            $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
		            $("html, body").scrollTop($("body").offset().top);
		        }
		    });
		}
	}

     

    

</script>

@stop
<?php } ?>