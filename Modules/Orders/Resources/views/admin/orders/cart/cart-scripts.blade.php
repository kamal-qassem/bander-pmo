<script>


    
var billtype = 'products';
var d_csrf=crsf_token+'='+crsf_hash;

/**
 * id: productID
 * row: row Index
 */
function getProductDetails( id ) {
    
    $.ajax({
        url: '{{url('admin/orders/searchproduct')}}',
        dataType: "json",
        method: 'post',
        data: 'product_id='+id+'&type=product_details&row_num='+row+'&wid='+$("#ware_house_id option:selected").val()+'&'+d_csrf,
        success: function (data) {
            assignValues( row, data);            
        }
    });
}




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
        if ( 'left' === currency_position ) {
            minput = currency + minput;
        }
        if ( 'right' === currency_position ) {
            minput = minput + currency;
        }
        if ( 'left_with_space' === currency_position ) {
            minput = currency + ' ' . minput;
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

    $('.product_row').each(function () {
        var rowIndex = $(this).data('rowid');
        var rowTotal = $("#total-" + rowIndex).val();
        var rowTax = $("#tax_value-" + rowIndex).val();
        var rowDiscount = $("#discount_value-" + rowIndex).val();
        grand_total += parseFloat( rowTotal );
        total_tax += parseFloat( rowTax );
        total_discount += parseFloat( rowDiscount );
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
};

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 46 || charCode > 57)) {
        return false;
    }
    return true;
}

$('#addproduct').on('click', function () {

    var cvalue = 0;
    $('.product_row').each(function() {
        var current = $(this).data( 'rowid' );
        if( parseInt( current ) > cvalue ) {
            cvalue = current;
        }
    });
    cvalue += 1;

    var functionNum = "'" + cvalue + "'";

   
//product row
var productname = '<input type="text" class="form-control" name="product_name[]" placeholder="Enter Product name or Code" id="productname-' + cvalue + '">';

<?php
$products_selection = getSetting( 'products_selection', 'site_settings' );
if ( in_array( $products_selection, array( 'select', 'select2' ) ) ) {
    $products = getProducts();
    $select2 = '';
    if( 'select2' === $products_selection ) {
        $select2 = ' select2';
    }
    ?>
    productname = '<select class="form-control<?php echo $select2; ?>" required="required" name="product_name[]" placeholder="{{trans('custom.products.please_select')}}" id="productselectname-' + cvalue + '" onchange="getProductDetails(this.value, '+functionNum+')"><option value="">{{trans('custom.products.please_select')}}</option>';
    <?php
    if ( ! empty( $products ) ) {
        foreach ($products as $product) {
            ?>
            productname+= '<option value="{{$product->id}}">{{$product->name}}</option>'
            <?php
        }
        
    }
    ?>
    productname += '</select>';
    <?php
}
?>
var buttons = '<p><i class="fa fa-plus-circle fa-lg quantity-increase" aria-hidden="true" onclick="quantity_increase(' + functionNum + ')"></i>&nbsp;<i class="fa fa-minus-circle fa-lg quantity-decrease" aria-hidden="true" onclick="quantity_decrease(' + functionNum + ')"></i><p>';

var data = '<tr height="90px" class="product_row" data-rowid="' + cvalue + '"><td valign="top">'+productname+'</td><td valign="top"><input type="text" class="form-control req amnt" name="product_qty[]" id="quantity-' + cvalue + '" onkeypress="return isNumber(event)" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off" value="1" >' + buttons + '<input type="hidden" id="alert-' + cvalue + '" value=""  name="alert[]"> </td> <td valign="top"><input type="text" class="form-control req prc" name="product_price[]" id="price-' + cvalue + '" onkeypress="return isNumber(event)" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off"></td><td valign="top"> <input type="text" class="form-control vat" name="product_tax[]" id="tax_rate-' + cvalue + '" onkeypress="return isNumber(event)" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off"><input type="hidden" name="tax_value[]" id="tax_value-' + cvalue + '" value="0"><select name="tax_type[]" id="tax_type-' + cvalue + '" onchange="rowTotal(' + functionNum + ')"><option value="percent" onchange="rowTotal(' + functionNum + ')">{{trans('custom.common.percent')}}</option><option value="value">{{trans('custom.common.value')}}</option></select></td> <td id="tax_value_display-' + cvalue + '" class="text-center" valign="top">0</td> <td valign="top"><input type="text" class="form-control discount" name="product_discount[]" onkeypress="return isNumber(event)" id="discount_rate-' + cvalue + '" onkeyup="rowTotal(' + functionNum + ')" autocomplete="off"><input type="hidden" name="discount_value[]" id="discount_value-' + cvalue + '" value="0"><select name="discount_type[]" id="discount_type-' + cvalue + '" onchange="rowTotal(' + functionNum + ')"><option value="percent">{{trans('custom.common.percent')}}</option><option value="value">{{trans('custom.common.value')}}</option></select></td><td class="text-center" id="discount_value_display-' + cvalue + '" valign="top">0</td> <td class="text-center" valign="top"><strong><span class="ttlText" id="result-' + cvalue + '">0</span></strong></td> <td class="text-center" valign="top"><button type="button" data-rowid="' + cvalue + '" class="btn btn-danger removeProd" title="Remove" > <i class="fa fa-minus-square"></i> </button> </td><input type="hidden" name="taxa[]" id="taxa-' + cvalue + '" value="0"><input type="hidden" name="disca[]" id="disca-' + cvalue + '" value="0"><input type="hidden" class="ttInput" name="product_subtotal[]" id="total-' + cvalue + '" value="0"> <input type="hidden" class="pdIn" name="pid[]" id="pid-' + cvalue + '" value="0"> <input type="hidden" name="unit[]" id="unit-' + cvalue + '" value=""> <input type="hidden" name="hsn[]" id="hsn-' + cvalue + '" value=""> <input type="hidden" name="product_ids[]" id="product_ids-' + cvalue + '" value="" class="product_ids"></tr><tr><td colspan="9"><textarea class="form-control"  id="dpid-' + cvalue + '" name="product_description[]" placeholder="Enter Product description" autocomplete="off"></textarea><input type="hidden" name="alert[]" id="alert-' + cvalue + '" value="0"><input type="hidden" name="stock_quantity[]" id="stock_quantity-' + cvalue + '" value="0"></td></tr>';
    //ajax request
    // $('#saman-row').append(data);
    $('tr.last-item-row').before(data);

    row = cvalue;
    $('#productname-' + cvalue).autocomplete({
        source: function (request, response) {
            var product_ids = [];
            $( '.product_ids').each(function() {
                product_ids.push( $(this).val()  );
            });
            // console.log( product_ids );

            $.ajax({
                url: '{{url('admin/search_products')}}/' + billtype,
                dataType: "json",
                method: 'post',
                data: 'name_startsWith='+request.term+'&type=product_list&row_num='+row+'&wid='+$("#ware_house_id option:selected").val()+'&'+d_csrf + '&product_ids=' + product_ids,
                success: function (data) {
                    response($.map(data, function (item) {
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
            assignValues( cvalue,  data );
        },
        create: function (e) {
            $(this).prev('.ui-helper-hidden-accessible').remove();
        }
    });


    //var sideh2 = document.getElementById('rough').scrollHeight;
    //var opx3 = sideh2 + 50;
    //document.getElementById('rough').style.height = opx3 + "px";
});

function quantity_increase( rowid ) {
   var quantity = $('#quantity-' + rowid).val();
    var product_id = $('#product_ids-' + rowid).val();
    if ( product_id != '' ) {
        quantity = parseFloat( quantity ) + 1;
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
    $('#quantity-' + cvalue).val( 1 );
    $('#price-' + cvalue).val(data['sale_price']);
    $('#pid-' + cvalue).val(cvalue); // Row Index ID
    $('#product_ids-' + cvalue).val(data['id']); // Product ID

    var product = {};
    product["product_id"] = data['id'];
    product["rowid"] = cvalue;

    js_global['cartproducts'].push( product );

    // If admin enabled dropdown select box.
    $('#productselectname-' + cvalue).val( data['id'] );

    $('#productname-' + cvalue).val( data['name'] );
    

    $('#tax_rate-' + cvalue).val(data['tax_rate']);
    //$('#tax_value-0').val(data['tax_value']); // Hidden
    //$('#tax_value_display-0').html(data['tax_value']);
    $('#tax_type-' + cvalue).val(data['rate_type']);
    
    $('#discount_rate-' + cvalue).val(data['discount_rate']);
    //$('#discount_value-0').val(data['discount_value']); // Hidden
    //$('#discount_value_display-0').html(data['discount_value']);
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

$('#products-row').on('click', '.removeProd1', function () {
    
    // alert('removeProd');
    var products = $('.product_row').length;
    var rowid = $(this).closest('tr').data('rowid');
    var product_id = $('#pid-' + rowid).val();
    
    if ( products == 1 ) { // Which means this is the only product on the list.
        var clone = $(this).closest('tr');

        clone.find( 'td input:text, textarea' ).val( '' );

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
        jQuery( cartproducts ).each(function(key, val) {
            if ( val.product_id ==  product_id ) {
                cartproducts = cartproducts.slice(key, 1);
            }
        });
        js_global['cartproducts'] = cartproducts;
    }
    
    calculateTotal();

    return false;
});

var presentvalue = 0;
function quantityfield_previous( val ) {
    presentvalue = val;
	
}

function quantityfield( obj ) {
    
    var quantity = $(obj).val();
    var stock_quantity = $(obj).data('stock_quantity');
    var slug = $(obj).data('slug');

    //console.log(stock_quantity + '##' + quantity + '##' + presentvalue);
    if ( stock_quantity < quantity ) {
        if ( confirm("Maxinum quantity available for this product is " + stock_quantity + ". Do you want to continue with this quantity " + stock_quantity + "?" ) ) {
            $(obj).val( stock_quantity );
            quantity_increase_new( slug, 'no' );
        } else {
            $(obj).val( presentvalue );
        }
    }

}

</script>