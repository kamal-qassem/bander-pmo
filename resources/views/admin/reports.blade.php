@inject('request', 'Illuminate\Http\Request')

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-10">
            <h2 style="margin-top: 0;">{{ $reportTitle }}</h2>

            <form action="" method="get" id="reportform">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="date_filter" id="date_filter"/>
                </div>
                <div class="col-md-5">
                    <?php
                    $date_type = $request->input('date_type');
                    if ( empty( $date_type )) {
                        $date_type = 'created_at';
                    }
                    ?>

                    @if ( ! empty( $dateTypes ) )
                    <select name="date_type" class="form-control" id="date_type">
                        @foreach( $dateTypes as $key => $val )
                            <option value="{{$key}}" <?php if ( $key == $date_type ) echo ' selected' ?>>{{$val}}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
               
            </div>
            </form>
            <?php
            $controller = getController('controller');
            $action = getController('action');
            if ( 'ReportsController' === $controller && 'rolesUsersReport' === $action ) {
                $count = \App\Role::where('type', 'role')->whereBetween('created_at', [$date_from, $date_to])->get()->count();
                if ( $count == 0 ) {
                    echo  '<span style="color:red">'.trans("others.reports.users-count").'</span>';
                }
            }
            ?>

            <canvas id="myChart"></canvas>

            <script src="{{ url('js/cdn-js-files/chartjs250') }}/Chart.min.js"></script>
            
            <script>
                window.chartColors = {
                    red: 'rgb(255, 99, 132)',
                    orange: 'rgb(255, 159, 64)',
                    yellow: 'rgb(255, 205, 86)',
                    green: 'rgb(75, 192, 192)',
                    blue: 'rgb(54, 162, 235)',
                    purple: 'rgb(153, 102, 255)',
                    grey: 'rgb(201, 203, 207)'
                };

                var color = Chart.helpers.color;

                var ctx = document.getElementById("myChart");
                var myChart = new Chart(ctx, {
                    type: '{{ $chartType }}',
                    data: {
                        labels: [
                            @foreach ($results as $group => $result)
                                "{{ $group }}",
                            @endforeach
                        ],

                        datasets: [{
                            label: '{{ $reportLabel }}',
                            data: [
                                @foreach ($results as $group => $result)
                                    {!! clean($result) !!},
                                @endforeach
                            ],
                            @if ( ! empty( $colors ) )
                            backgroundColor: [
                                @foreach ($colors as $color)
                                "{{$color}}",
                                @endforeach
                            ],
                            @else
                            backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                            @endif
                            borderWidth: 1
                        }]
                    },
                    @if( ! in_array( $chartType, ['pie'] ) )
                    options: {
                        scales: {
                            xAxes: [],
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                    @endif
                });
            </script>
        </div>
    </div>
@stop

@section('javascript')
    <!-- Include Required Prerequisites -->
<script src="{{ url('js/cdn-js-files') }}/moment.min.js"></script>

<!-- Include Date Range Picker -->
<script src="{{ url('js/cdn-js-files/daterangepicker') }}/daterangepicker.js"></script>

<link href="{{ url('css/cdn-styles-css/daterangepicker/daterangepicker.css') }}" rel="stylesheet">


    <script type="text/javascript">
        $(function () {
            let dateInterval = getQueryParameter('date_filter');
            let start = moment().startOf('isoWeek');
            let end = moment().endOf('isoWeek');
            if (dateInterval) {
                dateInterval = dateInterval.split(' - ');
                start = dateInterval[0];
                end = dateInterval[1];
            }
            $('#date_filter').daterangepicker({
                "showDropdowns": true,
                "showWeekNumbers": true,
                "alwaysShowCalendars": true,
                startDate: start,
                endDate: end,
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
            });
        });

        $('#date_filter').on('apply.daterangepicker', function(ev, picker) {
            $('#reportform').submit();
        });

        $('#date_type').change(function() {
            $('#reportform').submit();
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
