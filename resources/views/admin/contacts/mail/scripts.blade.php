<script src="{{ url('adminlte/plugins/ckeditor/adapters/jquery.js') }}"></script>
<script type="text/javascript">
	var token_string = crsf_token + '=' + crsf_hash;

	function sendMail( action, id, selectedid )
	{
		var loadtemplate = true;
		
		$('#loadingModal').modal();
		$("#loadingModal").draggable({
		      handle: ".modal-header"
		 });

		if ( loadtemplate ) {
			$('#loading_icon').show();
			$('#loadingModal #content').html('');
			loadEmailTemplate( id, action, selectedid );
		} else {
			$('#loadingModal').modal('toggle');
		}
	}

	$(document).ready(function () {
		var $modal = $('#ajax-modal');
		var sysrender = $('#application_ajaxrender');
		var invoice_id = $('#invoice_id').val();



		$('.sendBill').click(function(){
			var action = $(this).data('action');
			var invoice_id = $(this).data('invoice_id');
			
			var loadtemplate = true;
			if ( action == 'make-payment-pay' ) {
				var paid_amount = $(this).data('paid_amount');
				var payable_amount = $(this).data('payable_amount');
			
				if ( paid_amount >= payable_amount ) {
					loadtemplate = false;
					if( confirm( '{{trans("others.invoices.already-paid-proceed")}}' ) ) {
						loadtemplate = true;
					}
				}
			}
			if ( loadtemplate ) {
				$('#loading_icon').show();
				$('#loadingModal #content').html('');
				loadEmailTemplate( invoice_id, action );
			} else {
				$('#loadingModal').modal('toggle');
			}
		});

		

		function getFormData( formid ){
    		if ( typeof( formid ) == 'undefined' ) {
    			formid = 'email_form';
    		}
    		var unindexed_array = $( '#' + formid ).serializeArray();
		    var indexed_array = {};
		    console.log( unindexed_array );
		    $.map(unindexed_array, function(n, i){
		        indexed_array[n['name']] = n['value'];
		    });

		    return indexed_array;
		}


		$('#mailSend').click(function() {
			var contact_id = $('#contact_id').val();
			var action = $('#action').val();
			
			var toemail = $('#toemail').val();
			var toname = $('#toname').val();
			var ccemail = $('#ccemail').val();
			var bccemail = $('#bccemail').val();
			var subject = $('#subject').val();

			var errors = 0;
			if ( toemail == '' ) {
				$('#toemail').after('<span class="error">{{trans("custom.invoices.messages.toemail")}}</a>');
				$('#toemail').focus();
				errors++;
			}
			
			if ( errors == 0 ) {
				$.ajax({
	                url: '{{route("admin.contacts.send-email")}}',
	                dataType: "json",
	                method: 'post',
	                data: {
	                	action: action,
	                	'_token': crsf_hash,
	                	'data': getFormData()
	                },
	                success: function (data) {
	                	$('#loadingModal').modal('toggle');
	                    notifyMe( 'success', '{{trans( "custom.messages.mailsent" )}}');
	                }
	            });
			}
			
		});

	});
	function loadEmailTemplate (id, action, selectedid) {
		$('#loading_icon').show();

		jQuery.ajax({
	        url: '{{route("admin.home.load-modal")}}',
	        type: 'POST',
	        data: {
	        	'_token': crsf_hash,
	        	id: id,
	        	action: action,
	        	selectedid: selectedid
	        },
	        //dataType: 'json',
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
</script>