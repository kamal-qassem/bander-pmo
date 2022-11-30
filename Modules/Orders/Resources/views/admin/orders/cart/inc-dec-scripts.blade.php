<script type="text/javascript">
	

	var presentvalue = 0;
	function quantityfield_previous( val ) {
	    presentvalue = val;
		
	}

	function quantityfield( obj ) {
    
	    var quantity = $(obj).val();
	    var stock_quantity = $(obj).data('stock_quantity');
	    var slug = $(obj).data('slug');

	 
	    if ( stock_quantity < quantity ) {
	        if ( confirm("Maxinum quantity available for this product is " + stock_quantity + ". Do you want to continue with this quantity " + stock_quantity + "?" ) ) {
	            $(obj).val( stock_quantity );
	            quantity_increase_new( slug, 'no' );
	        } else {
	            $(obj).val( presentvalue );
	        }
	    }

	}

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
				notifyMe( data.status, data.message);

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