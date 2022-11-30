<script>
    
$(function() {
    function initializeincomecategorydonutChart() {
        Morris.Donut({
            element: 'incomecategorydonutChart',
            data: [
            @foreach( $yearly_data['income'] as $income_category => $income )
                {label: "{{$income_category}}", value: {{$income}}},
            @endforeach
            ],
            labelColor: '#23AE89',
            colors: ['#E67A77', '#D9DD81', '#79D1CF', '#95D7BB']
        });       
    }
    initializeincomecategorydonutChart();

    $(window).resize(function() {        
        setTimeout(function() {
            $("#incomecategorydonutChart").empty();
            initializeincomecategorydonutChart();
        }, 200);
    });
});
</script>