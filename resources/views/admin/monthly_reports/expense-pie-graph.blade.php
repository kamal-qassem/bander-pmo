<h2 style="margin-top: 0;">{{ $reportTitle_expense_pie }}</h2>

<canvas id="myChart_expense_pie"></canvas>

<script>
    var ctx = document.getElementById("myChart_expense_pie");
    var myChart = new Chart(ctx, {
        type: '{{ $chartType_expense_pie }}',
        data: {
            labels: [
                @foreach ($results_expense_pie as $group => $result)
                    "{{ $group }}",
                @endforeach
            ],

            datasets: [{
                label: '{{ $reportLabel_expense_pie }}',
                data: [
                    @foreach ($results_expense_pie as $group => $result)
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