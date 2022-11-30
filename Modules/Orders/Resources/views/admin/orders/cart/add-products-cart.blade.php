<?php
$products_count = \App\Product::count();
$currency_id = getDefaultCurrency( 'id' );
$currency_code = getDefaultCurrency( 'code' );


if ( $products_count > 0 ) {
?>

<div class="col-md-12">

   <div class="col-md-6">
      
      <?php
        $enable_products_slider = getSetting( 'enable_products_slider', 'site_settings' );
        
        if ( 'yes' === $enable_products_slider ) {
               $products = getProducts('Active', ['quantity' => ['condition' => '>', 'value' => 0], 'currency' => ['condition' => 'like', 'value' => $currency_code]] );
            ?>
  
      <div class="products-slider">
         @foreach( $products as $product )
         <?php
         $actual_price = $product->actual_price;
         $sale_price = $product->sale_price;

         $prices = ! empty($product->prices) ? json_decode( $product->prices, true ) : array();
         if ( isCustomer() ) {
            if ( ! empty( $prices['sale'][ $currency_code ] ) ) {
              $sale_price = $prices['sale'][ $currency_code ]; // If customer is on orders page we need to display prices in his own currency.
            }
            if ( ! empty( $prices['actual'][ $currency_code ] ) ) {
              $actual_price = $prices['actual'][ $currency_code ]; // If customer is on orders page we need to display prices in his own currency.
            }
         }
         ?>
         <div class="item">
            <div class="product" data-product_id="{{$product->id}}" data-toggle="modal" data-target="#loadingModal">
               <div class="st-testimo-box">
                  <?php
                 $thumbnail = asset('images/product-50x50.jpg');
                 if ( ! empty( $product->thumbnail ) && file_exists(public_path() . '/thumb/' . $product->thumbnail) ) {
                  $thumbnail = asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail);
                 }
                 ?>
                  <div class="st-testimo-profile">
                     <img src="{{$thumbnail}}" alt="{{$product->name}}" class="img-circle img-responsive" title="{{$product->name}}" data-lazy="{{$thumbnail}}">
                  </div>
                  <p class="st-text">{{$product->name}}</p>
                <p class="item-price">
                  @if( ! empty( $actual_price ) )
                  <strike>{{digiCurrency($actual_price, $currency_id)}}</strike> &nbsp; 
                  @endif
                  <b>{{digiCurrency($sale_price, $currency_id)}}</b></p>                   
            <button class="btn btn-info addToCartQuick" data-product_id="{{$product->id}}" data-stock_quantity="{{$product->stock_quantity}}"><i class="fa fa-shopping-cart"></i>&nbsp;{{trans('orders::global.orders.add-to-cart')}}</button>
               </div>
            </div>
         </div>

         @endforeach
      </div>
      
      <?php
       }
        ?>

               <?php
               $products = \App\Product::where('product_status', 'Active')->where('stock_quantity', '>', 0)->where('prices_available', 'like', "%$currency_code%")->paginate(9);
               ?>
               <h2 style="margin-top: 50px;"><b>{{trans('orders::global.orders.featured-products')}}</b></h2>
               <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="0">
                  <!-- Carousel indicators -->
                  <!-- Wrapper for carousel items -->
                  <div class="carousel-inner">
                     <div class="item carousel-item active">
                        <div class="row">
                           @foreach( $products as $product )
                           <?php
                           $actual_price = $product->actual_price;
                           $sale_price = $product->sale_price;

                           $prices = ! empty($product->prices) ? json_decode( $product->prices, true ) : array();
                           if ( isCustomer() ) {
                              if ( ! empty( $prices['sale'][ $currency_code ] ) ) {
                                $sale_price = $prices['sale'][ $currency_code ]; // If customer is on orders page we need to display prices in his own currency.
                              }
                              if ( ! empty( $prices['actual'][ $currency_code ] ) ) {
                                $actual_price = $prices['actual'][ $currency_code ]; // If customer is on orders page we need to display prices in his own currency.
                              }
                           }
                           ?>
                           <div class="col-sm-4">
                              <div class="thumb-wrapper">
                                 <?php
                                 $thumbnail = asset('images/product-50x50.jpg');
                                 if ( ! empty( $product->thumbnail ) && file_exists(public_path() . '/thumb/' . $product->thumbnail) ) {
                                  $thumbnail = asset(env('UPLOAD_PATH').'/thumb/' . $product->thumbnail);
                                 }
                                 ?>
                                 <div class="img-box">
                                    <img src="{{$thumbnail}}" alt="{{$product->name}}" class="img-circle img-responsive" title="{{$product->name}}" data-lazy="{{$thumbnail}}">                 
                                 </div>
                                 <div class="thumb-content">
                                    <h4 class="txt-shrink" title="{{$product->name}}"><a href="javascript:void(0);" class="product" data-product_id="{{$product->id}}" data-toggle="modal" data-target="#loadingModal">{{$product->name}}</a></h4>
                                    <div class="star-rating" style="display:none;">
                                       <ul class="list-inline">
                                          <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                          <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                          <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                          <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                          <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                       </ul>
                                    </div>
                                    <p class="item-price">
                                      @if( ! empty( $actual_price ) )
                                      <strike>{{digiCurrency($actual_price, $currency_id)}}</strike> <br/>
                                      @endif
                                      <b>{{digiCurrency($sale_price, $currency_id)}}</b></p>
                                    <button class="btn addToCartQuickBtn" data-product_id="{{$product->id}}" data-stock_quantity="{{$product->stock_quantity}}"><i class="fa fa-shopping-cart fa-1x"></i>&nbsp;&nbsp;{{trans('orders::global.orders.add-to-cart')}}</button>
                                 </div>
                              </div>
                           </div>
                           @endforeach
                        </div>
                        {{ $products->links() }}
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
     slidesToShow: 3,
     slidesToScroll: 1,
     arrows: true,
    
     prevArrow: '<button class="slick-prev slick-arrow" aria-label="{{trans('custom.common.previous')}}" type="button" style="display: block; background-color: #444;">{{trans('custom.common.previous')}}</button>',
     nextArrow: '<button class="slick-next slick-arrow" aria-label="{{trans('custom.common.next')}}" type="button" style="display: block; background-color: #444;">{{trans('custom.common.next')}}</button>',
     lazyLoad: 'ondemand',
     focusOnSelect: true
   });

   $('.showproductdetails').on('click', function(event){
      var product_id = $(this).data('product_id');
      $('#loading_icon').show();
      $('#loadingModal').toggle();
      loadEmailTemplate( product_id );
      $("#loadingModal").draggable({
        handle: ".modal-header"
      });
   });

   function loadProductTemplate (product_id ) {
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

             if ( data.quantity <= 0 ) { // If the item quantity is zero, let us remvoe it from 'cartproducts' global variable.
                var product_id = data.product_id;
                // Let us remove the product id from cartitems array.
                var cartproducts = js_global['cartproducts'];

                if ( cartproducts.length > 0 ) {
                    jQuery( cartproducts ).each(function(key, val) {
                        if ( val.product_id ==  product_id ) {
                            cartproducts = cartproducts.slice(key, 1);
                        }
                    });
                    js_global['cartproducts'] = cartproducts;
                }
              }
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
   
    
      function remove_item( record_slug ) {
    
    var quantity = $('#quantity_' + record_slug).val();
    
    jQuery.ajax({
      url: '{{route("admin.orders.remove_from_cart")}}',
      type: 'POST',
      data: {
        '_token': crsf_hash,
        'record_slug': record_slug,
        'quantity' : quantity,
        'operation' : 'removeitem'
      },
    
      beforeSend: function() {
       




      },
      success: function (data) {
        notifyMe( data.status, data.message);

        var product_id = data.product_id;
        // Let us remove the product id from cartitems array.
        var cartproducts = js_global['cartproducts'];

        if ( cartproducts.length > 0 ) {
            jQuery( cartproducts ).each(function(key, val) {
                if ( val.product_id ==  product_id ) {
                    cartproducts = cartproducts.slice(key, 1);
                }
            });

            js_global['cartproducts'] = cartproducts;
        }
        updatecart();
      },
      error: function (data) {
        $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
        $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
        $("html, body").scrollTop($("body").offset().top);
      }
    });
  }
   
   
</script>
@stop
<?php } ?>