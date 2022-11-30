<h2 style="margin-top: 0;">{{ $reportTitle }}</h2>

<canvas id="myChart"></canvas>

<script>
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