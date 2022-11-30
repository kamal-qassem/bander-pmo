<script>
    
$(function() {
    function initializeInvoicesYearsDataAreaChart() {
        /**
        Orders years quarterly area chart
        */
        Morris.Area({
            element: 'InvoicesYearsDataAreaChart',
            behaveLikeLine: true,
            padding: 10,
            fillOpacity: .7,
            lineColors: ['#ED5D5D', '#D6D23A', '#E67A77', '#79D1CF'],
            gridEnabled: false,
            gridLineColor: '#dddddd',
            axes: true,
            data: [
            @foreach( $InvoicesYearsDataAreaChart as $year => $data )
            {
                y: '{{$year}}',
                @foreach( $data as $quarter => $quarter_data)
                {{$quarter}}: {{$quarter_data['amount']}},
                @endforeach
            }, 
            @endforeach            
            ],
            xkey: 'y',
            ykeys: [
            @foreach( $quarters as $q )
                '{{$q["title"]}}',
            @endforeach
            ],
            labels: [
            @foreach( $quarters as $q )
                '{{$q["quarter_months"]}}',
            @endforeach
            ],
            pointSize: 0,
            lineWidth: 0,
            hideHover: 'auto'
        });        
    }
    initializeInvoicesYearsDataAreaChart();

    $(window).resize(function() {        
        setTimeout(function() {
            $("#InvoicesYearsDataAreaChart").empty();
            initializeInvoicesYearsDataAreaChart();
        }, 200);
    });
});
</script>