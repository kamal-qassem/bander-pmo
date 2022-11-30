<script src="{{ url('adminlte/plugins/ckeditor/ckeditor.js') }}"></script>

<script type="text/javascript">
	function modalForm( action, id ) {
		$('#loadingModal #content').html('');
		$('#loading_icon').show();

		$('#loadingModal').modal();

		loadFormTemplate( action, id );
	}

	$('.modalForm').click(function() {
		
		var action = $(this).data('action');
		var id = $(this).data('id');

		$('#loadingModal #content').html('');
		$('#loading_icon').show();

		$('#loadingModal').modal();

		loadFormTemplate( action, id );			
	});

	function loadFormTemplate( action, id ) {
		$('#loading_icon').show();

		jQuery.ajax({
	        url: '{{route("admin.home.load-modal")}}',
	        type: 'POST',
	        data: {
	        	'_token': crsf_hash,
	        	id: id,
	        	action: action,
	        	type: 'masterproduct'
	        },
	        
	        beforeSend: function() {
	            
	        },
	        success: function (data) {
	            $('#loading_icon').hide();
	            $('#loadingModal #content').html( data );
	            
	            $(".select2").select2();
	        },
	        error: function (data) {
	            $("#notify .message").html("<strong>" + data.status + "</strong>: " + data.message);
	            $("#notify").removeClass("alert-success").addClass("alert-danger").fadeIn();
	            $("html, body").scrollTop($("body").offset().top);
	        }
	    });
	}
</script>