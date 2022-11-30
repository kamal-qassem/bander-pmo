<link href="{{ url('css/cdn-styles-css/bootstrap/0.2.0/bootstrap-notify.min.css') }}" rel="stylesheet">
<script src="{{ url('js/bootstrap-notify.min.js') }}"></script>
<script type="text/javascript">
$('#loadingModal').modal('toggle');
notifyMe("{{$status}}", "{{$message}}")
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
        showProgressbar: true,
        delay: 3000,
        newest_on_top: true,
        animate: {
            enter: 'animated lightSpeedIn',
            exit: 'animated lightSpeedOut'
        }

    });
}
</script>