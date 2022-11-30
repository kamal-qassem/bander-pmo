<script>
    window.deleteButtonTrans = '{{ trans("global.app_delete_selected") }}';
    window.copyButtonTrans = '{{ trans("global.app_copy") }}';
    window.csvButtonTrans = '{{ trans("global.app_csv") }}';
    window.excelButtonTrans = '{{ trans("global.app_excel") }}';
    window.pdfButtonTrans = '{{ trans("global.app_pdf") }}';
    window.printButtonTrans = '{{ trans("global.app_print") }}';
    window.colvisButtonTrans = '{{ trans("global.app_colvis") }}';
    window.fieldrequired = '{{ trans("global.this_field_required") }}';

    window.are_you_sure = '{{ trans("custom.common.are-you-sure-delete") }}';
    window.please_select = '{{ trans("others.please-select-record") }}';
</script>

<!-- Jquery Core Js -->
<script src="{{ themes('plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap Core Js -->
<script src="{{ themes('plugins/bootstrap/js/bootstrap.js') }}"></script>

<!-- Select Plugin Js -->
<script src="{{ themes('plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>

<!-- Multi Select Plugin Js -->
<script src="{{ themes('plugins/multi-select/js/jquery.multi-select.js') }}"></script>

<!-- Slimscroll Plugin Js -->
<script src="{{ themes('plugins/jquery-slimscroll/jquery.slimscroll.js') }}"></script>

<!-- Waves Effect Plugin Js -->
<script src="{{ themes('plugins/node-waves/waves.js') }}"></script>

<!-- Custom Js -->
<script src="{{ themes('js/admin.js') }}"></script>

<!-- Canvas Scripts -->
<script src="{{ themes('js/easypiechart-data.js') }}"></script>
<script src="{{ themes('js/easypiechart.js') }}"></script>

<!-- Demo Js -->
<script src="{{ themes('js/demo.js') }}"></script>


<script src="{{ themes('plugins/jquery-validation/jquery.validate.js') }}"></script>
<!--<script src="{{ themes('js/pages/forms/form-validation.js') }}"></script>-->

<!-- Waves Effect Plugin Js -->
<script src="{{ themes('plugins/node-waves/waves.js') }}"></script>

<script src="{{ themes('plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="{{ themes('plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
<script src="{{ themes('plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>

<script src="{{ themes('js/cdn-js-files/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ themes('js/cdn-js-files/datatables/responsive.bootstrap.min.js') }}"></script>



<!-- Bootstrap Chosen Plugin Js -->
<script src="{{ themes('plugins/chosen/chosen.jquery.min.js') }}"></script>

<!-- Bootstrap Tags Input Plugin Js -->
<script src="{{ themes('plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>



<script src="{{ themes('js/cdn-js-files/jquery-ui.min.js') }}"></script>

<!-- Application scripts -->
<!-- js-cdn-files folder (Themes) -->
<script src="{{ themes('js/cdn-js-files/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ themes('js/cdn-js-files/datatables/buttons.flash.min.js') }}"></script>
<script src="{{ themes('js/cdn-js-files/datatables/jszip.min.js') }}"></script>

<script src="{{ themes('js/cdn-js-files/pdfmake.min.js') }}"></script>
<script src="{{ themes('js/cdn-js-files/vfs_fonts.js') }}"></script>


<script src="{{ themes('js/cdn-js-files/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ themes('js/cdn-js-files/datatables/buttons.print.min.js') }}"></script>
<script src="{{ themes('js/cdn-js-files/datatables/buttons.colVis.min.js') }}"></script>
<script src="{{ themes('js/cdn-js-files/datatables/dataTables.select.min.js') }}"></script>

<script src="{{ themes('plugins/sweetalert/sweetalert-dev.js') }}"></script>
<script src="{{ url('js/bootstrap-notify.min.js') }}"></script>
<script src="{{ themes('js/bootstrap3-typeahead.js') }}"></script>

<script src="{{ themes('js/cdn-js-files/datatables/bootbox.min.js') }}"></script>


<script src="{{ themes('js/main.js') }}"></script>

@include("partials.scripts")

<script>
    window._token = '{{ csrf_token() }}';
</script>


<script>
    
    $.extend(true, $.fn.dataTable.defaults, {
        "language": {
            "url": "{{url('js/cdn-js-files/datatables/i18n/')}}{{ array_key_exists(app()->getLocale(), config('app.languages')) ? config('app.languages')[app()->getLocale()] : 'English' }}.json"
        }
    });

    $(document).ready(function() {


        $(document).on('keyup', '.searchable-field', function () {
            if ( $(this).val().length >= 3 ) {
                $.ajax({
                    method: 'GET',
                    url: '{{ route("admin.mega-search") }}',
                    dataType: 'json',
                    data: {
                       'search[term]': $(this).val(),
                       'search[_type]': 'query'
                    }
                    }).done(function ( data ) {                        
                        $('#mega-search-results').remove();
                        var html = '<div id="mega-search-results">';
                        $( data.results ).each(function( index, item ) {
                            // console.log(item)
                            let markup = "<div class='searchable-link' href='" + item.url + "'>";
                            markup += "<div class='searchable-title'>" + item.model + "</div>";
                            $.each(item.fields, function(key, field) {
                                markup += "<div class='searchable-fields'>" + item.fields_formated[field] + " : " + item[field] + "</div>";
                            });
                            markup += "</div>";

                            html += markup;
                        });
                        html += '</div>'
                        
                        $('.search-bar').append( html );
                        
                    });
            }

            if ( $(this).val().length == 0 ) {
                $('#mega-search-results').remove();
            }
        });
        
       
        
    });
</script>

<script>
    $(document).ready(function () {
        $(".notifications-menu").on('click', function () {
            if (!$(this).hasClass('open')) {
                $('.notifications-menu .label-warning').hide();
                $.get('internal_notifications/read');
            }
        });


        $(".confirmbootbox").on('click', function (e) {
            e.preventDefault();
            alert('YOu called me');
            var url = $(this).data('route');
            var message = $(this).data('custommessage');
            console.log(message);
            if ( typeof(message) == 'undefined' ) {
                message = "{{trans('custom.common.are-you-sure-delete')}}";
            }

            bootbox.confirm({
                title: "{{trans('custom.common.are-you-sure')}}",
                message: message,
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
                        document.location = url;
                    }
                }
            });
        });
    });

</script>

<script type="text/javascript">
/**
 * type: info, success, danger
 */
function notifyMe( type, message ) {
    if ( type == '' ) {
        type = 'success';
    }
    if ( message == '' ) {
        message = '{{trans("custom.messages.somethiswentwrong")}}';
    }

    var title = '{{trans("custom.messages.failed")}}';
    var icon = 'glyphicon glyphicon-warning-sign';
    if ( type == 'success' ) {
        title = '{{trans("custom.messages.success")}}';
        icon = 'glyphicon glyphicon-success-sign';
    }
    if ( type == 'info' ) {
        title = '{{trans("custom.messages.info")}}';
        icon = 'glyphicon glyphicon-info-sign';
    }
    $.notify({
        // options
        title: title,
        message: message,
        icon: icon
    },{
        // settings
        type: type,
        // showProgressbar: true,
        delay: 1000,
        newest_on_top: true,
        animate: {
            enter: 'animated lightSpeedIn',
            exit: 'animated lightSpeedOut'
        }

    });
}
@if (Session::has('message'))
<?php
$message_type = getSetting('message_type', 'site_settings', 'onpage');
if ( 'notify' == $message_type ) { ?>
notifyMe("{{Session::get('status', 'info')}}", "{{ Session::get('message') }}")
<?php } if ( 'sweetalert' == $message_type ) { 
    // type: warning, error, success, info
    $type = Session::get('status', 'info');
    if ( 'danger' === $type ) {
        $type = 'error';
    }
    ?>
    swal({
            title: "{{{ Session::get('status', 'info') }}}",
            text: "{{{ Session::get('message') }}}",
            type: "{{{ $type }}}",
            timer: 1700,
            showConfirmButton: false
        });
<?php } ?>
 @endif
</script>

@yield('javascript')

@yield('footer')