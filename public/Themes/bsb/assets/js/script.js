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
	    format: "{{ getDateFormatNew() }}",
	    locale: "{{ App::getLocale() }}",
	});

});