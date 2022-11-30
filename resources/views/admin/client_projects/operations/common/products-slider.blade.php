<?php
$products = getProducts();
$currency_id = ! empty( $products_return->currency_id ) ? $products_return->currency_id : getDefaultCurrency('id');

$currency_code = getCurrency($currency_id, 'code');

if ( ! empty( $products ) ) {
    $slider_type = getSetting( 'slider_type', 'site_settings', 'bootstrap');
    if ( 'jssor' === $slider_type ) {
?>
<div style="min-height: 120px; border:1px solid; padding: 8px; margin-bottom: 8px;">
    <!-- Jssor Slider Begin -->
    
    <style>
       /*jssor slider loading skin spin css*/
        .jssorl-009-spin img {
            animation-name: jssorl-009-spin;
            animation-duration: 1.6s;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }

        @keyframes jssorl-009-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

    </style>
    <div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:980px;height:100px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="{{asset('svg/spin.svg')}}" />
        </div>

        <!-- Slides Container -->
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:980px;height:100px;overflow:hidden;">
            <?php
            $index = 0;
            ?>
            @foreach( $products as $product )
	            @if($product->thumbnail)
	            <div class="product" data-product_id="{{$product->id}}" id="slide_{{$index}}" data-stock_quantity="{{$product->stock_quantity}}">
	                <img data-u="image" src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}" class="sliderImage" />
	                <div data-u="thumbnavigator" class="slideTitle">{{$product->name}}</div>
	            </div>
	            <?php
	            $index++;
	            ?>
	            @endif
            @endforeach
        </div>
        
        <!--#region Bullet Navigator Skin Begin -->
        <!-- Help: https://www.jssor.com/development/slider-with-bullet-navigator.html -->
        <style>
            .jssorb031 {position:absolute;}
            .jssorb031 .i {position:absolute;cursor:pointer;}
            .jssorb031 .i .b {fill:#000;fill-opacity:0.5;stroke:#fff;stroke-width:1200;stroke-miterlimit:10;stroke-opacity:0.3;}
            .jssorb031 .i:hover .b {fill:#fff;fill-opacity:.7;stroke:#000;stroke-opacity:.5;}
            .jssorb031 .iav .b {fill:#fff;stroke:#000;fill-opacity:1;}
            .jssorb031 .i.idn {opacity:.3;}
        </style>
        <div data-u="navigator" class="jssorb031" style="position:absolute;bottom:12px;right:12px;" data-autocenter="1" data-scale="0.5" data-scale-bottom="0.75">
            <div data-u="prototype" class="i" style="width:16px;height:16px;">
                <svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    <circle class="b" cx="8000" cy="8000" r="5800"></circle>
                </svg>
            </div>
        </div>
        <!--#endregion Bullet Navigator Skin End -->
    
        <!--#region Arrow Navigator Skin Begin -->
        <!-- Help: https://www.jssor.com/development/slider-with-arrow-navigator.html -->
        <style>
            .jssora051 {display:block;position:absolute;cursor:pointer;}
            .jssora051 .a {fill:none;stroke:#fff;stroke-width:360;stroke-miterlimit:10;}
            .jssora051:hover {opacity:.8;}
            .jssora051.jssora051dn {opacity:.5;}
            .jssora051.jssora051ds {opacity:.3;pointer-events:none;}
            .slideTitle {
            	padding-top: 80px;
            	font-weight: bold;
            	color: #fff;
            }

        </style>
        <div data-u="arrowleft" class="jssora051" style="width:55px;height:55px;top:0px;left:25px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <polyline class="a" points="11040,1920 4960,8000 11040,14080 "></polyline>
            </svg>
        </div>
        <div data-u="arrowright" class="jssora051" style="width:55px;height:55px;top:0px;right:25px;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <polyline class="a" points="4960,1920 11040,8000 4960,14080 "></polyline>
            </svg>
        </div>
        <!--#endregion Arrow Navigator Skin End -->
    </div>
    <!-- Jssor Slider End -->
</div>
<?php } else {
    ?>
    
<div class="col-md-12">
    
    <div class="products-slider">
      @foreach( $products as $product )
	  <?php
	$prices = ! empty( $product->prices ) ? json_decode( $product->prices, true ) : array();

	$actual_price = ! empty( $prices['actual'][ $currency_code ] ) ? $prices['actual'][ $currency_code ] : '0';
	$sale_price = ! empty( $prices['sale'][ $currency_code ] ) ? $prices['sale'][ $currency_code ] : '0';
	  ?>
      <div class="item">
            <div class="product" data-product_id="{{$product->id}}" data-stock_quantity="{{$product->stock_quantity}}">
                <div class="st-testimo-box">
                    <div class="st-testimo-profile">
                        <img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}" alt="{{$product->name}}" class="img-circle img-responsive" title="{{$product->name}}" data-lazy="{{ asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail) }}">
                    </div>
                    <p class="st-text">{{$product->name}}</p>
                </div>

				<p class="st-name">
                    <p>{{trans('global.products.fields.sale-price')}} : <b>
					
					
					<span class="sale_price {{$product->id}}_sale_price_{{$currency_code}}" data-product_id="{{$product->id}}" data-price="{{$sale_price}}" id="{{$product->id}}_sale_price_{{$currency_code}}">{{digiCurrency($sale_price, $currency_id)}}</span>
					
					<?php
					if ( ! empty( $prices['sale'] ) ) {
						foreach( $prices['sale'] as $code => $price ) {
							if ( $code == $currency_code ) {
								continue;
							}
							?>
							<span class="sale_price {{$product->id}}_sale_price_{{$code}}" data-product_id="{{$product->id}}" data-price="{{$price}}" id="{{$product->id}}_sale_price_{{$code}}" style="display:none;">{{digiCurrency($price, $code)}}</span>
							<?php
						}
					}
					?>					
					</b></p>
                    <p>{{trans('global.products.fields.actual-price')}} : <b><strike>
					<span class="actual_price {{$product->id}}_actual_price_{{$currency_code}}" data-product_id="{{$product->id}}" data-price="{{$actual_price}}" id="{{$product->id}}_actual_price_{{$currency_code}}">
					{{digiCurrency($actual_price, $currency_id)}}
					</span>
					
					<?php
					if ( ! empty( $prices['actual'] ) ) {
						foreach( $prices['actual'] as $code => $price ) {
							if ( $code == $currency_code ) {
								continue;
							}
							?>
							<span class="actual_price {{$product->id}}_actual_price_{{$code}}" data-product_id="{{$product->id}}" data-price="{{$price}}" id="{{$product->id}}_actual_price_{{$code}}" style="display:none;">{{digiCurrency($price, $code)}}</span>
							<?php
						}
					}
					?>
					</strike></b></p>
                    
                </p>                    
            </div>
        </div>
        @endforeach
    </div>
    
</div>

    
    <?php
} ?>

@section('javascript')

@parent

@if ( 'jssor' === $slider_type )
<script src="{{ url('js/jssor.slider.min.js') }}"></script>
<script type="text/javascript">
jssor_1_slider_init = function() {

    var jssor_1_options = {
      $AutoPlay: 1,
      $Idle: 0,
      $SlideDuration: 5000,
      $SlideEasing: $Jease$.$Linear,
      $PauseOnHover: 4,
      $SlideWidth: 140,
      $Align: 0,
      $SlideSpacing: 10,
      $FillMode: 2
    };

    var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

    /*#region responsive code begin*/

    var MAX_WIDTH = 980;

    function ScaleSlider() {
        var containerElement = jssor_1_slider.$Elmt.parentNode;
        var containerWidth = containerElement.clientWidth;

        if (containerWidth) {

            var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

            jssor_1_slider.$ScaleWidth(expectedWidth);
        }
        else {
            window.setTimeout(ScaleSlider, 30);
        }
    }

    ScaleSlider();

    $Jssor$.$AddEvent(window, "load", ScaleSlider);
    $Jssor$.$AddEvent(window, "resize", ScaleSlider);
    $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
    /*#endregion responsive code end*/

    jssor_1_slider.$On($JssorSlider$.$EVT_CLICK, SliderClickEventHandler);
    function SliderClickEventHandler( slideIndex, event ) {
		var product_id = $('#slide_' + slideIndex).data('product_id');
		var cartproducts = js_global['cartproducts'];
		var incart = [];
		var rowid = 0;

		if ( cartproducts.length > 0 ) {
			jQuery( cartproducts ).each(function(key, val) {
				incart.push( val.product_id );
				if ( val.product_id ==  product_id ) {
					rowid = val.rowid;
				}
			});
		}
		

		if(jQuery.inArray(product_id, incart) === -1) { // Which means product not added to cart.		
			var cvalue = 0;
		    $('.product_row').each(function() {
		        var current = $(this).data( 'rowid' );
		        if( parseInt( current ) > cvalue ) {
		            cvalue = current;
		        }
		    });
		    	    
		    if ( incart.length > 0 ) { // Which means there are no products in the cart.
		    	$('#addproduct').trigger('click');
		    	cvalue += 1;
			}
			getProductDetails( product_id, cvalue );
		} else {			
			if ( confirm('{{trans("custom.products.already-in-cart")}}') ) {
				var quantity = $('#quantity-' + rowid).val();
				quantity = parseFloat( quantity ) + 1;
				$('#quantity-' + rowid).val( quantity );
				rowTotal( rowid );				
			}
		}
	}
};


</script>
<script type="text/javascript">
	jssor_1_slider_init();
</script>
@else

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
      focusOnSelect: false
    });

    $('.product').on('click', function(event){
        
        var product_id = $(this).data('product_id');
        var cartproducts = js_global['cartproducts'];
        var incart = [];
        var rowid = 0;

        if ( cartproducts.length > 0 ) {
            jQuery( cartproducts ).each(function(key, val) {
                incart.push( val.product_id );
                if ( val.product_id ==  product_id ) {
                    rowid = val.rowid;
                }
            });
        }

        if(jQuery.inArray(product_id, incart) === -1) { // Which means product not added to cart.       
            var cvalue = 0;
            $('.product_row').each(function() {
                var current = $(this).data( 'rowid' );
                if( parseInt( current ) > cvalue ) {
                    cvalue = current;
                }
            });
                    
            if ( incart.length > 0 ) { // Which means there are no products in the cart.
                // $('#addproduct').trigger('click');
				addproduct();
                cvalue += 1;
            }
            // console.log( product_id );
			getProductDetails( product_id, cvalue );

            notifyMe( 'success', '{{trans("others.products.product-added")}}');
        } else {            
            if ( confirm('{{trans("custom.products.already-in-cart")}}') ) {
                var quantity = $('#quantity-' + rowid).val();
                quantity = parseFloat( quantity ) + 1;
                $('#quantity-' + rowid).val( quantity );
                rowTotal( rowid );

                notifyMe( 'success', '{{trans("others.products.product-added")}}');
            }
        }
    });
</script>
@endif

@stop
<?php } ?>