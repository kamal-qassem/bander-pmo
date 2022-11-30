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

				$('.error').remove();

				var errors = 0;
				if ( account == '' ) {
					$('#account').after('<span class="error">{{trans("custom.invoices.messages.account")}}</a>');
					$('#account').focus();
					errors++;
				}
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
		                url: '{{url('admin/proposals/save-payment')}}',
		                dataType: "json",
		                method: 'post',
		                data: {
		                	action: action,
		                	'_token': crsf_hash,
		                	'data': getFormData( 'form_add_payment' )
		                },
		                success: function (data) {
		                	$('#loadingModal').modal('toggle');
		                   
		                    location.reload();
		                }
		            });
				}
			}
			 else {
				var errors = 0;

				if ( sub == 'sms' ) {
					var tonumber = $('#tonumber').val();
					var toname = $('#toname').val();
					var message = $('#message').val();

					if ( tonumber == '' ) {
						$('#tonumber').after('<span class="error">{{trans("custom.messages.tonumber")}}</a>');
						$('#tonumber').focus();
						errors++;
					} else if ( ! phonenumber( tonumber ) ) {
						$('#tonumber').after('<span class="error">{{trans("custom.messages.tonumber-invalid")}}</a>');
						$('#tonumber').focus();
						errors++;
					}
					if ( toname == '' ) {
						$('#toname').after('<span class="error">{{trans("custom.messages.toname")}}</a>');
						$('#toname').focus();
						errors++;
					}
					if ( message == '' ) {
						$('#message').after('<span class="error">{{trans("custom.messages.toname")}}</a>');
						$('#message').focus();
						errors++;
					}
				} else {
				var toemail = $('#toemail').val();
				var toname = $('#toname').val();
				var ccemail = $('#ccemail').val();
				var bccemail = $('#bccemail').val();
				var subject = $('#subject').val();
				
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
		                url: '{{url("admin/proposals/send")}}',
		                dataType: "json",
		                method: 'post',
		                data: {
		                	action: action,
		                	'_token': crsf_hash,
		                	'data': getFormData()
		                },
		                success: function (data) {
		                	$('#loadingModal').modal('toggle');
		                  
		                    location.reload();
		                }
		            });
				}
			}
		});

	});
	function loadEmailTemplate (invoice_id, action) {
		$('#loading_icon').show();

		jQuery.ajax({
	        url: baseurl + '/admin/proposals/mail-invoice',
	        type: 'POST',
	        data: {
	        	'_token': crsf_hash,
	        	invoice_id: invoice_id,
	        	action: action
	        },
	   
	        beforeSend: function() {
	           
	        },
	        success: function (data) {
	            $('#loading_icon').hide();
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

	$('.convertProposal').click(function() {
		var proposal_id = $(this).data('proposal_id');
		var invoice_id = $(this).data('invoice_id');
		var slug = $(this).data('slug');
		var url = $(this).data('url');
		var invoice_url = $(this).data('invoice_url');

		var str = '';
		if ( invoice_id != '' ) {
			var invurl = '';
			if ( invoice_url != '' ) {
				invurl = '&nbsp;<a href="'+invoice_url+'" target="_blank">{{trans("proposals::custom.proposals.view-now")}}</a>';
			}
			str = '<p><code>{{trans("proposals::custom.proposals.proposal-already-convert")}}</code>'+invurl+'</p>';
		}

		bootbox.confirm({
		    title: "{{trans('proposals::custom.proposals.convert-to-invoice')}}",
		    message: str + "{{trans('proposals::custom.proposals.convert-to-invoice-are-you-sure')}}",
		    buttons: {
		        cancel: {
		            label: '<i class="fa fa-times"></i> {{trans("custom.common.no")}}',
		            className: 'btn-danger'
		        },
		        confirm: {
		            label: '<i class="fa fa-check"></i> {{trans("custom.common.yes")}}',
		            className: 'btn-success'
		        }
		    },
		    callback: function (result) {
		     
		        if ( result ) {
		        	window.location = url;
		        }
		    }
		});
	});

	$('.convertQuote').click(function() {
		var proposal_id = $(this).data('proposal_id');
		var quote_id = $(this).data('quote_id');
		var slug = $(this).data('slug');
		var url = $(this).data('url');
		var invoice_url = $(this).data('invoice_url');

		var str = '';
		if ( quote_id != '' ) {
			var invurl = '';
			if ( invoice_url != '' ) {
				quourl = '&nbsp;<a href="'+invoice_url+'" target="_blank">{{trans("proposals::custom.proposals.view-now")}}</a>';
			}
			str = '<p><code>{{trans("proposals::custom.proposals.proposal-already-convert-quote")}}</code>'+quourl+'</p>';
		}

		bootbox.confirm({
		    title: "{{trans('proposals::custom.proposals.convert-to-quote')}}",
		    message: str + "{{trans('proposals::custom.proposals.convert-to-quote-are-you-sure')}}",
		    buttons: {
		        cancel: {
		            label: '<i class="fa fa-times"></i> {{trans("custom.common.no")}}',
		            className: 'btn-danger'
		        },
		        confirm: {
		            label: '<i class="fa fa-check"></i> {{trans("custom.common.yes")}}',
		            className: 'btn-success'
		        }
		    },
		    callback: function (result) {
		      
		        if ( result ) {
		        	window.location = url;
		        }
		    }
		});
	});


</script>