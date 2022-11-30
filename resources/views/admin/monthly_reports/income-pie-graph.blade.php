<h2 style="margin-top: 0;">{{ $reportTitle_income_pie }}</h2>

<canvas id="myChart_income_pie"></canvas>

<script>
    var ctx = document.getElementById("myChart_income_pie");
    var myChart = new Chart(ctx, {
        type: '{{ $chartType_income_pie }}',
        data: {
            labels: [
                @foreach ($results_income_pie as $group => $result)
                    "{{ $group }}",
                @endforeach
            ],

            datasets: [{
                label: '{{ $reportLabel_income_pie }}',
                data: [
                    @foreach ($results_income_pie as $group => $result)
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