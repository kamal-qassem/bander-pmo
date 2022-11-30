<script type="text/javascript">
function printItem( elem ) {
    var mywindow = window.open('', 'PRINT', 'height=400,width=600' );
    mywindow.document.write('<html><head><title>' + document.title  + '</title>' );
    mywindow.document.write('</head><body >' );
    mywindow.document.write('<h1>' + document.title  + '</h1>' );
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>' );

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}
</script>

<script src="{{ url('adminlte/plugins/ckeditor/adapters/jquery.js') }}"></script>
<script type="text/javascript">
	var token_string = crsf_token + '=' + crsf_hash;

	$(document).ready(function () {
		var $modal = $('#ajax-modal');
		var sysrender = $('#application_ajaxrender');
		var invoice_id = $('#invoice_id').val();

		

		$('.sendBill').click(function(){
			var action = $(this).data('action');
			var invoice_id = $(this).data('invoice_id');
			$('#loadingModal #content').html(''); // Let us clear if any previous data!

			$('#loading_icon').show();
			loadEmailTemplate( invoice_id, action );
		});

		$("#loadingModal").draggable({
		      handle: ".modal-header"
		 });

		function getFormData( formid ){
    		if ( typeof( formid ) == 'undefined' ) {
    			formid = 'email_form';
    		}
    		var unindexed_array = $( '#' + formid ).serializeArray();
		    var indexed_array = {};
		   
		    $.map(unindexed_array, function(n, i){
		        indexed_array[n['name']] = n['value'];
		    });

		    return indexed_array;
		}


		$('#invoiceSend').click(function() {
			var invoice_id = $('#invoice_id').val();
			var action = $('#action').val();
			var sub = $('#sub').val();
			$('.error').remove();

			if ( 'make-payment-pay' == action ) {				
				$('#invoiceSend').html('{{trans("custom.common.save")}}');
				var account = $('#account').val();
				var date = $('#date').val();
				var description = $('#description').val();
				var amount = $('#amount').val();
				var category = $('#category').val();
				var paymethod = $('#paymethod').val();
				var transaction_id = $('#transaction_id').val();

				

				var errors = 0;
				@if ( isPluginActive('account') )
				if ( account == '' ) {
					$('#account').after('<span class="error">{{trans("custom.invoices.messages.account")}}</a>');
					$('#account').focus();
					errors++;
				}
				@endif
				if ( date == '' ) {
					$('#date').after('<span class="error">{{trans("custom.invoices.messages.date")}}</a>');
					$('#date').focus();
					errors++;
				}
				if ( amount == '' ) {
					$('#amount').after('<span class="error">{{trans("custom.invoices.messages.amount")}}</a>');
					$('#amount').focus();
					errors++;
				}
				if ( category == '' ) {
					$('#category').after('<span class="error">{{trans("custom.invoices.messages.category")}}</a>');
					$('#category').focus();
					errors++;
				}
				if ( paymethod == '' ) {
					$('#paymethod').after('<span class="error">{{trans("custom.invoices.messages.paymethod")}}</a>');
					$('#paymethod').focus();
					errors++;
				}
				if ( transaction_id == '' ) {
					$('#transaction_id').after('<span class="error">{{trans("custom.invoices.messages.transaction_id")}}</a>');
					$('#transaction_id').focus();
					errors++;
				}

				if ( errors == 0 ) {
					$.ajax({
		                url: '{{url('admin/purchase_orders/save-payment')}}',
		                dataType: "json",
		                method: 'post',
		                data: {
		                	action: action,
		                	'_token': crsf_hash,
		                	'data': getFormData( 'form_add_payment' )
		                },
		                success: function (data) {
		                	if($.isEmptyObject(data.error)){
			                	$('#loadingModal').modal('toggle');
			                    
			                    location.reload();
		               	 	} else {
		               	 		printErrorMsg(data.error);
		               	 	}
		                }
		            });
				}
			} else {
				if ( sub == 'sms' ) {
					var tonumber = $('#tonumber').val();
					var toname = $('#toname').val();
					var message = $('#message').val();

					var errors = 0;
					if ( tonumber == '' ) {
						$('#tonumber').after('<span class="error">{{trans("custom.invoices.messages.tonumber")}}</a>');
						$('#tonumber').focus();
						errors++;
					}else if ( ! phonenumber( tonumber ) ) {
						$('#tonumber').after('<span class="error">{{trans("custom.messages.tonumber-invalid")}}</a>');
						$('#tonumber').focus();
						errors++;
					}
					if ( toname == '' ) {
						$('#toname').after('<span class="error">{{trans("custom.invoices.messages.toname")}}</a>');
						$('#toname').focus();
						errors++;
					}
					if ( message == '' ) {
						$('#message').after('<span class="error">{{trans("custom.invoices.messages.message")}}</a>');
						$('#message').focus();
						errors++;
					}
				} else {
					var toemail = $('#toemail').val();
					var toname = $('#toname').val();
					var ccemail = $('#ccemail').val();
					var bccemail = $('#bccemail').val();
					var subject = $('#subject').val();
					var message = $('#message').val();

					var errors = 0;
					if ( toemail == '' ) {
						$('#toemail').after('<span class="error">{{trans("custom.invoices.messages.toemail")}}</a>');
						$('#toemail').focus();
						errors++;
					} else if( ! isEmail( toemail ) ) {
						$('#toemail').after('<span class="error">{{trans("custom.messages.email-notvalid")}}</a>');
						$('#toemail').focus();
						errors++;
					}

					if ( toname == '' ) {
						$('#toname').after('<span class="error">{{trans("custom.invoices.messages.toname")}}</a>');
						$('#toname').focus();
						errors++;
					}

					if ( ccemail != '' && ! isEmail( ccemail ) ) {
						$('#ccemail').after('<span class="error">{{trans("custom.messages.email-notvalid")}}</a>');
						$('#ccemail').focus();
						errors++;
					}
					if ( bccemail != '' && ! isEmail( bccemail ) ) {
						$('#bccemail').after('<span class="error">{{trans("custom.messages.email-notvalid")}}</a>');
						$('#bccemail').focus();
						errors++;
					}

					if ( subject == '' ) {
						$('#subject').after('<span class="error">{{trans("custom.invoices.messages.subject")}}</a>');
						$('#subject').focus();
						errors++;
					}

					if ( message == '' ) {
						$('#message').after('<span class="error">{{trans("custom.invoices.messages.message")}}</a>');
						$('#message').focus();
						errors++;
					}
				}
				
				if ( errors == 0 ) {
					$.ajax({
		                url: '{{url("admin/purchase_orders/send")}}',
		                dataType: "json",
		                method: 'post',
		                data: {
		                	action: action,
		                	'_token': crsf_hash,
		                	'data': getFormData()
		                },
		                success: function (data) {
		                	if($.isEmptyObject(data.error)){
		                		$('#loadingModal').modal('toggle');
		                    	
		                    	location.reload();
		                	} else {
		                		printErrorMsg(data.error);
		                	}
		                }
		            });
				}
			}
		});

	});
function printErrorMsg (msg) {
      $("#message_bag").find("ul").html('');
      $("#message_bag").css('display','block');
      $("#message_bag").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $("#message_bag").find("ul").append('<li>'+value+'</li>');
      });
  }

	function loadEmailTemplate (invoice_id, action) {
		$('#loading_icon').show();

		jQuery.ajax({
	        url: baseurl + '/admin/purchase_orders/mail-invoice',
	        type: 'POST',
	        data: {
	        	'_token': crsf_hash,
	        	invoice_id: invoice_id,
	        	action: action
	        },
	        //dataType: 'json',
	        beforeSend: function() {
	            
	        },
	        success: function (data) {
	            $('#loading_icon').hide();
	            $('#loadingModal #content').html(''); // Let us clear if any previous data!
	            $('#loadingModal #content').html( data );
	            $('.editor').ckeditor();
	        },
	        error: function (data) {
	            $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
	            $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
	            $("html, body").scrollTop($("body").offset().top);
	        }
	    });
	}

</script>