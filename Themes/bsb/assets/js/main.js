$(document).ready(function () {

    var handleCheckboxes = function (html, rowIndex, colIndex, cellNode) {
        var $cellNode = $(cellNode);
        var $check = $cellNode.find(':checked');
        return ($check.length) ? ($check.val() == 1 ? 'Yes' : 'No') : $cellNode.text();
    };

    var activeSub = $(document).find('.active-sub');
    if (activeSub.length > 0) {
        activeSub.parent().show();
        activeSub.parent().parent().find('.arrow').addClass('open');
        activeSub.parent().parent().addClass('open');
    }
    window.dtDefaultOptions = {
        retrieve: true,
        responsive: true,
        dom: 'lBfrtip<"actions">',
        columnDefs: [],
        "iDisplayLength": 10,
        "aaSorting": [],
        buttons: [
            {
                extend: 'copy',
                text: window.copyButtonTrans,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text: window.csvButtonTrans,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text: window.excelButtonTrans,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: window.pdfButtonTrans,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: window.printButtonTrans,
                exportOptions: {
                    columns: ':visible'
                }
            },
            /*{
                extend: 'colvis',
                text: window.colvisButtonTrans,
                exportOptions: {
                    columns: ':visible'
                }
            },*/
        ]
    };
    $('.datatable').each(function () {
        if ($(this).hasClass('dt-select')) {
            window.dtDefaultOptions.select = {
                style: 'multi',
                selector: 'td:first-child'
            };

            window.dtDefaultOptions.columnDefs.push({
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            });
        }
        $(this).attr("style","width:100%");
        $(this).dataTable(window.dtDefaultOptions);
    });
    $(document).on( 'init.dt', function ( e, settings ) {
        if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
            $('.datatable, .ajaxTable').siblings('.actions').html('<a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a>');
        }
    });

    $(document).on('click', '.js-delete-selected', function () {
        var ids = [];

        $(this).closest('.actions').siblings('.datatable, .ajaxTable').find('tbody tr.selected').each(function () {
            // console.log("selected", $(this).data('entry-id'));
            ids.push($(this).data('entry-id'));
        });

        //console.log(ids);
        if ( ids.length == 0 ) {
            alert( please_select );
            return false;
        }

        if (confirm( are_you_sure )) {
            var ids = [];

            $(this).closest('.actions').siblings('.datatable, .ajaxTable').find('tbody tr.selected').each(function () {
                // console.log("selected", $(this).data('entry-id'));
                ids.push($(this).data('entry-id'));
            });

            $.ajax({
                method: 'POST',
                url: $(this).attr('href'),
                data: {
                    _token: _token,
                    ids: ids
                }
            }).done(function () {
                location.reload();
            });
        }

        return false;
    });
    
    $(document).on('click', '#select-all', function () {
        var selected = $(this).is(':checked');

        $(this).closest('table.datatable, table.ajaxTable').find('td:first-child').each(function () {
            if (selected != $(this).closest('tr').hasClass('selected')) {
                $(this).click();
            }
        });
    });

    $('.mass').click(function () {
        if ($(this).is(":checked")) {
            $('.single').each(function () {
                if ($(this).is(":checked") == false) {
                    $(this).click();
                }
            });
        } else {
            $('.single').each(function () {
                if ($(this).is(":checked") == true) {
                    $(this).click();
                }
            });
        }
    });

    $('.page-sidebar').on('click', 'li > a', function (e) {

        if ($('body').hasClass('page-sidebar-closed') && $(this).parent('li').parent('.page-sidebar-menu').size() === 1) {
            return;
        }

        var hasSubMenu = $(this).next().hasClass('sub-menu');

        if ($(this).next().hasClass('sub-menu always-open')) {
            return;
        }

        var parent = $(this).parent().parent();
        var the = $(this);
        var menu = $('.page-sidebar-menu');
        var sub = $(this).next();

        var autoScroll = menu.data("auto-scroll");
        var slideSpeed = parseInt(menu.data("slide-speed"));
        var keepExpand = menu.data("keep-expanded");

        if (keepExpand !== true) {
            parent.children('li.open').children('a').children('.arrow').removeClass('open');
            parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed);
            parent.children('li.open').removeClass('open');
        }

        var slideOffeset = -200;

        if (sub.is(":visible")) {
            $('.arrow', $(this)).removeClass("open");
            $(this).parent().removeClass("open");
            sub.slideUp(slideSpeed, function () {
                if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                    if ($('body').hasClass('page-sidebar-fixed')) {
                        menu.slimScroll({
                            'scrollTo': (the.position()).top
                        });
                    }
                }
            });
        } else if (hasSubMenu) {
            $('.arrow', $(this)).addClass("open");
            $(this).parent().addClass("open");
            sub.slideDown(slideSpeed, function () {
                if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                    if ($('body').hasClass('page-sidebar-fixed')) {
                        menu.slimScroll({
                            'scrollTo': (the.position()).top
                        });
                    }
                }
            });
        }
        if (hasSubMenu == true || $(this).attr('href') == '#') {
            e.preventDefault();
        }
    });

});

function processAjaxTables() {
    $('.ajaxTable').each(function () {
        window.dtDefaultOptions.processing = true;
        window.dtDefaultOptions.serverSide = true;
        if ($(this).hasClass('dt-select')) {
            window.dtDefaultOptions.select = {
                style: 'multi',
                selector: 'td:first-child'
            };

            window.dtDefaultOptions.columnDefs.push({
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            });
        }
        $(this).DataTable(window.dtDefaultOptions);
        if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
            $(this).siblings('.actions').html('<a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a>');
        }
    });

}

window.dtDefaultOptionsNew = {
    retrieve: true,
    responsive: true,
    dom: 'lBfrtip<"actions">',
    columnDefs: [],
    "iDisplayLength": 10,
    "aaSorting": [],
    ajax:{
        'url' : '',
        'data' : ''
    },
    buttons: [
        {
            extend: 'copy',
            text: window.copyButtonTrans,
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            extend: 'csv',
            text: window.csvButtonTrans,
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            extend: 'excel',
            text: window.excelButtonTrans,
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            extend: 'pdf',
            text: window.pdfButtonTrans,
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            extend: 'print',
            text: window.printButtonTrans,
            exportOptions: {
                columns: ':visible'
            }
        },
        /*{
            extend: 'colvis',
            text: window.colvisButtonTrans,
            exportOptions: {
                columns: ':visible'
            }
        },*/
    ]
};
var ajaxTableNew;

function processAjaxTablesNew() {
    $('.ajaxTable').each(function () {
        $(this).addClass('display responsive nowrap');
        
        window.dtDefaultOptionsNew.processing = true;
        window.dtDefaultOptionsNew.serverSide = true;
        if ($(this).hasClass('dt-select')) {
            window.dtDefaultOptionsNew.select = {
                style: 'multi',
                selector: 'td:first-child'
            };

            window.dtDefaultOptionsNew.columnDefs.push({
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            });
        }

        window.dtDefaultOptionsNew.ajax.data = function(d) {
            d.date_filter = $('#date_filter').val();
            d.date_type = $('#date_type').val();
            d.paymentstatus = $('#paymentstatus').val();
            d.status = $('#status_id_filter').val();
            d.customer = $('#customer').val();
            d.supplier     = $('#supplier').val();
            
            d.currency_id = $('#currency_id_filter').val();

            d.contact_type = $('#contact_type_id_filter').val();
            d.project_type = $('#project_type_id_filter').val();
            d.country_id   = $('#country_id_filter').val();
            d.group_id     = $('#group_id_filter').val();

            d.priority      = $('#priority').val();
            d.projectStatus = $('#project_status_id_filter').val();
            
        };
        ajaxTableNew = $(this).DataTable(window.dtDefaultOptionsNew);
        if (typeof window.route_mass_crud_entries_destroy != 'undefined') {
            $(this).siblings('.actions').html('<a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">'+window.deleteButtonTrans+'</a>');
        }
    });
}

$('#search-form').on('submit', function(e) {
    ajaxTableNew.draw();
    e.preventDefault();
});


function summarypaymentstatus(field, val, type) {
    $(this).hide();

    if ( field == 'paymentstatus' ) {
        $('#paymentstatus_'+val+'_loader_' + type).html('<img src="'+baseurl+'/images/loading-small-small.gif">');
        $('#paymentstatus').val( val ).trigger('change');

        //$('.canvas').collapse('toggle');

    }

     if ( field == 'paymentstatus' ) {
        $('#paymentstatus_'+val+'_loader_' + type).html('<img src="'+baseurl+'/images/loading-small-small.gif">');
        $('#paymentstatus').val( val ).trigger('change');

        //$('.canvas').collapse('toggle');

    }
    ajaxTableNew.draw();

    //notifyMe('success', window.data_filtered);

    setTimeout(function(){ 
        $('#paymentstatus_'+val+'_loader_' + type).html('');

        $('.canvas').removeClass('collapse in');
        $('.canvas').addClass('collapse');
     }, 1000);

    $(this).show();
}

function summarypriority(field, val, type) {
    $(this).hide();

    if ( field == 'priority' ) {
        $('#priority_'+val+'_loader_' + type).html('<img src="'+baseurl+'/images/loading-small-small.gif">');
        $('#priority').val( val ).trigger('change');

        //$('.canvas').collapse('toggle');

    }

     if ( field == 'priority' ) {
        $('#priority_'+val+'_loader_' + type).html('<img src="'+baseurl+'/images/loading-small-small.gif">');
        $('#priority').val( val ).trigger('change');

        //$('.canvas').collapse('toggle');

    }
    ajaxTableNew.draw();

    //notifyMe('success', window.data_filtered);

    setTimeout(function(){ 
        $('#priority_'+val+'_loader_' + type).html('');

        $('.canvas').removeClass('collapse in');
        $('.canvas').addClass('collapse');
     }, 1000);

    $(this).show();
}

function summarystatus(field, val, type) {
    $(this).hide();

    if ( field == 'status' ) {
        $('#status_'+val+'_loader_' + type).html('<img src="'+baseurl+'/images/loading-small-small.gif">');
        $('#status').val( val ).trigger('change');

        //$('.canvas').collapse('toggle');

    }

     if ( field == 'status' ) {
        $('#status_'+val+'_loader_' + type).html('<img src="'+baseurl+'/images/loading-small-small.gif">');
        $('#status').val( val ).trigger('change');

        //$('.canvas').collapse('toggle');

    }
    ajaxTableNew.draw();

    //notifyMe('success', window.data_filtered);

    setTimeout(function(){ 
        $('#status_'+val+'_loader_' + type).html('');

        $('.canvas').removeClass('collapse in');
        $('.canvas').addClass('collapse');
     }, 1000);

    $(this).show();
}
/*
$('.summarypaymentstatus').click(function() {
    var field = $(this).data('field');
    var val = $(this).data('status');
    var type = $(this).data('type');

    
    $(this).hide();

    if ( field == 'paymentstatus' ) {
        $('#paymentstatus_'+val+'_loader_' + type).html('<img src="'+baseurl+'/images/loading-small-small.gif">');
        $('#paymentstatus').val( val ).trigger('change');

        //$('.canvas').collapse('toggle');

    }
    ajaxTableNew.draw();

    //notifyMe('success', window.data_filtered);

    setTimeout(function(){ 
        $('#paymentstatus_'+val+'_loader_' + type).html('');

        $('.canvas').removeClass('collapse in');
        $('.canvas').addClass('collapse');
     }, 1000);

    $(this).show();
});
*/

$('#search-reset').click(function() {

    $('#date_filter').val('');
    $('#date_type').val('created_at');
    $('#paymentstatus').val('');
    $('#status_id_filter').val('');
    $('#customer').val('');
    $('#supplier').val('');
    $('#employee').val('');
    $('#invoice_no_filter').val('');
    $('#currency_id_filter').val('');
    $('#contact_type_id_filter').val('');
    $('#project_type_id_filter').val('');
    $('#country_id_filter').val('');
    $('#group_id_filter').val('');
    $('#priority').val('');
    $('#project_status_id_filter').val('');

    let start = moment().startOf('week');
    let end = moment().endOf('isoWeek');

    var date_format = $('#hdata').data('df');
    
    $('#date_filter').daterangepicker({
        "showDropdowns": true,
        "showWeekNumbers": true,
        "alwaysShowCalendars": true,
        autoUpdateInput: false,
        startDate: start,
        endDate: end,
        //minYear: 1901,
        locale: {
            format: date_format,
            firstDay: 1,
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            'All time': [moment().subtract(30, 'year').startOf('month'), moment().endOf('month')],
        }
    }, function( start_date, end_date ) {
        $('#date_filter').val( start_date.format(date_format)+' - '+end_date.format(date_format) );
    });

    $('.select2').trigger('change');
    // $('.select2').trigger('change');
    ajaxTableNew.draw();
});

/// Customize

// jQuery ".Class" SELECTOR.
$(document).ready(function() {
    $('.amount').keypress(function (event) {
        return isDecimalNumber(event, this)
    });

    $('.number').keypress(function (event) {
        return isNumber(event, this)
    });
});
// THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
function isDecimalNumber(evt, element) {

    var charCode = (evt.which) ? evt.which : event.keyCode

    if (
        (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
        (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
        (charCode < 48 || charCode > 57))
        return false;

    return true;
}

// THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
function isNumber(evt, element) {

    var charCode = (evt.which) ? evt.which : event.keyCode

    if (
        //(charCode != 45 || $(element).val().indexOf('-') != -1) &&      // â€œ-â€ CHECK MINUS, AND ONLY ONE.
        //(charCode != 46 || $(element).val().indexOf('.') != -1) &&      // â€œ.â€ CHECK DOT, AND ONLY ONE.
        (charCode < 48 || charCode > 57))
        return false;

    return true;
}

setTimeout(function () { 
    $('#productsrow_loader').hide();
    $('.productsrow').show(); 
}, 1000);

function isEmail(email) {
  var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return regex.test(email);
}

function phonenumber( value ) {
    var phoneno = /^\d{10,13}$/;
    return phoneno.test(value);
}