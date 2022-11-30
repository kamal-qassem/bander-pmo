
<div class="panel panel-default filters">
    <div class="panel-heading">
        <h3 class="panel-title">@lang('others.statistics.custom-filters')</h3>
    </div>
    <div class="panel-body">
        <form method="POST" id="search-form" class="form-inline" role="form">
        <div class="col-md-12">
                <div class="col-md-2">
            <div class="form-group filter-group frrmm">
                <label for="name">@lang('others.statistics.date')</label>
                <input type="text" class="form-control" name="date_filter" style="padding:2px;" id="date_filter" placeholder="@lang('others.statistics.select-date')" autocomplete="off">
            </div>
        </div>
                <div class="col-md-2">
            <div class="form-group filter-group">
                <label for="email">@lang('others.statistics.type')</label>
                <?php
                $date_types = array(
                   
                    'created_at' => trans('others.statistics.created'),
                    'invoice_date' => trans('global.invoices.fields.invoice-date'),
                    'invoice_due_date' => trans('global.invoices.fields.invoice-due-date'),
                );
                ?>
                {!! Form::select('date_type', $date_types, old('date_type'), ['class' => 'form-control select2',  'data-live-search' => 'true', 'data-show-subtext' => 'true', 'id' => 'date_type']) !!}     
            </div>
        </div>
                <div class="col-md-2">
            <div class="form-group filter-group">
                <label for="email">@lang('others.statistics.payment')</label>
                <?php
                $paymentstatuses = array(
                    '' => trans('others.statistics.all'),
                    'paid' => trans('others.statistics.paid'),
                    'unpaid' => trans('others.statistics.unpaid'),
                    'overdue' => trans('others.statistics.overdue'),
                    'cancelled' => trans('others.statistics.cancelled'),
                    'partial' => trans('others.statistics.partial'),
                );
                ?>
                {!! Form::select('paymentstatus', $paymentstatuses, old('paymentstatus'), ['class' => 'form-control select2',  'data-live-search' => 'true', 'data-show-subtext' => 'true', 'id' => 'paymentstatus']) !!}
            </div>
        </div>
                <div class="col-md-2">
            <div class="form-group filter-group">
                <label for="email">@lang('global.invoices.fields.currency')</label>
                <?php
                $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                ?>
                {!! Form::select('currency_id', $currencies, old('currency_id'), ['class' => 'form-control select2',  'data-live-search' => 'true', 'data-show-subtext' => 'true', 'id' => 'currency_id_filter']) !!}
            </div>
        </div>
                <div class="col-md-2">
            <div class="form-group filter-group">
                <label for="email">@lang('others.statistics.customer')</label>
               <?php   
                 $filtercustomers = \App\Contact::whereHas("contact_type",
                   function ($query) {
                       $query->where('id', CUSTOMERS_TYPE);
                   })->get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
                 ?>

                {!! Form::select('customer', $filtercustomers, old('customer'), ['class' => 'form-control select2',  'data-live-search' => 'true', 'data-show-subtext' => 'true', 'id' => 'customer']) !!}
            </div>
        </div>

        <div class="col-md-2" style="margin-top:35px;">
        <div class="form-group filter-group">
            <button type="submit" class="btn btn-primary">@lang('others.statistics.search')</button>&nbsp;
            <button type="reset" class="btn btn-danger" id="search-reset">@lang('others.statistics.reset')</button>
        </div>
      </div>
    </div>
        </form>
    </div>
</div>
@section('javascript')
@parent
<!-- Include Required Prerequisites -->
<script src="{{ url('js/cdn-js-files') }}/moment.min.js"></script>

<!-- Include Date Range Picker -->
<script src="{{ url('js/cdn-js-files/daterangepicker') }}/daterangepicker.js"></script>

<link href="{{ url('css/cdn-styles-css/daterangepicker/daterangepicker.css') }}" rel="stylesheet">

<script type="text/javascript">
    $(function () {
        let dateInterval = getQueryParameter('date_filter');
        let start = moment().startOf('year');
        let end = moment().endOf('isoWeek');
        if (dateInterval) {
            dateInterval = dateInterval.split(' - ');
            start = dateInterval[0];
            end = dateInterval[1];
        }
        //start = end = null;
        $('#date_filter').daterangepicker({
            "showDropdowns": true,
            "showWeekNumbers": true,
            "alwaysShowCalendars": true,
            autoUpdateInput: false,
            startDate: start,
            endDate: end,
            //minYear: 1901,
            locale: {
                format: '{{config('app.date_format_moment')}}',
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
            $('#date_filter').val( start_date.format('{{config('app.date_format_moment')}}')+' - '+end_date.format('{{config('app.date_format_moment')}}') );
        });
    });

    function getQueryParameter(name) {
        const url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
</script>
@stop