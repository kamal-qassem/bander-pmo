<script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>

<script type="text/javascript">
	$(document).ready(function () {
		//Bootstrap datepicker plugin
		$('.select2').selectpicker({
		    liveSearch: true
		});

	});

	
	
	$(function(){
		moment.updateLocale('{{ App::getLocale() }}', {
		    week: { dow: 1 } // Monday is the first day of the week
		});
		
		$('.date').datetimepicker({
		    format: "{{getDateFormatNew()}}",
		    locale: "{{ App::getLocale() }}"
		});
	});

	$(function () {
	    $('.formvalidation').validate({
	        ignore: ".date",
	        highlight: function (input) {
	            $(input).parents('.form-line').addClass('error');
	        },
	        
	        unhighlight: function (element, errorClass, validClass) {
	            var elem = $(element);
	            elem.removeClass(errorClass);
	        },
	        errorPlacement: function (error, element) {
	            $(element).parents('.form-group').append(error);
	        }
	    });

	    $('form.select').on('change', function() {  // when the value changes
	        $(this).valid(); // trigger validation on this element
	    });
	});
</script>

<link rel="stylesheet"
  href="{{ url('adminlte/css') }}/select2.min.css"/>
<script src="{{ url('adminlte/js') }}/select2.full.min.js"></script>
<script>
$('.select2').select2();
</script>
