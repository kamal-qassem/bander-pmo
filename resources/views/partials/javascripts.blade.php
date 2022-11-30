<script>
    window.deleteButtonTrans = '{{ trans("global.app_delete_selected") }}';
    window.copyButtonTrans = '{{ trans("global.app_copy") }}';
    window.csvButtonTrans = '{{ trans("global.app_csv") }}';
    window.excelButtonTrans = '{{ trans("global.app_excel") }}';
    window.pdfButtonTrans = '{{ trans("global.app_pdf") }}';
    window.printButtonTrans = '{{ trans("global.app_print") }}';
    window.colvisButtonTrans = '{{ trans("global.app_colvis") }}';

    window.are_you_sure = '{{ trans("custom.common.are-you-sure-delete") }}';
    window.please_select = '{{ trans("others.please-select-record") }}';
    window.data_filtered = '{{trans("others.data-filtered")}}';
    window.are_you_sure_duplicate = '{{ trans("custom.common.are-you-sure-duplicate") }}';
</script>

<script src="{{ url('js/cdn-js-files/jquery-1.11.3.min.js') }}"></script>

<script src="{{ url('js/cdn-js-files/datatables/jquery.dataTables.min.js') }}"></script>

<script src="{{ url('js/cdn-js-files/datatables/dataTables.buttons.min.js') }}"></script>

<script src="{{ url('js/cdn-js-files/datatables/buttons.flash.min.js') }}"></script>

<script src="{{ url('js/cdn-js-files/jszip.min.js') }}"></script>

<script src="{{ url('js/cdn-js-files/pdfmake.min.js') }}"></script>
<script src="{{ url('js/cdn-js-files/vfs_fonts.js') }}"></script>

<!-- datatables -->
<script src="{{ url('js/cdn-js-files/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/cdn-js-files/datatables/buttons.print.min.js') }}"></script>
<script src="{{ url('js/cdn-js-files/datatables/buttons.colVis.min.js') }}"></script>
<script src="{{ url('js/cdn-js-files/datatables/dataTables.select.min.js') }}"></script>
<script src="{{ url('js/cdn-js-files/datatables/dataTables.responsive.min.js') }}"></script>
<!--end datatables -->


<script src="{{ url('js/cdn-js-files/jquery-ui.min.js') }}"></script>
<script src="{{ url('adminlte/js/bootstrap.min.js') }}"></script>
<script src="{{ url('adminlte/js') }}/select2.full.min.js"></script>
<script src="{{ url('adminlte/js') }}/main.js"></script>

<script src="{{ url('js/select2-tab-fix.js') }}"></script>



<script src="{{ url('adminlte/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/fastclick/fastclick.js') }}"></script>
<script src="{{ url('adminlte/js/app.min.js') }}"></script>
<script src="{{ url('js/bootstrap-notify.min.js') }}"></script>

<script src="{{ url('js/cdn-js-files/bootbox.min.js') }}"></script>

<script src="{{ url('js/sweetalert-dev.js') }}"></script>

<script type="text/javascript" src="{{asset('slick/slick.min.js')}}"></script>

<script>
    window._token = '{{ csrf_token() }}';
</script>
<script>
    $.extend(true, $.fn.dataTable.defaults, {
        "language": {
            "url": "{{url('js/cdn-js-files/datatables/i18n')}}/{{ array_key_exists(app()->getLocale(), config('app.languages')) ? config('app.languages')[app()->getLocale()] : 'English' }}.json"
        }
    });

    $(document).ready(function() {
        $('.searchable-field').select2({
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("admin.mega-search") }}',
                dataType: 'json',
                type: "GET",
                delay: 200,
                data: function (term) {
                    return {
                        search: term
                    };
                },
                results: function (data) {
                    return {
                        data
                    };
                }
            },
            escapeMarkup: function (markup) { return markup; },
            templateResult: formatItem,
            templateSelection: formatItemSelection,
            placeholder : 'Search...'

        });
        function formatItem (item) {
            if (item.loading) {
                return 'Searching...';
            }
            let markup = "<div class='searchable-link' href='" + item.url + "'>";
            markup += "<div class='searchable-title'>" + item.model + "</div>";
            $.each(item.fields, function(key, field) {
                markup += "<div class='searchable-fields'>" + item.fields_formated[field] + " : " + item[field] + "</div>";
            });
            markup += "</div>";

            return markup;
        }

        function formatItemSelection (item) {
            if (!item.model) {
                return 'Search...';
            }
            return item.model;
        }
        $(document).delegate('.searchable-link', 'click', function() {
            let url = $(this).attr('href');
            window.location = url;
        });
    });


</script>

<script>
    $(function(){
        /** add active class and stay opened when selected */
        var url = window.location;

        // for sidebar menu entirely but not cover treeview
        $('ul.sidebar-menu a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');

        $('ul.treeview-menu a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');

        // for treeview
        $('ul.treeview-menu a').filter(function() {
             return this.href == url;
        }).parentsUntil('.sidebar-menu > .treeview-menu').addClass('menu-open').css('display', 'block');
    });
</script>

<style>
    .searchable-title {
        font-weight: bold;
    }
    .searchable-fields {
        padding-left:5px;
    }
    .searchable-link {
        padding:0 5px 0 5px;
    }
    .searchable-link:hover   {
        cursor: pointer;
        background: #eaeaea;
    }
    .select2-results__option {
        padding-left: 0px;
        padding-right: 0px;
    }
</style>

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
    // type: success, error, warning, info, question
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

 /**
 * List of all the available skins
 *
 * @type Array
 */
var mySkins = [
    'skin-blue',
    'skin-black',
    'skin-red',
    'skin-yellow',
    'skin-purple',
    'skin-green',
    'skin-blue-light',
    'skin-black-light',
    'skin-red-light',
    'skin-yellow-light',
    'skin-purple-light',
    'skin-green-light'
]

<?php
$theme = getSetting( 'theme_color', 'site_settings');
if ( empty( $theme ) ) {
    $theme = 'skin-blue';
}
?>


 /**
 * Replaces the old skin with the new skin
 * @param String cls the new skin class
 * @returns Boolean false to prevent link's default action
 */
function changeSkin(cls) {
    $.each(mySkins, function (i) {
        $('body').removeClass(mySkins[i])
    })

    $('body').addClass(cls)
    store('skin', cls)
    return false
}

/**
 * Store a new settings in the browser
 *
 * @param String name Name of the setting
 * @param String val Value of the setting
 * @returns void
 */
function store(name, val) {
    if (typeof (Storage) !== 'undefined') {
        localStorage.setItem(name, val)
    } else {
        window.alert('Please use a modern browser to properly view this template!')
    }
}


function confirmbootbox() {
    alert('You clicked me');
    $( ".confirmbootbox" ).trigger( "click" );
}

</script>


@yield('javascript')

@yield('footer')
