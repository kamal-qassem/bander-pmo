<h2 style="margin-top: 0;">{{ $reportTitle_expense }}</h2>

<canvas id="myChart_expense"></canvas>

<script>
    var ctx = document.getElementById("myChart_expense");
    var myChart = new Chart(ctx, {
        type: '{{ $chartType_expense }}',
        data: {
            labels: [
                @foreach ($results_expense as $group => $result)
                    "{{ $group }}",
                @endforeach
            ],

            datasets: [{
                label: '{{ $reportLabel_expense }}',
                data: [
                    @foreach ($results_expense as $group => $result)
                        {!! $result !!},
                    @endforeach
                ],
                borderWidth: 1
            }]
        },
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
    });
</script>