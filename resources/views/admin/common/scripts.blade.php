<script>
var controller = '{{getController("controller")}}';  
var billtype = 'products';
var d_csrf=crsf_token+'='+crsf_hash;

<?php
$currency_id = getDefaultCurrency('id');
$currency_symbol = getCurrency($currency_id, 'symbol');
$currency_code = getCurrency($currency_id, 'code');

if ( ! empty( $products_return->currency_id ) ) {
	$currency_id = $products_return->currency_id;
    $currency_symbol = getCurrency($currency_id, 'symbol');
    $currency_code = getCurrency($currency_id, 'code');
}
?>
var currency_id = '{{$currency_id}}';

$('#hdata').data('curr', '{{$currency_symbol}}' );
$('#hdata').data('currency_id', currency_id );


/**
 * id: productID
 * row: row Index
 */
function getProductDetails( id, row ) {
    
    var cartproducts = js_global['cartproducts'];
    
    var incart = [];
    var incart_indexes = [];
    var rowid = 0;
    var product_found = 'no';
    incart_indexes.push( row );

    if ( cartproducts.length > 0 ) {
        jQuery( cartproducts ).each(function(key, val) {            
            
            if ( val.product_id ==  id ) {
                if ( val.rowid == row ) { // Which means user/admin changing the product

                } else {
                    rowid = val.rowid;
                    product_found = 'yes';
                }
            } else {
                incart.push( val.product_id );
            }
        });
    }

	var newcurrency_id = $('#hdata').data('currency_id');

    if( product_found == 'no') { // Which means product not added to cart.   
        $.ajax({
            url: '{{url('admin/search_products')}}/' + billtype,
            dataType: "json",
            method: 'post',
            data: 'product_id='+id+'&type=product_details&row_num='+row+'&wid='+$("#ware_house_id option:selected").val()+'&'+d_csrf+'&currency_id=' + newcurrency_id,
            success: function (data) {
                assignValues( row, data);            
            }
        });
    } else {
        $('#productselectname-' + row).val('');
        notifyMe( 'danger', '{{trans( "custom.products.already-in-cart-message" )}}');
        return false;
    }
}

$('#productname-0').autocomplete({
        source: function (request, response) {
            
            var product_ids = [];
            $( '.product_ids').each(function() {
                product_ids.push( $(this).val()  );
            });

            var newcurrency_id = $('#hdata').data('currency_id');
            
            $.ajax({
                url: '{{url('admin/search_products')}}/' + billtype,
                dataType: "json",
                method: 'post',
              data: 'name_startsWith='+request.term+'&type=product_list&row_num=1&wid='+$("#ware_house_id").val()+'&'+d_csrf + '&product_ids=' +product_ids+'&currency_id=' + newcurrency_id,
                success: function (data) {
                    response($.map(data, function (item) {
                        var product_d = item['id'];
                        
                        return {
                            label: item['namequantity'],
                            value: item['name'],
                            data: item
                        };
                    }));
                }
            });
        },
        autoFocus: true,
        minLength: 0,
        select: function (event, ui) {
            var data = ui.item.data;
            assignValues( 0,  data );
        }
    });

var presentvalue = 0;
function quantityfield_previous( val ) {
    presentvalue = val;
    
}

function quantityfield( obj ) {
    
    var quantity = $(obj).val();
    var stock_quantity = $(obj).data('stock_quantity');
    var slug = $(obj).data('slug');

    if ( controller != 'PurchaseOrdersController') {
        if ( stock_quantity < quantity ) {
            if ( confirm("Maxinum quantity available for this product is " + stock_quantity + ". Do you want to continue with this quantity " + stock_quantity + "?" ) ) {
                $(obj).val( stock_quantity );
                quantity_increase( slug, 'no' );
            } else {
                $(obj).val( presentvalue );
            }
        }
    }

}

var rowTotal = function (numb) {
    var quantity = parseFloat( formInputGet("#quantity", numb) ); // Quantity
    var stock_quantity = parseFloat( formInputGet("#stock_quantity", numb) ); // Quantity


    var priceVal = parseFloat( formInputGet("#price", numb) ); // Item Price

    var tax_rate = parseFloat( formInputGet("#tax_rate", numb) );
    var tax_type = formInputGet("#tax_type", numb);

    var discount_rate = parseFloat( formInputGet("#discount_rate", numb) );
    var discount_type = formInputGet("#discount_type", numb);

    var subTotal = priceVal * quantity;

    $("#product_amount-" + numb).val(subTotal);

    var tax_value = tax_rate;
    if ( 'percent' == tax_type && tax_rate > 0 ) {
        tax_value = ( subTotal * tax_rate ) / 100;
    }
    $("#tax_value-" + numb).val(deciFormat(tax_value));
    $("#tax_value_display-" + numb).html(deciFormat(tax_value, 'yes'));

    var discount_value = discount_rate;
    if ( 'percent' == discount_type && discount_rate > 0 ) {
        discount_value = ( subTotal * discount_rate ) / 100;
    }
    $("#discount_value-" + numb).val(deciFormat(discount_value));
    $("#discount_value_display-" + numb).html(deciFormat(discount_value, 'yes'));

    $('#products_amount').val( subTotal ); // Amount without Tax and Discount

    // tax_value-0: Hidden, tax_value_display-0:HTML, discount_value-0:Hidden, discount_value_display-0:HTML
    var totalValue = parseFloat( subTotal ) + parseFloat( tax_value ) - parseFloat( discount_value );
    $("#result-" + numb).html(deciFormat(totalValue, 'yes'));
    $("#total-" + numb).val(deciFormat(totalValue));

    calculateTotal();
};

var formInputGet = function (iname, inumber) {
    var inputId;
    inputId = iname + '-' + inumber;
    var inputValue = $(inputId).val();

    if (inputValue == '') {

        return 0;
    } else {
        return inputValue;
    }
};

//caculations
var precentCalc = function (total, percentageVal) {
    return (total / 100) * percentageVal;
};
//format
var deciFormat = function (minput, is_currency) {
    if(!minput) {
        minput=0;   
    }
    minput = parseFloat(minput).toFixed( decimals );
    if(!is_currency) {
        is_currency='no';   
    }
    if ( 'yes' == is_currency ) {
        currency = $('#hdata').data('curr');
		
		if ( 'left' === currency_position ) {
            minput = currency + minput;
        }
        if ( 'right' === currency_position ) {
            minput = minput + currency;
        }
        if ( 'left_with_space' === currency_position ) {
            minput = currency + ' ' + minput;
        }
        if ( 'right_with_space' === currency_position ) {
            minput = minput + ' ' + currency;
        }
    }
    return minput
};

//product total
var calculateTotal = function () {
    
    var grand_total = 0;
    var sub_total = 0; // Total with discount.
    var total_tax = 0;
    var total_discount = 0;
    var product_amount = 0;
	var products_amount = 0;

    $('.product_row').each(function () {
        var rowIndex = $(this).data('rowid');
        var rowTotal_val = $("#total-" + rowIndex).val();
        var rowTax = $("#tax_value-" + rowIndex).val();
        var rowDiscount = $("#discount_value-" + rowIndex).val();
		var product_amount = $("#product_amount-" + rowIndex).val();
		
        grand_total += parseFloat( rowTotal_val );
        total_tax += parseFloat( rowTax );
        total_discount += parseFloat( rowDiscount );
		products_amount += parseFloat( product_amount );
    });

    sub_total = parseFloat( grand_total ) + parseFloat( total_discount );
        

    $("#total_discount_display").html( deciFormat(total_discount, 'yes') );
    $("#total_tax_display").html( deciFormat(total_tax, 'yes') );
    $('#grand_total_display').html( deciFormat(grand_total, 'yes') );
    $('#sub_total_display').html( deciFormat(sub_total, 'yes') );

    $("#total_discount").val( deciFormat(total_discount) );
    $("#total_tax").val( deciFormat(total_tax) );
    $('#grand_total').val( deciFormat(grand_total) );
    $('#sub_total').val( deciFormat(sub_total) );
	$('#products_amount').val( deciFormat(products_amount) );
	
	// Calculation of Cart Tax.
	var tax_id = $('#tax_id').val();  
    if ( tax_id > 0 ) {
		getDetails( tax_id, 'tax' );
	}
	
	var discount_id = $('#discount_id').val();
    if ( discount_id > 0 ) {
		getDetails( discount_id, 'discount' );
	}
};

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function addproduct() {
	var cvalue = 0;
	$('.product_row').each(function() {
		var current = $(this).data( 'rowid' );
		if( parseInt( current ) > cvalue ) {
			cvalue = current;
		}
	});

    if ( $('#productselectname-' + cvalue).val() == '') {
        alert('{{trans("custom.products.please-select-product")}}');
        $('#productselectname-' + cvalue).focus();
        return false;
    }

    var rate = $('#price-' + cvalue).val();
    var tax_rate = $('#tax_rate-' + cvalue).val();
    var tax_type = $('#tax_type-' + cvalue).val();
    var discount_rate = $('#discount_rate-' + cvalue).val();
    var discount_type = $('#discount_type-' + cvalue).val();

    if ( tax_type == 'percent' && tax_rate > 100 ) {
        alert('{{trans("custom.products.not-more-than-rate")}}');
        $('#tax_rate-' + cvalue).focus();
        return false;
    }

    if( discount_type == 'percent' && discount_rate > 100 ){
        alert('{{trans("custom.products.not-more-than-rate")}}');
        $('#discount_rate-' + cvalue).focus();
        return false;
    }

	cvalue += 1;

	var functionNum = "'" + cvalue + "'";

	//product row
	var productname = '<input type="text" class="form-control" name="product_name[]" placeholder="Enter Product name or Code" id="productname-' + cvalue + '" required>';

	<?php
	$products_selection = getSetting( 'products_selection', 'site_settings' );
    $products_selection = 'select';
	if ( in_array( $products_selection, array( 'select', 'select2' ) ) ) {
		$products = getProducts();
		$select2 = '';
		if( 'select2' === $products_selection ) {
			$select2 = ' select2';
		}
		?>
		productname = '<select class="form-control product_name_select <?php echo $select2; ?>" required="required" name="product_name[]" placeholder="{{trans('custom.products.please_select')}}" id="productselectname-' + cvalue + '" onchange="getProductDetails(this.value, '+functionNum+')" required><option value="">{{trans('custom.products.please_select')}}</option>';
        @if ( ! empty( $products_return ) && ! empty( $products_return->project_id ) )
            productname += '<optgroup label="@lang('global.products.title')">';
        @endif
		<?php
		if ( ! empty( $products ) ) {
			foreach ($products as $product) {
				?>
				productname+= '<option value="<?php echo $product->id; ?>">{{$product->name . " (".$product->stock_quantity.")"}}</option>'
				<?php
			}
			
		}
		?>
        @if ( ! empty( $products_return ) && ! empty( $products_return->project_id ) )
            productname += '</optgroup>';
        @endif

        @if ( ! empty( $products_return ) && ! empty( $products_return->project_id ) )
            <?php
            $query = \App\ProjectTask::where( 'billable', 'yes' )->where('project_id', $products_return->project_id);
            $query->select([
                'project_tasks.id',
                'project_tasks.name',
                'project_tasks.description',
                'project_tasks.startdate',
                'project_tasks.duedate',
                'project_tasks.datefinished',
                'project_tasks.billable',
                'project_tasks.billed',
                'project_tasks.hourly_rate',
            ]);                
            if ( $query->count() > 0 ) { ?>
                productname += '<optgroup label="@lang('global.project-tasks.title')">';
                <?php
                
                foreach ($query->get() as $record ) {
                    ?>
                    productname += '<option value="{{$record->id}}_task">{{$record->name}}@if( $record->billed == 'yes' ) {{trans('global.client-projects.billed')}} @endif</option>';
                    <?php
                } ?>
                productname += '</optgroup>';
                <?php
            }
            ?>

            <?php
            // Expenses.
            $query = \App\Expense::where( 'billable', 'yes' )->where('project_id', $products_return->project_id);
            $query->select([
                'expenses.id',
                'expenses.name',
                'expenses.entry_date',
                'expenses.amount',
                'expenses.description',
                'expenses.ref_no',
                'expenses.project_id',
                'expenses.billable',
                'expenses.billed',
            ]);
            if ( $query->count() > 0 ) { ?>
                productname += '<optgroup label="@lang('global.expense.title')">';
                <?php
                
                foreach ($query->get() as $record ) {
                    
                    $prices = ! empty( $record->prices ) ? json_decode( $record->prices, true ) : array();

                    $actual_price = ! empty( $prices['actual'][ $currency_code ] ) ? $prices['actual'][ $currency_code ] : '0';
                    $sale_price = ! empty( $prices['sale'][ $currency_code ] ) ? $prices['sale'][ $currency_code ] : '0';
        
                    $tax = $record->tax;
                    if ( $tax ) {
                        $record->tax_rate = $tax->rate;
                        $record->tax_value = $tax->rate;
                        $record->rate_type = $tax->rate_type;
                        if ( $tax->rate > 0 && 'percent' === $tax->rate_type ) {
                            $record->tax_value = ($sale_price * $tax->rate) / 100;
                        }
                    } else {
                        $record->tax_rate = 0;
                        $record->tax_value = 0;
                        $record->rate_type = 'percent';
                    }

                    $discount = $record->discount;
                    if ( $discount ) {
                        $record->discount_rate = $discount->discount;
                        $record->discount_value = $record->discount;
                        $record->discount_type = $discount->discount_type;
                        if ( $discount->discount > 0 && 'percent' === $discount->discount_type ) {
                            $record->discount_value = ($sale_price * $discount->discount) / 100;
                        }
                    } else {
                        $record->discount_rate = 0;
                        $record->discount_value = 0;
                        $record->discount_type = 'percent';
                    }
                    ?>
                    productname += '<option value="{{$record->id}}_expense">{{$record->name . '('.digiCurrency( $record->amount, $products_return->currency_id ).')'}}@if( $record->billed == 'yes' ) {{trans('global.client-projects.billed')}} @endif</option>';
                    <?php
                } ?>
                productname += '</optgroup>'
                <?php
            }
            ?>
        @endif
		productname += '</select>';
		<?php
	}
	?>
    @if ( Gate::allows('product_create') )
        @if( 'button' === $addnew_type )
        productname += '<button type="button" onclick="modalForm(\'createproduct\',' + cvalue + ')" class="btn btn-danger" data-toggle="modal"  data-id="' + cvalue + '" data-post="data-php" data-action="createproduct" data-redirect="{{route('admin.purchase_orders.create')}}">{{ trans('global.app_add_new') }}</button>';
        @else
        productname += '<a onclick="modalForm(\'createproduct\',' + cvalue + ')" class="modalForm" data-toggle="modal"  data-id="' + cvalue + '" data-post="data-php" data-action="createproduct" data-redirect="{{route('admin.purchase_orders.create')}}"><i class="fa fa-plus-square"></i></button>';
        @endif
    @endif
	var buttons = '<p><i class="fa fa-minus-circle fa-lg quantity-decrease" aria-hidden="true" onclick="quantity_decrease(' + functionNum + ')"></i>&nbsp;<i class="fa fa-plus-circle fa-lg quantity-increase" aria-hidden="true" onclick="quantity_increase(' + functionNum + ')"></i><p>';

	var data = '<tr height="90px" class="product_row" data-rowid="' + cvalue + '" id="product_row_' + cvalue + '"><td valign="top">'+productname+'</td><td valign="top"><input type="text" class="form-control req amnt" name="product_qty[]" id="quantity-' + cvalue + '" onkeypress="return isNumber(event)" onscroll="rowTotal(' + functionNum + ')" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off" value="1" onmouseover="quantityfield_previous(this.value)">' + buttons + '<input type="hidden" id="alert-' + cvalue + '" value=""  name="alert[]"> </td> <td valign="top"><input type="text" class="form-control req prc" name="product_price[]" id="price-' + cvalue + '" onkeypress="return isDecimalNumber(event, this)" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off" step="0.01" placeholder="{{trans('custom.products.rate')}}" required><input type="hidden" class="product_amount" name="product_amount[]" id="product_amount-' + cvalue + '" value=""></td><td valign="top"> <input type="number" class="form-control vat" name="product_tax[]" id="tax_rate-' + cvalue + '" onkeypress="return isDecimalNumber(event, this)" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off" step="0.01" placeholder="{{trans('custom.products.tax_percent')}}"><input type="hidden" name="tax_value[]" id="tax_value-' + cvalue + '" value="0"><select name="tax_type[]" id="tax_type-' + cvalue + '" onchange="rowTotal(' + functionNum + ')"><option value="percent" onchange="rowTotal(' + functionNum + ')">{{trans('custom.common.percent')}}</option><option value="value">{{trans('custom.common.value')}}</option></select></td> <td id="tax_value_display-' + cvalue + '" class="text-center" valign="top">0</td> <td valign="top"><input type="number" class="form-control discount" name="product_discount[]" onkeypress="return isDecimalNumber(event, this)" id="discount_rate-' + cvalue + '" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off" step="0.01" placeholder="{{trans('custom.products.discount_percent')}}"><input type="hidden" name="discount_value[]" id="discount_value-' + cvalue + '" value="0"><select name="discount_type[]" style="margin-top: 5px; id="discount_type-' + cvalue + '" onchange="rowTotal(' + functionNum + ')"><option value="percent">{{trans('custom.common.percent')}}</option><option value="value">{{trans('custom.common.value')}}</option></select></td><td class="text-center" id="discount_value_display-' + cvalue + '" valign="top">0</td> <td class="text-center" valign="top"><strong><span class="ttlText" id="result-' + cvalue + '">0</span></strong></td> <td class="text-center" valign="top"><button type="button" data-rowid="' + cvalue + '" class="btn btn-danger removeProd" title="Remove" onclick="removeProd(' + cvalue + ')"> <i class="fa fa-trash-o"></i> </button> </td><input type="hidden" name="taxa[]" id="taxa-' + cvalue + '" value="0"><input type="hidden" name="disca[]" id="disca-' + cvalue + '" value="0"><input type="hidden" class="ttInput" name="product_subtotal[]" id="total-' + cvalue + '" value="0"> <input type="hidden" class="pdIn" name="pid[]" id="pid-' + cvalue + '" value="0"> <input type="hidden" name="unit[]" id="unit-' + cvalue + '" value=""> <input type="hidden" name="hsn[]" id="hsn-' + cvalue + '" value=""> <input type="hidden" name="product_ids[]" id="product_ids-' + cvalue + '" value="" class="product_ids"></tr><tr id="product_desc_row_' + cvalue + '"><td colspan="9"><textarea class="form-control"  id="dpid-' + cvalue + '" name="product_description[]" placeholder="Enter Product description" autocomplete="off"></textarea><input type="hidden" name="alert[]" id="alert-' + cvalue + '" value="0"><input type="hidden" name="stock_quantity[]" id="stock_quantity-' + cvalue + '" value="0"></td></tr>';
	


	$('tr.last-item-row').before(data);

	row = cvalue;

    @if( 'select2' === $products_selection )
        $('#productselectname-' + cvalue).select2();
    @endif

    @if ( ! in_array( $products_selection, array( 'select', 'select2' ) ) )
    	$('#productname-' + cvalue).autocomplete({
    		source: function (request, response) {
    			var product_ids = [];
    			$( '.product_ids').each(function() {
    				product_ids.push( $(this).val()  );
    			});
    			var newcurrency_id = $('#hdata').data('currency_id');

    			$.ajax({
    				url: '{{url('admin/search_products')}}/' + billtype,
    				dataType: "json",
    				method: 'post',
    				data: 'name_startsWith='+request.term+'&type=product_list&row_num='+row+'&wid='+$("#ware_house_id option:selected").val()+'&'+d_csrf + '&product_ids=' + product_ids+'&currency_id=' + newcurrency_id,
    				success: function (data) {
    					response($.map(data, function (item) {
    						return {
    							label: item['namequantity'],
    							value: item['name'],
    							data: item
    						};
    					}));
    				}
    			});
    		},
    		autoFocus: true,
    		minLength: 0,
    		select: function (event, ui) {
    			var data = ui.item.data;
    			assignValues( cvalue,  data );
    		},
    		create: function (e) {
    			$(this).prev('.ui-helper-hidden-accessible').remove();
    		}
    	});
    @endif

}

function quantity_increase( rowid ) {
   var quantity = $('#quantity-' + rowid).val();
   var stock_quantity = $('#stock_quantity-' + rowid).val();

    var product_id = $('#product_ids-' + rowid).val();
    if ( product_id != '' ) {
        quantity = parseFloat( quantity ) + 1;

        if ( controller != 'PurchaseOrdersController') {
            if ( stock_quantity < quantity ) {
                $('#quantity-' + rowid).val( parseFloat(quantity) - 1 );
                alert('{{trans("others.products.quantity-not-available")}}');
                return false;
           }
        }
        $('#quantity-' + rowid).val( quantity );
        rowTotal( rowid );
    } else {
        alert('{{trans("custom.products.please-select-product")}}');
    }
}


function quantity_decrease( rowid ) {
    var quantity = $('#quantity-' + rowid).val();
    var product_id = $('#product_ids-' + rowid).val();
    if ( product_id != '' ) {
        if ( quantity == 1 ) {
            alert('{{trans("custom.products.should-be-one")}}');
        } else {
            quantity = parseFloat( quantity ) - 1;
            $('#quantity-' + rowid).val( quantity );
            rowTotal( rowid );
        }
    } else {
        alert('{{trans("custom.products.please-select-product")}}');
    }
}


function assignValues( cvalue,  data ) {
    if ( controller != 'PurchaseOrdersController') {
        if ( data['stock_quantity'] < 1 ) {
            alert('{{trans("others.products.quantity-not-available")}}');
            return false;
        }
    }
	
	$('#hdata').data('curr', data['currency_code']);
    $('#quantity-' + cvalue).val( 1 );
    $('#price-' + cvalue).val(data['sale_price']);
    $('#pid-' + cvalue).val(cvalue); // Row Index ID
    $('#product_ids-' + cvalue).val(data['id']); // Product ID

    var product = {};
    product["product_id"] = data['id'];
    product["rowid"] = parseInt( cvalue );

    caninsert = 'no';
    cartproducts = js_global['cartproducts'];
    if ( cartproducts.length > 0 ) {
        jQuery( cartproducts ).each(function(key, val) {           
            
            if ( val.product_id ==  data['id'] ) {
                caninsert = 'no';
            } else if ( val.rowid == cvalue ) { // Which means product changing.
                js_global['cartproducts'][ cvalue ].product_id = data['id'] ; // Updating product id.
                caninsert = 'no';
            } else {
                
                caninsert = 'yes';
            }
        });
    } else { // Which means there are no products added yet.
        caninsert = 'yes';
    }

    if ( 'yes' == caninsert ) {
        js_global['cartproducts'].push( product );
    }

    if ( data['record_type'] == 'task' ) {
        $('#productselectname-' + cvalue).val( data['id'] + '_task' );
    } else if( data['record_type'] == 'expense' ) {
        $('#productselectname-' + cvalue).val( data['id'] + '_expense' );
    } else {
        $('#productselectname-' + cvalue).val( data['id'] );
    }

    $('#productname-' + cvalue).val( data['name'] );
    

    $('#tax_rate-' + cvalue).val(data['tax_rate']);
    
    $('#tax_type-' + cvalue).val(data['rate_type']);
    
    $('#discount_rate-' + cvalue).val(data['discount_rate']);
    
    $('#discount_type-' + cvalue).val(data['discount_type']);

    if ( data['excerpt'] != '' ) {
        $('#dpid-' + cvalue).val(data['excerpt']);
    } else {
        $('#dpid-' + cvalue).val(data['description']);
    }
    $('#unit-' + cvalue).val(data['measurement_unit']);
    $('#hsn-' + cvalue).val(data['hsn_sac_code']);
    $('#alert-' + cvalue).val(data['alert_quantity']);
    $('#stock_quantity-' + cvalue).val(data['stock_quantity']);
    rowTotal(cvalue);
}

function removeProd( rowid ) {
    var products = $('.product_row').length;
    
    var product_id = $('#product_ids-' + rowid).val();
    
    if ( products == 1 ) { // Which means this is the only product on the list.
        var clone = $('#product_row_' + rowid).closest('tr');
        

        clone.find( 'td input, textarea, select' ).val( '' );

        clone.find( 'input, select, textarea' ).each(function() {
            var value   = $( this ).attr('value');
            if( typeof value != 'undefined' ) {
                $( this ).attr( 'value', '' );
            }
        });

        // Description Clear.
        var clone = $('#product_desc_row_' + rowid).closest('tr');
        clone.find( 'td input, textarea, select' ).val( '' );
        clone.find( 'input, select, textarea' ).each(function() {
            var value   = $( this ).attr('value');
            if( typeof value != 'undefined' ) {
                $( this ).attr( 'value', '' );
            }
        });

        $('#productselectname-' + rowid).trigger('change');

        rowTotal(rowid);
    } else {
        
        $('#product_row_' + rowid).remove();
        $('#product_desc_row_' + rowid).remove();
    }

    // Let us remove the product id from cartitems array.
    var cartproducts = js_global['cartproducts'];
    var incart = [];
    var rowid = 0;
    var product_found = 'no';

    if ( cartproducts.length > 0 ) {
        
        cartproducts = $.grep(cartproducts, function(e){ 
             return e.product_id != product_id; 
        });
        
        js_global['cartproducts'] = cartproducts;
    }
    

    calculateTotal();
}

$('#products-row').on('click', '.removeProd1', function () {
    
    var products = $('.product_row').length;
    var rowid = $(this).closest('tr').data('rowid');
    var product_id = $('#product_ids-' + rowid).val();
    
    if ( products == 1 ) { // Which means this is the only product on the list.
        var clone = $(this).closest('tr');
		
		$(this).closest('tr').next('tr').find('td input:text, textarea').val('');

        clone.find( 'td input, textarea, select' ).val( '' );

        clone.find( 'input, select, textarea' ).each(function() {
            var value   = $( this ).attr('value');
            if( typeof value != 'undefined' ) {
                $( this ).attr( 'value', '' );
            }
        });

        rowTotal(rowid);
    } else {
        $(this).closest('tr').remove();
        $('#d' + $(this).closest('tr').find('.pdIn').attr('id')).closest('tr').remove();
    }

    // Let us remove the product id from cartitems array.
    var cartproducts = js_global['cartproducts'];
    var incart = [];
    var rowid = 0;
    var product_found = 'no';

    if ( cartproducts.length > 0 ) {
        
		cartproducts = $.grep(cartproducts, function(e){ 
			 return e.product_id != product_id; 
		});
		
        js_global['cartproducts'] = cartproducts;
    }
	
	
    calculateTotal();

    return false;
});

$('#customer_id').change( function () {
    $.ajax({
        url: '{{url('admin/search_products')}}/products',
        dataType: "json",
        method: 'post',
      data: 'customer_id='+$('#customer_id').val()+'&type=customer&row_num=1&'+d_csrf,
        success: function (data) {
            
            $('#address').val( data.address );
            $('#delivery_address').val( data.delivery_address );
            $('#currency_id').val( data.currency_id ).trigger('change');
            $('#currency_id_old').val( data.currency_id );
            $('#hdata').data('curr', data.currency_code );
			$('#hdata').data('currency_id', data.currency_id );
        }
    });
});


$('#currency_id').change(function() {
	$.ajax({
        url: '{{url('admin/search_products')}}/products',
        dataType: "json",
        method: 'post',
      data: 'currency_id='+$('#currency_id').val()+'&type=reload&row_num=1&'+d_csrf,
        success: function (data) {
            
			$('.productsrow').html( data.html );
            $('#hdata').data('curr', data.currency_code );
			$('#hdata').data('currency_id', data.currency_id );
			
			$('.sale_price').hide();
			$('.actual_price').hide();
			$('.sale_price').each(function() {
				var product_id = $(this).data('product_id');
				var sale_price = $(this).data('price');
				$('.' + product_id + '_sale_price_' + data.currency_short_code).show(); // It will show the currency price only
			});
			
			$('.actual_price').each(function() {
				var product_id = $(this).data('product_id');
				var actual_price = $(this).data('price');
				$('.' + product_id + '_actual_price_' + data.currency_short_code).show(); // It will show the currency price only
			});


            $('.product_name_select').autocomplete({
                source: function (request, response) {
                    
                    var product_ids = [];
                    $( '.product_ids').each(function() {
                        product_ids.push( $(this).val()  );
                    });
                    var newcurrency_id = $('#hdata').data('currency_id');
                    $.ajax({
                        url: '{{url('admin/search_products')}}/' + billtype,
                        dataType: "json",
                        method: 'post',
                      data: 'name_startsWith='+request.term+'&type=product_list&row_num=1&wid='+$("#ware_house_id").val()+'&'+d_csrf + '&product_ids=' +product_ids+'&currency_id=' + newcurrency_id,
                        success: function (data) {
                            response($.map(data, function (item) {
                                var product_d = item['id'];
                                
                                return {
                                    label: item['name'],
                                    value: item['name'],
                                    data: item
                                };
                            }));
                        }
                    });
                },
                autoFocus: true,
                minLength: 0,
                select: function (event, ui) {
                    var data = ui.item.data;
                    assignValues( 0,  data );
                }
            });
        }
    });
});



function returngetDetails( data ) {
	var type = data.type;
	var cart_tax = 0;
	var cart_discount = 0;
	
	$('#hdata').data('curr', data.currency_code);
	$('#hdata').data('currency_id', data.currency_id);
	
	if ( type == 'tax' ) {
		var tax = data.results;
		var rate = tax.rate;
		var rate_type = tax.rate_type;
		var tax_format = $('#tax_format').val();
		var products_amount = $('#products_amount').val();
		var total_tax = $("#total_tax").val();
		
		if ( rate > 0 ) {
			if ( 'before_tax' === tax_format ) {
				if ( 'percent' === rate_type ) {
					cart_tax = ( parseFloat( products_amount ) * parseFloat( rate ) ) / 100;
				} else {
					cart_tax = rate;
				}                    
			} else {
				var new_amount = parseFloat( products_amount ) + parseFloat( total_tax );
				if ( 'percent' === rate_type ) {
					cart_tax = ( parseFloat( new_amount ) * parseFloat( rate ) ) / 100;
				} else {
					cart_tax = rate;
				}
			}
			
			$("#additional_tax_display").html( deciFormat(cart_tax, 'yes') );
			$("#additional_tax").val( cart_tax );
		}
	}
	
	if ( type == 'discount' ) {
		var discount = data.results;
		var rate = discount.rate;
		var rate_type = discount.rate_type;
		var discount_format = $('#discount_format').val();
		var products_amount = $('#products_amount').val();
		var total_tax = $("#total_tax").val();
		
		if ( rate > 0 ) {
			if ( 'before_tax' === discount_format ) {
				if ( 'percent' === rate_type ) {
					cart_discount = ( parseFloat( products_amount ) * parseFloat( rate ) ) / 100;
				} else {
					cart_discount = rate;
				}                    
			} else {
				var new_amount = parseFloat( products_amount ) + parseFloat( total_tax );
				if ( 'percent' === rate_type ) {
					cart_discount = ( parseFloat( new_amount ) * parseFloat( rate ) ) / 100;
				} else {
					cart_discount = rate;
				}
			}
			
			$("#additional_discount_display").html( deciFormat(cart_discount, 'yes') );
			$("#additional_discount").val( cart_discount );
		}
	}
	
	var grand_total = $('#grand_total').val();
	var additional_tax = $("#additional_tax").val();
	if ( typeof( additional_tax ) === 'undefined' ) {
		additional_tax = 0;
	}
	var additional_discount = $("#additional_discount").val();
	if ( typeof( additional_discount ) === 'undefined' ) {
		additional_discount = 0;
	}
	
	var amount_payable = parseFloat( grand_total ) + parseFloat( additional_tax ) - parseFloat( additional_discount );
	
	$("#amount_payable_display").html( deciFormat( amount_payable, 'yes' ) );
}

function getDetails( id, type ) {
	$.ajax({
        url: "{{url('admin/get-details')}}",
        dataType: "json",
        method: 'post',
		data: 'id='+id+'&type='+type+'&'+d_csrf,
        success: returngetDetails
    });
}



</script>